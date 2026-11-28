<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_uuid',
        'category_uuid',
        'subcategory_uuid',
        'status',
        'estimated_pickup_date',
        'total_amount',
        'payment_status',
        'payment_id',
        'coins_earned',
        'coins_redeemed',
        'discount_applied',
        'paid_amount',
        'advance_amount',
        'advance_percent',
        'payment_method',
        'pickup_location',
        'pin_code',
        'pickup_date',
        'pickup_time',
        'door_no',
        'floor',
        'landmark',
        'images',
        'notes',
        'items',
    ];

    protected $casts = [
        'images' => 'array',
        'pickup_date' => 'date',
        'items' => 'array',
    ];

    /** Same title / picture on the website and in the mobile app (API sends these too). */
    protected $appends = ['display_title', 'display_image', 'items_with_images', 'payment_info'];

    /** Small payment summary for cards in the app and website (status chip + what is due). */
    /**
     * Older orders stored slots without AM/PM ("09:00 - 09:30", "03:30 - 04:00").
     * Pickups run 9 AM - 9 PM, so 9-11 are AM and 12-8 are PM; add the suffix when reading.
     * Values that already carry AM/PM (new orders) are returned untouched.
     */
    public function getPickupTimeAttribute($value)
    {
        if (!is_string($value) || preg_match('/AM|PM/i', $value)) {
            return $value;
        }
        if (!preg_match('/^\s*(\d{1,2}):(\d{2})\s*-\s*(\d{1,2}):(\d{2})\s*$/', $value, $m)) {
            return $value;
        }
        $fmt = fn ($h, $min) => sprintf('%02d:%s %s', $h, $min, ($h >= 9 && $h <= 11) ? 'AM' : 'PM');

        return $fmt((int) $m[1], $m[2]) . ' - ' . $fmt((int) $m[3], $m[4]);
    }

    public function getPaymentInfoAttribute(): array
    {
        $verifying = $this->exists && $this->payments()->where('status', 'pending')->where('method', '!=', 'razorpay')->exists();
        $key = \App\Services\OrderPaymentService::statusKey($this, $verifying);
        $due = \App\Services\OrderPaymentService::dueNow($this);

        return [
            'status' => $key,
            'label' => \App\Services\OrderPaymentService::statusLabel($key),
            'paid' => (float) $this->paid_amount,
            'order_amount' => (float) $this->total_amount,
            'discount' => (float) ($this->discount_applied ?? 0),
            'coins_redeemed' => (int) ($this->coins_redeemed ?? 0),
            'remaining' => $due['remaining'],
            'due_now' => $due['amount'],
            'due_kind' => $due['kind'],
            'can_pay' => $due['amount'] > 0 && !$verifying,
        ];
    }

    public function getDisplayTitleAttribute(): string
    {
        $first = is_array($this->items) ? ($this->items[0] ?? null) : null;

        return $this->category->title ?? $this->subcategory->name ?? ($first['name'] ?? 'Scrap pickup');
    }

    public function getDisplayImageAttribute(): ?string
    {
        if ($this->subcategory && $this->subcategory->image) {
            return $this->subcategory->image;
        }
        foreach ($this->items_with_images as $item) {
            if (!empty($item['image'])) {
                return $item['image'];
            }
        }

        return $this->category->image ?? null;
    }

    /** Each picked item with its subcategory picture (name + quantity are stored on the order). */
    public function getItemsWithImagesAttribute(): array
    {
        $items = is_array($this->items) ? $this->items : [];
        $uuids = collect($items)->pluck('subcategory_uuid')->filter()->unique()->all();
        $images = $uuids ? Subcategory::whereIn('uuid', $uuids)->pluck('image', 'uuid') : collect();

        return array_map(function ($i) use ($images) {
            $i['image'] = $images[$i['subcategory_uuid'] ?? ''] ?? null;

            return $i;
        }, $items);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class)->latest();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_uuid', 'uuid');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_uuid', 'uuid');
    }
}
