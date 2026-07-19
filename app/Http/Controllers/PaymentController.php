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

        // Use the total_amount updated by the admin
        $amount = $order->total_amount ?? 0;
        
        // Ensure amount is greater than 0 to initiate payment
        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Payment amount is not set or invalid.');
        }
        
        $amountInPaise = (int)($amount * 100);

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
            'amount' => $amountInPaise
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
                $order->payment_status = 'completed';
                $order->save();
            }

            return redirect()->route('customer.orders')->with('success', 'Payment successful!');
        } catch (\Exception $e) {
            return redirect()->route('customer.orders')->with('error', 'Payment failed! ' . $e->getMessage());
        }
    }
}
