<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Refer a friend: every user has a code. A friend registers with it; once the friend's first
 * pickup is paid, the referrer (and the friend) get reward coins. Amounts come from
 * Admin > Site Settings.
 */
class ReferralService
{
    public static function codeFor(User $user): string
    {
        if ($user->referral_code) {
            return $user->referral_code;
        }

        do {
            $code = 'SD' . strtoupper(Str::random(6));
        } while (User::where('referral_code', $code)->exists());

        $user->referral_code = $code;
        $user->save();

        return $code;
    }

    public static function findReferrer(?string $code): ?User
    {
        $code = strtoupper(trim((string) $code));
        if ($code === '') {
            return null;
        }

        return User::where('referral_code', $code)->where('status', 1)->first();
    }

    public static function referrerBonus(): int
    {
        return max(0, (int) SiteSetting::get('referral_bonus_coins', 50));
    }

    public static function friendBonus(): int
    {
        return max(0, (int) SiteSetting::get('referral_friend_coins', 20));
    }

    /** Call inside the payment transaction, after the friend's order is marked paid. */
    public static function rewardOnFirstPaidOrder(User $friend): void
    {
        if (!$friend->referred_by || $friend->referral_rewarded) {
            return;
        }

        $referrer = User::where('uuid', $friend->referred_by)->lockForUpdate()->first();
        if ($referrer) {
            $referrer->reward_coins = (int) ($referrer->reward_coins ?? 0) + self::referrerBonus();
            $referrer->save();
        }

        $friend->reward_coins = (int) ($friend->reward_coins ?? 0) + self::friendBonus();
        $friend->referral_rewarded = true;
        $friend->save();
    }

    public static function stats(User $user): array
    {
        $friends = User::where('referred_by', $user->uuid);

        return [
            'friends_joined' => (clone $friends)->count(),
            'friends_rewarded' => (clone $friends)->where('referral_rewarded', true)->count(),
            'coins_earned' => (clone $friends)->where('referral_rewarded', true)->count() * self::referrerBonus(),
        ];
    }
}
