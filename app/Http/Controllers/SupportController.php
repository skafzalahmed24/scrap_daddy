<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\SupportTicket;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Help & support (contact details from Admin > Site Settings, tickets answered in Admin > Support Tickets)
 * and Refer a friend. Used by the website (session) and the mobile app (Sanctum token).
 */
class SupportController extends Controller
{
    /** Contact details exactly as the admin entered them. */
    public static function contact(): array
    {
        $s = SiteSetting::values();
        $digits = preg_replace('/\D+/', '', (string) ($s['whatsapp'] ?? ''));
        $call = $s['toll_free'] && preg_match('/\d{6,}/', preg_replace('/\D+/', '', $s['toll_free']))
            ? $s['toll_free'] : $s['phone'];

        return [
            'phone' => $s['phone'],
            'call_number' => $call,
            'toll_free' => $s['toll_free'],
            'email' => $s['email'],
            'whatsapp' => $s['whatsapp'],
            'whatsapp_link' => $digits ? 'https://wa.me/' . $digits : null,
            'address' => trim(implode(', ', array_filter([
                $s['address_line1'], $s['address_line2'], $s['city'], $s['state'], $s['pincode'],
            ]))),
            'company_name' => $s['company_name'],
        ];
    }

    // ---------------- API (mobile app) ----------------

    public function info()
    {
        return response()->json(['status' => 1, 'message' => 'Support info', 'data' => self::contact()]);
    }

    public function createTicket(Request $request)
    {
        $v = Validator::make($request->all(), [
            'subject' => 'required|string|min:3|max:150',
            'message' => 'required|string|min:10|max:2000',
        ]);
        if ($v->fails()) {
            return response()->json(['status' => 0, 'message' => 'Validation error', 'data' => ['errors' => $v->errors()]], 200);
        }

        $ticket = $this->storeTicket($request->user()->uuid, $request->subject, $request->message);

        return response()->json(['status' => 1, 'message' => 'Ticket raised. Our team will reply soon.', 'data' => $ticket], 201);
    }

    public function myTickets(Request $request)
    {
        $rows = SupportTicket::where('user_uuid', $request->user()->uuid)->latest()->take(50)->get();

        return response()->json(['status' => 1, 'message' => 'Tickets fetched', 'data' => ['count' => $rows->count(), 'rows' => $rows]]);
    }

    public function referral(Request $request)
    {
        return response()->json(['status' => 1, 'message' => 'Referral info', 'data' => $this->referralPayload($request->user())]);
    }

    // ---------------- Website ----------------

    public function supportPage()
    {
        $user = auth()->user();

        return view('customer.support', [
            'contact' => self::contact(),
            'tickets' => SupportTicket::where('user_uuid', $user->uuid)->latest()->take(20)->get(),
        ]);
    }

    public function webCreateTicket(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|min:3|max:150',
            'message' => 'required|string|min:10|max:2000',
        ]);

        $this->storeTicket(auth()->user()->uuid, $request->subject, $request->message);

        return redirect()->route('customer.support')->with('success', 'Ticket raised. Our team will reply soon.');
    }

    public function referPage()
    {
        return view('customer.refer', ['ref' => $this->referralPayload(auth()->user())]);
    }

    // ---------------- helpers ----------------

    private function storeTicket(string $userUuid, string $subject, string $message): SupportTicket
    {
        return SupportTicket::create([
            'user_uuid' => $userUuid,
            'subject' => trim(strip_tags($subject)),
            'message' => trim(strip_tags($message)),
            'status' => 'open',
        ]);
    }

    private function referralPayload($user): array
    {
        $code = ReferralService::codeFor($user);
        $link = url('/customer/register') . '?ref=' . $code;

        return [
            'code' => $code,
            'link' => $link,
            'referrer_bonus_coins' => ReferralService::referrerBonus(),
            'friend_bonus_coins' => ReferralService::friendBonus(),
            'share_message' => "Sell your scrap from home at the best price with ScrapDaddy! Sign up with my code {$code} and get "
                . ReferralService::friendBonus() . " reward coins on your first pickup. {$link}",
        ] + ReferralService::stats($user);
    }
}
