<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One step of "How it works". Icons are limited to this list so the website and the app can both draw them. */
class HowStep extends Model
{
    public const ICONS = [
        'sack-dollar' => 'Money bag',
        'calendar-check' => 'Calendar',
        'scale-balanced' => 'Weighing scale',
        'money-bill-transfer' => 'Payment',
        'truck-fast' => 'Fast truck',
        'truck' => 'Truck',
        'location-dot' => 'Location',
        'coins' => 'Coins',
        'recycle' => 'Recycle',
        'box-open' => 'Box',
        'mobile-screen' => 'Phone',
        'handshake' => 'Handshake',
    ];

    protected $fillable = ['title', 'description', 'icon', 'color', 'is_active', 'sort'];

    protected $casts = ['is_active' => 'boolean'];

    public static function active()
    {
        return static::where('is_active', true)->orderBy('sort')->orderBy('id')->get();
    }
}
