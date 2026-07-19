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
        'pickup_location',
        'pickup_date',
        'pickup_time',
        'images',
        'notes',
    ];

    protected $casts = [
        'images' => 'array',
        'pickup_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
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
