<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'profile_image',
        'email',
        'phone_number',
        'password',
        'pin_code',
        'location',
        'device_id',
        'device_unique_id',
        'device_details',
        'platform_type',
        'latitude',
        'longitude',
        'otp',
        'is_verified',
        'otp_expires_at',
        'status',
        'reward_coins',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_uuid', 'uuid');
    }

    public function scrapVehicles()
    {
        return $this->hasMany(ScrapVehicle::class, 'user_uuid', 'uuid');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'user_uuid', 'uuid');
    }

    /**
     * Get the dynamically calculated available reward coins for the user.
     * It sums the available_coins of all unexpired orders.
     */
    public function getAvailableRewardCoins()
    {
        return \App\Models\Order::where('user_uuid', $this->uuid)
            ->where('available_coins', '>', 0)
            ->where(function($q) {
                $q->whereNull('coins_expires_at')
                  ->orWhere('coins_expires_at', '>', now());
            })->sum('available_coins');
    }
}
