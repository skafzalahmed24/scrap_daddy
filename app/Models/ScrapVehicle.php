<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_uuid',
        'vehicle_type',
        'vehicle_number',
        'vehicle_brand',
        'vehicle_model',
        'photos',
        'remark',
        'status',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
