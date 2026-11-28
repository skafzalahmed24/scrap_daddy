<?php

namespace App\Services;

/** Builds the one-line pickup address everybody sees (admin, receipts, WhatsApp) from the separate form fields. */
class PickupAddress
{
    public static function compose(?string $address, ?string $door = null, ?string $floor = null, ?string $landmark = null): string
    {
        $parts = [];
        if (filled($door)) {
            $parts[] = 'Door/Flat ' . trim($door);
        }
        if (filled($floor)) {
            $parts[] = trim($floor) . (preg_match('/floor/i', $floor) ? '' : ' floor');
        }
        $parts[] = trim((string) $address);
        $line = implode(', ', array_filter($parts));

        return filled($landmark) ? $line . ' (Landmark: ' . trim($landmark) . ')' : $line;
    }
}
