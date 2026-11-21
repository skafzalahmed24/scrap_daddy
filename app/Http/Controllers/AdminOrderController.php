<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;

/** Admin order detail page and payment actions (record cash/UPI/bank received, approve/reject submitted payments). */
class AdminOrderController extends Controller
{
    public function show($id)
    {
        $order = Order::with(['user', 'category', 'subcategory'])->findOrFail($id);

        $payments = $order->payments()->get();

        // Money received, per payment. Older orders (paid before payments were itemised) get one line from the order itself.
        $received = $payments->where('status', 'paid')->map(fn ($p) => [
            'method' => $p->method, 'label' => $p->method_label, 'kind' => $p->kind, 'amount' => (float) $p->amount,
            'reference' => $p->reference, 'at' => $p->reviewed_at ?? $p->created_at,
        ])->values();
        if ($received->isEmpty() && $order->payment_status === 'completed') {
            $method = $order->payment_method ?: (str_starts_with((string) $order->payment_id, 'pay_') ? 'razorpay' : null);
            $received = collect([[
                'method' => $method, 'label' => $method ? (\App\Models\OrderPayment::METHODS[$method] ?? ucfirst($method)) : 'Payment',
                'kind' => 'full', 'amount' => OrderPaymentService::paidTotal($order), 'reference' => $order->payment_id, 'at' => $order->updated_at,
            ]]);
        }

        $onlineGross = (float) $received->where('method', 'razorpay')->sum('amount');
        $fee = OrderPaymentService::gatewayFee($onlineGross);

        return view('admin.orders.show', [
            'order' => $order,
            'pay' => OrderPaymentService::summary($order),
            'payments' => $payments,
            'received' => $received,
            'onlineGross' => $onlineGross,
            'fee' => $fee,
            'feePct' => (float) \App\Models\SiteSetting::get('gateway_fee_percent', 2),
        ]);
    }

    /** Admin received money directly (cash, UPI, bank) - counts immediately. */
    public function recordPayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $remaining = OrderPaymentService::remaining($order);

        $data = $request->validate([
            'method' => 'required|in:cash,upi,bank',
            'amount' => 'required|numeric|min:1|max:500000',
            'reference' => 'nullable|string|max:60',
            'note' => 'nullable|string|max:200',
        ]);

        if ($order->status === 'cancelled') {
            return back()->with('error', 'This order is cancelled.');
        }
        if ((float) $order->total_amount <= 0) {
            return back()->with('error', 'Set the order amount before recording a payment.');
        }
        if ((float) $data['amount'] > $remaining + 0.009) {
            return back()->with('error', 'Amount is more than the balance due (₹' . number_format($remaining, 2) . ').');
        }

        $kind = OrderPaymentService::dueNow($order)['kind'];
        OrderPaymentService::recordPayment($order, $kind === 'none' ? 'full' : $kind, (float) $data['amount'], $data['method'], $data['reference'] ?? null, 'paid', null, $data['note'] ?? 'Recorded by admin');

        return back()->with('success', 'Payment recorded.');
    }

    public function reviewPayment(Request $request, $id)
    {
        $data = $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:200',
        ]);

        $payment = OrderPayment::findOrFail($id);
        if ($payment->status !== 'pending' || $payment->method === 'razorpay') {
            return back()->with('error', 'This payment cannot be reviewed.');
        }

        $data['action'] === 'approve'
            ? OrderPaymentService::approve($payment, $data['note'] ?? null)
            : OrderPaymentService::reject($payment, $data['note'] ?? 'Not received');

        return back()->with('success', $data['action'] === 'approve' ? 'Payment approved.' : 'Payment rejected.');
    }
}
