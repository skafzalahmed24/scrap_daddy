<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Sends plain-text WhatsApp messages through the ChatHub public API
 * (POST /whatsapp/send_text on https://consolewebwhatsapp.digitalmbg.com/api).
 *
 * Rules this class follows:
 *  - a WhatsApp problem must never break booking, login or an admin action: every call is wrapped,
 *    has a short timeout and only logs on failure;
 *  - nothing is sent unless WHATSAPP_ENABLED=true and an API key is set;
 *  - WhatsApp delivers free text to people who chatted with the business number in the last 24 hours.
 */
class WhatsAppService
{
    public static function enabled(): bool
    {
        return (bool) config('whatsapp.enabled') && filled(config('whatsapp.api_key'));
    }

    /** "98765 43210" / "+91 98765-43210" / "09876543210" -> "919876543210"; null when it is not a usable number. */
    public static function normalize(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if ($digits === '') {
            return null;
        }
        if (strlen($digits) === 11 && $digits[0] === '0') {
            $digits = substr($digits, 1);
        }
        if (strlen($digits) === 10) {
            $digits = config('whatsapp.country_code') . $digits;
        }

        return (strlen($digits) >= 11 && strlen($digits) <= 15) ? $digits : null;
    }

    // ------------------------------------------------------------------ sending

    /** POST /whatsapp/send_text. True only when ChatHub answers 2xx and does not say success=false. */
    public static function sendText(string $to, string $text): bool
    {
        $payload = ['text' => $text, 'senderId' => $to];
        if (filled(config('whatsapp.from'))) {
            $payload['from'] = config('whatsapp.from');
        }

        try {
            $res = Http::withHeaders(['x-api-key' => (string) config('whatsapp.api_key')])
                ->acceptJson()
                ->asJson()
                ->timeout((int) config('whatsapp.timeout'))
                ->post(rtrim((string) config('whatsapp.base_url'), '/') . '/whatsapp/send_text', $payload);

            $body = $res->json();
            if ($res->successful() && !(is_array($body) && array_key_exists('success', $body) && $body['success'] === false)) {
                return true;
            }

            Log::warning('WhatsApp send_text failed', ['status' => $res->status(), 'body' => Str::limit($res->body(), 300)]);
        } catch (\Throwable $e) {
            Log::warning('WhatsApp send_text error: ' . $e->getMessage());
        }

        return false;
    }

    /** Sends to a phone number in any common format. Never throws; returns whether ChatHub accepted it. */
    public static function send(?string $phone, string $text): bool
    {
        if (!self::enabled()) {
            return false;
        }
        $to = self::normalize($phone);
        if (!$to) {
            Log::info('WhatsApp skipped: no valid phone number');
            return false;
        }

        return self::sendText($to, $text);
    }

    /** Runs after the HTTP response has been sent, so the customer never waits for WhatsApp. */
    private static function later(callable $job): void
    {
        if (!self::enabled()) {
            return;
        }
        // terminating callbacks run after the response has been sent (nothing is serialized or queued)
        app()->terminating(function () use ($job) {
            try {
                $job();
            } catch (\Throwable $e) {
                Log::warning('WhatsApp notification error: ' . $e->getMessage());
            }
        });
    }

    // ------------------------------------------------------------------ OTP

    public static function sendOtp(?string $phone, string $code): bool
    {
        return self::send(
            $phone,
            "*{$code}* is your Scrap Daddy verification code.\nIt is valid for 10 minutes. Please do not share it with anyone."
        );
    }

    // ------------------------------------------------------------------ order helpers

    private static function ref(Order $order): string
    {
        return '#ORD-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT);
    }

    private static function when(Order $order): string
    {
        $date = $order->pickup_date ? Carbon::parse($order->pickup_date)->format('d M Y') : '';

        return trim($date . ($order->pickup_time ? ', ' . $order->pickup_time : ''));
    }

    private static function items(Order $order): string
    {
        $parts = collect($order->items ?? [])->map(fn ($i) => trim(($i['name'] ?? 'Item') . ' x' . ($i['quantity'] ?? 1)));

        return $parts->isEmpty() ? '-' : Str::limit($parts->implode(', '), 300);
    }

    // ------------------------------------------------------------------ order notifications

    /** A pickup was booked: tell the admin number, and confirm to the customer. */
    public static function orderBooked(Order $order): void
    {
        self::later(function () use ($order) {
            $order->loadMissing('user');
            $name = $order->user->full_name ?? 'Customer';
            $phone = $order->user->phone_number ?? '';
            $ref = self::ref($order);
            $when = self::when($order);
            $address = trim(($order->pickup_location ?? '') . ($order->pin_code ? ' - ' . $order->pin_code : ''));

            self::send(
                config('whatsapp.admin_number'),
                "\u{1F195} *New pickup booked* {$ref}\n"
                . "Customer: {$name} ({$phone})\n"
                . 'Items: ' . self::items($order) . "\n"
                . "Pickup: {$when}\n"
                . "Address: {$address}"
                . ($order->notes ? "\nNotes: " . Str::limit($order->notes, 200) : '')
            );

            self::send(
                $phone,
                "Hi {$name}, your scrap pickup {$ref} is booked \u{2705}\nPickup: {$when}\nOur team will confirm it shortly.\n\n- Scrap Daddy"
            );
        });
    }

    /** The admin changed the order status: tell the customer (only when it really changed). */
    public static function orderStatusChanged(Order $order, ?string $oldStatus = null): void
    {
        if ($oldStatus !== null && $oldStatus === $order->status) {
            return;
        }

        self::later(function () use ($order) {
            $order->loadMissing('user');
            $name = $order->user->full_name ?? 'Customer';
            $ref = self::ref($order);
            $when = self::when($order);

            $text = match ($order->status) {
                'accepted' => "Hi {$name}, your pickup {$ref} is confirmed \u{2705}\nOur team will arrive on {$when}.\n\n- Scrap Daddy",
                'assigned' => "Hi {$name}, a pickup team is assigned to {$ref} \u{1F69A}\nExpected: {$when}.\n\n- Scrap Daddy",
                'completed' => "Hi {$name}, your pickup {$ref} is completed \u{2705}\nThank you for choosing Scrap Daddy! \u{267B}\u{FE0F}",
                'cancelled' => "Hi {$name}, your pickup {$ref} has been cancelled.\nIf this was not expected, please contact us.\n\n- Scrap Daddy",
                default => "Hi {$name}, your pickup {$ref} is under review. We will update you shortly.\n\n- Scrap Daddy",
            };

            self::send($order->user->phone_number ?? null, $text);
        });
    }

    /** The customer cancelled: tell the admin number. */
    public static function orderCancelledByCustomer(Order $order): void
    {
        self::later(function () use ($order) {
            $order->loadMissing('user');
            $name = $order->user->full_name ?? 'Customer';
            $phone = $order->user->phone_number ?? '';

            self::send(
                config('whatsapp.admin_number'),
                "\u{274C} *Pickup cancelled by customer* " . self::ref($order) . "\nCustomer: {$name} ({$phone})\nPickup was: " . self::when($order)
            );
        });
    }
}
