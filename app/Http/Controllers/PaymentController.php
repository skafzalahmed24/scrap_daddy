<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Order;

class PaymentController extends Controller
{
    public function initiatePayment(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        // Use the total_amount updated by the admin and apply any discount
        $amount = $order->total_amount ?? 0;
        
        if ($order->discount_applied > 0) {
            $amount -= $order->discount_applied;
            if ($amount < 0) $amount = 0;
        }
        
        // If amount is 0 due to rewards, auto-complete the payment
        if ($amount == 0 && $order->discount_applied > 0) {
            if ($order->payment_status !== 'completed') {
                $order->payment_status = 'completed';
                $order->save();
                
                // Credit earned coins to user and set expiration
                if ($order->coins_earned > 0) {
                    $order->available_coins = $order->coins_earned;
                    
                    // Fetch validity days
                    $rewardConfig = \App\Models\RewardConfiguration::where('status', 1)
                        ->where('min_amount', '<=', $order->total_amount)
                        ->where('max_amount', '>=', $order->total_amount)
                        ->first();
                        
                    if ($rewardConfig && $rewardConfig->validity_days) {
                        $order->coins_expires_at = now()->addDays($rewardConfig->validity_days);
                    }
                    $order->save();
                    
                    $user = \App\Models\User::where('uuid', $order->user_uuid)->first();
                    if ($user) {
                        $user->reward_coins += $order->coins_earned; // Legacy column, optional
                        $user->save();
                    }
                }
            }
            return redirect()->route('customer.orders')->with('success', 'Payment covered by rewards successfully!');
        }

        // Ensure amount is greater than 0 to initiate payment
        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Payment amount is not set or invalid.');
        }
        
        $amountInPaise = (int)($amount * 100);

        $user = \App\Models\User::where('uuid', $order->user_uuid)->first();
        $setting = \App\Models\RewardSetting::first();
        $coinValue = $setting ? $setting->coin_value_in_rupees : 0.10;

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $razorpayOrder = $api->order->create([
            'receipt'         => 'order_rcptid_' . $order->id,
            'amount'          => $amountInPaise,
            'currency'        => 'INR',
            'payment_capture' => 1 // auto capture
        ]);

        $order->payment_id = $razorpayOrder['id'];
        $order->save();

        return view('customer.payment', [
            'order' => $order,
            'razorpayOrder' => $razorpayOrder,
            'amount' => $amountInPaise,
            'user' => $user,
            'coinValue' => $coinValue
        ]);
    }

    public function paymentCallback(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $attributes = [
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ];

        try {
            $api->utility->verifyPaymentSignature($attributes);
            
            $order = Order::where('payment_id', $request->razorpay_order_id)->first();
            if ($order) {
                if ($order->payment_status !== 'completed') {
                    $order->payment_status = 'completed';
                    $order->save();
                    
                    // Credit earned coins to user and set expiration
                    if ($order->coins_earned > 0) {
                        $order->available_coins = $order->coins_earned;
                        
                        // Fetch validity days
                        $rewardConfig = \App\Models\RewardConfiguration::where('status', 1)
                            ->where('min_amount', '<=', $order->total_amount)
                            ->where('max_amount', '>=', $order->total_amount)
                            ->first();
                            
                        if ($rewardConfig && $rewardConfig->validity_days) {
                            $order->coins_expires_at = now()->addDays($rewardConfig->validity_days);
                        }
                        $order->save();
                        
                        $user = \App\Models\User::where('uuid', $order->user_uuid)->first();
                        if ($user) {
                            $user->reward_coins += $order->coins_earned; // Legacy column, optional
                            $user->save();
                        }
                    }
                }
            }

            return redirect()->route('customer.orders')->with('success', 'Payment successful!');
        } catch (\Exception $e) {
            return redirect()->route('customer.orders')->with('error', 'Payment failed! ' . $e->getMessage());
        }
    }

    public function applyRewards(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Since we are using token/localStorage on frontend, we might need to get user from token?
        // Wait, the routes are protected by web guard: `Route::middleware(['auth'])->group(...)`
        // But earlier we established auth()->user() is sometimes null if relying on localstorage.
        // Let's rely on the order's user_uuid
        $user = \App\Models\User::where('uuid', $order->user_uuid)->first();

        $availableBalance = $user->getAvailableRewardCoins();

        if (!$user || $availableBalance <= 0) {
            return redirect()->back()->with('error', 'No rewards available to apply.');
        }

        if ($order->discount_applied > 0) {
            return redirect()->back()->with('error', 'Rewards already applied to this order.');
        }
        
        // Prevent applying if order is already completed
        if ($order->payment_status === 'completed') {
            return redirect()->back()->with('error', 'Payment is already completed.');
        }

        $setting = \App\Models\RewardSetting::first();
        $coinValue = $setting ? $setting->coin_value_in_rupees : 0.10;

        $coinsToRedeem = $availableBalance;
        $discountValue = $coinsToRedeem * $coinValue;

        // Apply discount
        $order->coins_redeemed = $coinsToRedeem;
        $order->discount_applied = $discountValue;
        $order->save();

        // FIFO Ledger Deduction
        $unexpiredOrders = \App\Models\Order::where('user_uuid', $user->uuid)
            ->where('available_coins', '>', 0)
            ->where(function($q) {
                $q->whereNull('coins_expires_at')
                  ->orWhere('coins_expires_at', '>', now());
            })
            // Sort by nulls last, then by expires_at ASC
            ->orderByRaw('-coins_expires_at DESC')
            ->get();
            
        $remainingToDeduct = $coinsToRedeem;
        foreach ($unexpiredOrders as $uOrder) {
            if ($remainingToDeduct <= 0) break;
            
            if ($uOrder->available_coins <= $remainingToDeduct) {
                $remainingToDeduct -= $uOrder->available_coins;
                $uOrder->available_coins = 0;
            } else {
                $uOrder->available_coins -= $remainingToDeduct;
                $remainingToDeduct = 0;
            }
            $uOrder->save();
        }

        // Deduct from legacy user column as fallback
        $user->reward_coins = max(0, $user->reward_coins - $coinsToRedeem);
        $user->save();

        return redirect()->back()->with('success', 'Rewards applied successfully! Discount of ₹' . $discountValue . ' added.');
    }
}
