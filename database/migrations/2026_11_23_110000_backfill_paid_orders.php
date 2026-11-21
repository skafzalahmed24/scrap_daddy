<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Orders paid before payments were itemised have paid_amount = 0.
     * Mark them as fully received and, when the payment id is a Razorpay one, as paid via Razorpay.
     */
    public function up(): void
    {
        Order::where('payment_status', 'completed')->where('paid_amount', 0)->chunkById(200, function ($orders) {
            foreach ($orders as $order) {
                $payable = max(0, (float) $order->total_amount - (float) ($order->discount_applied ?? 0));
                $order->paid_amount = $payable;
                if (!$order->payment_method && str_starts_with((string) $order->payment_id, 'pay_')) {
                    $order->payment_method = 'razorpay';
                }
                $order->save();
            }
        });
    }

    public function down(): void
    {
        // data fix only
    }
};
