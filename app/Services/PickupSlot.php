<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Pickup time-slot rules: 1-hour windows ("9:00 AM - 10:00 AM") between 9 AM and 9 PM, never in the past.
 * The business runs on Indian time, whatever the app timezone is.
 *
 * Older app builds still send the retired 30-minute format ("09:00 - 09:30"); those are let through
 * untouched so existing installs keep working until they update.
 */
class PickupSlot
{
    public const TZ = 'Asia/Kolkata';
    public const FIRST_HOUR = 9;
    public const LAST_HOUR = 21;
    private const FORMAT = '/^\s*(\d{1,2}):00\s*(AM|PM)\s*-\s*(\d{1,2}):00\s*(AM|PM)\s*$/i';

    /** @return string|null error message, or null when the slot is acceptable */
    public static function error(string $date, string $time): ?string
    {
        if (!preg_match(self::FORMAT, $time, $m)) {
            // A range with AM/PM that is not on whole hours (e.g. 9:00 AM - 9:30 AM) is the retired format: refuse.
            // Anything else (old app builds sending "09:00 - 09:30") is let through.
            return (preg_match('/AM|PM/i', $time) && str_contains($time, '-'))
                ? 'Please choose a 1-hour pickup slot between 9:00 AM and 9:00 PM.'
                : null;
        }

        $start = self::to24($m[1], $m[2]);
        $end = self::to24($m[3], $m[4]);

        if ($end - $start !== 1 || $start < self::FIRST_HOUR || $end > self::LAST_HOUR) {
            return 'Please choose a 1-hour pickup slot between 9:00 AM and 9:00 PM.';
        }

        $now = Carbon::now(self::TZ);
        $slotStart = Carbon::parse($date, self::TZ)->startOfDay()->addHours($start);
        if ($slotStart->lte($now)) {
            return 'That pickup slot has already started. Please choose a later time slot.';
        }

        return null;
    }

    private static function to24(string $hour, string $ampm): int
    {
        $h = ((int) $hour) % 12;
        return strtoupper($ampm) === 'PM' ? $h + 12 : $h;
    }
}
