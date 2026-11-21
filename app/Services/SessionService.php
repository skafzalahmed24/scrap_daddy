<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * One active login per customer. A new login (website or app) replaces the old one;
 * the old device is told "you were logged in on another device" instead of a vague auth error.
 */
class SessionService
{
    public const MESSAGE = 'You have logged in from another device. For security reasons, you have been logged out of this session.';

    /** Revoke every existing app token, remember which ones were replaced, return the new login marker. */
    public static function startNewSession(User $user): string
    {
        foreach ($user->tokens()->pluck('id') as $tokenId) {
            Cache::put(self::replacedKey($tokenId), true, now()->addDays(30));
        }
        $user->tokens()->delete();

        $marker = Str::random(32);
        $user->forceFill(['login_marker' => $marker])->save();

        return $marker;
    }

    public static function wasReplaced(?string $bearer): bool
    {
        $id = explode('|', (string) $bearer)[0] ?? '';

        return ctype_digit($id) && Cache::has(self::replacedKey((int) $id));
    }

    private static function replacedKey(int $tokenId): string
    {
        return 'session_replaced:' . $tokenId;
    }
}
