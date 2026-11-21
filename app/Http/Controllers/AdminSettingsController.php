<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings', ['settings' => SiteSetting::values()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:150',
            'footer_about' => 'nullable|string|max:400',
            'address_line1' => 'required|string|max:200',
            'address_line2' => 'nullable|string|max:200',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'pincode' => ['required', 'string', 'regex:/^[0-9]{6}$/'],
            'email' => 'required|email|max:150',
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{5,30}$/'],
            'toll_free' => 'nullable|string|max:30',
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\s\-]{8,20}$/'],
            'referral_bonus_coins' => 'required|integer|min:0|max:100000',
            'referral_friend_coins' => 'required|integer|min:0|max:100000',
            'instagram' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
        ], [
            'pincode.regex' => 'PIN code must be 6 digits.',
            'phone.regex' => 'Enter a valid phone number.',
            'whatsapp.regex' => 'Enter a valid WhatsApp number (digits, optional + and spaces).',
            'instagram.url' => 'Instagram link must be a full URL, e.g. https://instagram.com/yourpage',
            'facebook.url' => 'Facebook link must be a full URL, e.g. https://facebook.com/yourpage',
            'youtube.url' => 'YouTube link must be a full URL, e.g. https://youtube.com/@yourchannel',
            'twitter.url' => 'X (Twitter) link must be a full URL, e.g. https://x.com/yourpage',
            'linkedin.url' => 'LinkedIn link must be a full URL, e.g. https://linkedin.com/company/yourpage',
        ]);

        foreach (array_keys($data) as $key) { // only what this form submitted (payment settings live on their own page)
            SiteSetting::updateOrCreate(['key' => $key], ['value' => trim((string) ($data[$key] ?? ''))]);
        }

        SiteSetting::flush();

        return redirect()->route('admin.settings.index')->with('success', 'Site settings saved. The website footer and contact details are updated.');
    }
}
