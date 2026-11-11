<?php

namespace App\Services;

use App\Models\Order;
use App\Models\RewardConfiguration;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;

/**
 * Single place for payment/reward rules so the website and the mobile API
 * behave identically.
 */
class OrderPaymentService
{
    /** Amount (in rupees) the customer still has to pay for the order. */
    public static function payableAmount(Order $order): float
    {
        $amount = (float) ($order->total_amount ?? 0) - (float) ($order->discount_applied ?? 0);

        return max(0, round($amount, 2));
    }

    public static function razorpay(): Api
    {
        return new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    /**
     * Confirm with Razorpay that the payment really exists, belongs to this
     * order's amount and is paid. Returns null when OK, otherwise an error message.
     */
    public static function verifyRazorpayPayment(
        Order $order,
        string $paymentId,
        ?string $razorpayOrderId = null,
        ?string $signature = null
    ): ?string {
        try {
            $api = self::razorpay();

            if ($razorpayOrderId && $signature) {
                $api->utility->verifyPaymentSignature([
                    'razorpay_order_id' => $razorpayOrderId,
                    'razorpay_payment_id' => $paymentId,
                    'razorpay_signature' => $signature,
                ]);
            }

            $payment = $api->payment->fetch($paymentId);

            if ($payment['status'] === 'authorized') {
                $payment = $payment->capture([
                    'amount' => $payment['amount'],
                    'currency' => $payment['currency'],
                ]);
            }

            if ($payment['status'] !== 'captured') {
                return 'Payment has not been completed.';
            }

            $expectedPaise = (int) round(self::payableAmount($order) * 100);
            if ((int) $payment['amount'] !== $expectedPaise) {
                return 'Payment amount does not match the order amount.';
            }

            if ($razorpayOrderId && !empty($payment['order_id']) && $payment['order_id'] !== $razorpayOrderId) {
                return 'Payment does not belong to this order.';
            }
        } catch (\Throwable $e) {
            report($e);

            return 'Unable to verify payment.';
        }

        return null;
    }

    /**
     * Mark an order as paid and credit the earned reward coins exactly once.
     */
    public static function markPaid(Order $order, ?string $paymentId = null): Order
    {
        return DB::transaction(function () use ($order, $paymentId) {
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            if ($order->payment_status === 'completed') {
                return $order;
            }

            $order->payment_status = 'completed';
            if ($paymentId) {
                $order->payment_id = $paymentId;
            }

            $earned = (int) ($order->coins_earned ?? 0);
            if ($earned <= 0) {
                $config = self::rewardConfigFor($order);
                $earned = $config ? (int) $config->reward_coins : 0;
                $order->coins_earned = $earned;
            }

            if ($earned > 0) {
                $order->available_coins = $earned;
                $config = self::rewardConfigFor($order);
                if ($config && $config->validity_days) {
                    $order->coins_expires_at = now()->addDays($config->validity_days);
                }

                $user = User::where('uuid', $order->user_uuid)->lockForUpdate()->first();
                if ($user) {
                    $user->reward_coins = (int) ($user->reward_coins ?? 0) + $earned;
                    $user->save();
                }
            }

            $order->save();

            return $order;
        });
    }

    public static function rewardConfigFor(Order $order): ?RewardConfiguration
    {
        $amount = (float) ($order->total_amount ?? 0);

        return RewardConfiguration::where('status', 1)
            ->where('min_amount', '<=', $amount)
            ->where('max_amount', '>=', $amount)
            ->first();
    }

    public static function coinValue(): float
    {
        $setting = \App\Models\RewardSetting::first();

        return $setting ? (float) $setting->coin_value_in_rupees : 0.10;
    }

    /**
     * Spend up to $coins of the user's coins on the order (FIFO by expiry).
     * Discount can never exceed the order total. Returns [coinsUsed, discount].
     */
    public static function redeemCoins(User $user, Order $order, ?int $requestedCoins = null): array
    {
        return DB::transaction(function () use ($user, $order, $requestedCoins) {
            $user = User::where('uuid', $user->uuid)->lockForUpdate()->first();
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            $coinValue = self::coinValue();
            $remainingValue = max(0, (float) $order->total_amount - (float) $order->discount_applied);
            $maxCoinsForOrder = $coinValue > 0 ? (int) floor($remainingValue / $coinValue) : 0;

            $balance = max((int) ($user->reward_coins ?? 0), 0);
            $coins = min($requestedCoins ?? $balance, $balance, $maxCoinsForOrder);

            if ($coins <= 0) {
                return [0, 0.0];
            }

            $discount = round($coins * $coinValue, 2);

            $order->coins_redeemed = (int) $order->coins_redeemed + $coins;
            $order->discount_applied = round((float) $order->discount_applied + $discount, 2);
            $order->save();

            $remaining = $coins;
            $ledger = Order::where('user_uuid', $user->uuid)
                ->where('available_coins', '>', 0)
                ->where(function ($q) {
                    $q->whereNull('coins_expires_at')->orWhere('coins_expires_at', '>', now());
                })
                ->orderByRaw('coins_expires_at IS NULL, coins_expires_at ASC')
                ->lockForUpdate()
                ->get();

            foreach ($ledger as $row) {
                if ($remaining <= 0) {
                    break;
                }
                $take = min($row->available_coins, $remaining);
                $row->available_coins -= $take;
                $row->save();
                $remaining -= $take;
            }

            $user->reward_coins = $balance - $coins;
            $user->save();

            return [$coins, $discount];
        });
    }
}
