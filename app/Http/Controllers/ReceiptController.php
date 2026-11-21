<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SiteSetting;
use App\Services\OrderPaymentService;

/**
 * Printable invoice / receipt (use the browser's "Save as PDF"). Same content as the mobile app's PDF.
 * The customer sees their own orders only; the admin copy also shows the gateway fee and the net amount.
 */
class ReceiptController extends Controller
{
    public function customer($orderId)
    {
        $order = Order::with(['user', 'category', 'subcategory'])
            ->where('id', $orderId)
            ->where('user_uuid', auth()->user()->uuid)
            ->firstOrFail();

        return $this->render($order, false);
    }

    public function admin($id)
    {
        $order = Order::with(['user', 'category', 'subcategory'])->findOrFail($id);

        return $this->render($order, true);
    }

    private function render(Order $order, bool $admin)
    {
        $pay = OrderPaymentService::summary($order);
        $payments = $order->payments()->get()->sortBy('created_at')->values();

        $onlineGross = (float) $payments->where('status', 'paid')->where('method', 'razorpay')->sum('amount');
        $feePct = (float) SiteSetting::get('gateway_fee_percent', 2);

        return view('receipt.invoice', [
            'order' => $order,
            'pay' => $pay,
            'payments' => $payments,
            'items' => $order->items_with_images,
            'admin' => $admin,
            'invoiceNo' => 'SD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'advancePaid' => (float) $payments->where('kind', 'advance')->where('status', 'paid')->sum('amount'),
            'balancePaid' => (float) $payments->where('kind', '!=', 'advance')->where('status', 'paid')->sum('amount'),
            'pendingAmt' => (float) $payments->where('status', 'pending')->sum('amount'),
            'onlineGross' => $onlineGross,
            'fee' => OrderPaymentService::gatewayFee($onlineGross),
            'feePct' => $feePct,
        ]);
    }
}
