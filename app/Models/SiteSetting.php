<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value site settings (contact details, address, social links) editable from the admin panel.
 * Anything not saved in the database falls back to the defaults below.
 */
class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    private const CACHE_KEY = 'site_settings_v1';

    private static ?array $memo = null;

    public static function defaults(): array
    {
        return [
            'company_name' => 'Scrap Daddy Trading Co. Ltd.',
            'footer_about' => "Bengaluru's trusted scrap pickup service. We collect, weigh and pay you instantly at your doorstep.",
            'address_line1' => 'No 42, 1st Main Road, Tech Park Layout',
            'address_line2' => 'Koramangala',
            'city' => 'Bengaluru',
            'state' => 'Karnataka',
            'country' => 'India',
            'pincode' => '560034',
            'email' => 'support@scrapdaddy.com',
            'phone' => '+91 80 4123 5678',
            'toll_free' => '1800-SCRAP-NOW',
            'whatsapp' => '',
            // ---- payments (Admin > Payment Settings)
            'advance_enabled' => '0',
            'advance_percent' => '20',
            'pay_razorpay' => '1',
            'gateway_fee_percent' => '2',
            'pay_upi' => '0',
            'upi_id' => '',
            'upi_name' => '',
            'pay_bank' => '0',
            'bank_holder' => '',
            'bank_name' => '',
            'bank_account' => '',
            'bank_ifsc' => '',
            'pay_cash' => '0',
            'pay_instructions' => '',
            'referral_bonus_coins' => '50',
            'referral_friend_coins' => '20',
            'instagram' => '',
            'facebook' => '',
            'youtube' => '',
            'twitter' => '',
            'linkedin' => '',
        ];
    }

    /** All settings (saved values over defaults). Never throws, even before the table exists. */
    public static function values(): array
    {
        if (self::$memo !== null) {
            return self::$memo;
        }

        $defaults = self::defaults();

        try {
            $saved = Cache::remember(self::CACHE_KEY, 3600, fn () => self::query()->pluck('value', 'key')->all());
        } catch (\Throwable $e) {
            $saved = [];
        }

        $merged = $defaults;
        foreach ($saved as $key => $value) {
            if (array_key_exists($key, $defaults)) {
                $merged[$key] = (string) $value;
            }
        }

        return self::$memo = $merged;
    }

    public static function get(string $key, $default = null)
    {
        return self::values()[$key] ?? $default;
    }

    public static function flush(): void
    {
        self::$memo = null;
        try {
            Cache::forget(self::CACHE_KEY);
        } catch (\Throwable $e) {
            // ignore
        }
    }
}
