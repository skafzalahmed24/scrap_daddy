<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePincode extends Model
{
    protected $fillable = ['pin_code', 'area', 'city', 'status'];

    protected $casts = ['status' => 'boolean'];

    public const FORMAT = '/^[1-9][0-9]{5}$/';

    /**
     * A PIN code is serviceable when the admin has it enabled.
     * Until the admin enables at least one PIN code, service is open everywhere
     * (so a fresh install is not blocked).
     */
    public static function isServiceable(?string $pin): bool
    {
        if (!static::restrictionActive()) {
            return true;
        }

        return $pin !== null && static::where('pin_code', $pin)->where('status', true)->exists();
    }

    public static function restrictionActive(): bool
    {
        return static::where('status', true)->exists();
    }

    public static function unavailableMessage(?string $pin): string
    {
        return $pin
            ? "Sorry, we do not provide service in PIN code {$pin} yet."
            : 'Please enter a valid 6-digit PIN code for the pickup address.';
    }
}
