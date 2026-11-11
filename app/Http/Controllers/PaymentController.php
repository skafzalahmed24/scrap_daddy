<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private function ownedOrder($orderId): Order
    {
        return Order::where('id', $orderId)
            ->where('user_uuid', auth()->user()->uuid)
            ->firstOrFail();
    }

    public function initiatePayment(Request $request, $orderId)
    {
        $order = $this->ownedOrder($orderId);

        if ($order->payment_status === 'completed') {
            return redirect()->route('customer.orders')->with('success', 'This order is already paid.');
        }

        if ((float) ($order->total_amount ?? 0) <= 0) {
            return redirect()->route('customer.orders')->with('error', 'Payment is not available for this order yet.');
        }

        $amount = OrderPaymentService::payableAmount($order);

        // Fully covered by reward coins - no gateway needed
        if ($amount <= 0 && $order->discount_applied > 0) {
            OrderPaymentService::markPaid($order);

            return redirect()->route('customer.orders')->with('success', 'Payment covered by rewards successfully!');
        }

        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Payment amount is not set or invalid.');
        }

        $amountInPaise = (int) round($amount * 100);
        $user = \App\Models\User::where('uuid', $order->user_uuid)->first();

        try {
            $razorpayOrder = OrderPaymentService::razorpay()->order->create([
                'receipt' => 'order_rcptid_' . $order->id,
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'payment_capture' => 1,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('customer.orders')->with('error', 'Unable to start the payment. Please try again later.');
        }

        // Remember the gateway order id so the callback can be matched to this order
        $order->payment_id = $razorpayOrder['id'];
        $order->save();

        return view('customer.payment', [
            'order' => $order,
            'razorpayOrder' => $razorpayOrder,
            'amount' => $amountInPaise,
            'user' => $user,
            'coinValue' => OrderPaymentService::coinValue(),
        ]);
    }

    public function paymentCallback(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $order = Order::where('payment_id', $request->razorpay_order_id)
            ->where('user_uuid', auth()->user()->uuid)
            ->first();

        if (!$order) {
            return redirect()->route('customer.orders')->with('error', 'Payment failed! Order not found.');
        }

        $error = OrderPaymentService::verifyRazorpayPayment(
            $order,
            $request->razorpay_payment_id,
            $request->razorpay_order_id,
            $request->razorpay_signature
        );

        if ($error) {
            return redirect()->route('customer.orders')->with('error', 'Payment failed! ' . $error);
        }

        OrderPaymentService::markPaid($order, $request->razorpay_payment_id);

        return redirect()->route('customer.orders')->with('success', 'Payment successful!');
    }

    public function applyRewards(Request $request, $orderId)
    {
        $order = $this->ownedOrder($orderId);
        $user = auth()->user();

        if ($order->payment_status === 'completed') {
            return redirect()->back()->with('error', 'Payment is already completed.');
        }

        if ((float) ($order->total_amount ?? 0) <= 0) {
            return redirect()->back()->with('error', 'Order amount is not set yet.');
        }

        if ($order->discount_applied > 0) {
            return redirect()->back()->with('error', 'Rewards already applied to this order.');
        }

        if (($user->reward_coins ?? 0) <= 0 && $user->getAvailableRewardCoins() <= 0) {
            return redirect()->back()->with('error', 'No rewards available to apply.');
        }

        [$used, $discount] = OrderPaymentService::redeemCoins($user, $order);

        if ($used <= 0) {
            return redirect()->back()->with('error', 'No rewards available to apply.');
        }

        return redirect()->back()->with('success', 'Rewards applied successfully! Discount of ₹' . number_format($discount, 2) . ' added.');
    }
}
