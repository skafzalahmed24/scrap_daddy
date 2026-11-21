<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One payment attempt/receipt for an order (advance, balance or full; online, UPI, bank or cash). */
class OrderPayment extends Model
{
    public const METHODS = [
        'razorpay' => 'Online (Card / UPI / Netbanking)',
        'upi' => 'UPI',
        'bank' => 'Bank transfer',
        'cash' => 'Cash',
    ];

    protected $fillable = [
        'order_id', 'user_uuid', 'kind', 'amount', 'method', 'reference', 'gateway_order_id', 'status', 'note', 'reviewed_at',
    ];

    protected $casts = ['reviewed_at' => 'datetime', 'amount' => 'float'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getMethodLabelAttribute(): string
    {
        return self::METHODS[$this->method] ?? ucfirst($this->method);
    }
}
