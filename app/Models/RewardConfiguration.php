<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_amount',
        'max_amount',
        'reward_coins',
        'status',
        'validity_days',
    ];
}
