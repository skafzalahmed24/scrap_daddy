<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;

/** Website payment pages. The money logic lives in OrderPaymentService (shared with the mobile API). */
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
            return redirect()->route('customer.payment.status', $order->id);
        }

        if ((float) ($order->total_amount ?? 0) <= 0) {
            return redirect()->route('customer.orders')->with('error', 'Payment is not available for this order yet. We will tell you when the amount is ready.');
        }

        // Fully covered by reward coins - no payment needed
        if (OrderPaymentService::payableAmount($order) <= 0 && $order->discount_applied > 0) {
            OrderPaymentService::markPaid($order);

            return redirect()->route('customer.payment.status', $order->id);
        }

        return view('customer.payment', [
            'order' => $order,
            'pay' => OrderPaymentService::summary($order),
            'user' => auth()->user(),
            'coinValue' => OrderPaymentService::coinValue(),
            'razorpayKey' => config('services.razorpay.key'),
        ]);
    }

    /** Razorpay checkout finished in the browser: verify with the gateway, record, show the receipt. */
    public function paymentCallback(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $order = $this->ownedOrder($request->order_id);

        $error = OrderPaymentService::verifyAndRecordRazorpay(
            $order, $request->razorpay_payment_id, $request->razorpay_order_id, $request->razorpay_signature
        );

        if ($error) {
            return redirect()->route('customer.payment.initiate', $order->id)->with('error', 'Payment failed! ' . $error);
        }

        return redirect()->route('customer.payment.status', $order->id);
    }

    /** Receipt / progress page: paid, advance received, or "request raised - verifying". */
    public function status($orderId)
    {
        $order = $this->ownedOrder($orderId);

        return view('customer.payment-status', [
            'order' => $order,
            'pay' => OrderPaymentService::summary($order),
        ]);
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

        if ((float) $order->paid_amount > 0) {
            return redirect()->back()->with('error', 'Rewards must be applied before the first payment.');
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
