<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

/** Admin > Payment Settings: advance payment (on/off + %) and which payment methods customers can use. */
class AdminPaymentSettingsController extends Controller
{
    public function index()
    {
        $key = (string) config('services.razorpay.key');

        return view('admin.payment-settings', [
            's' => SiteSetting::values(),
            'razorpayReady' => $key !== '' && (string) config('services.razorpay.secret') !== '',
            'razorpayMode' => str_starts_with($key, 'rzp_live') ? 'Live' : 'Test',
            'razorpayKeyHint' => $key !== '' ? substr($key, 0, 12) . '…' : null,
        ]);
    }

    public function update(Request $request)
    {
        $on = fn ($k) => $request->boolean($k) ? '1' : '0';

        $request->validate([
            'advance_percent' => 'required|integer|min:1|max:90',
            'gateway_fee_percent' => 'nullable|numeric|min:0|max:10',
            'upi_id' => ['nullable', 'string', 'max:60', 'regex:/^[A-Za-z0-9.\-_]{2,}@[A-Za-z]{2,}$/'],
            'upi_name' => 'nullable|string|max:80',
            'bank_holder' => 'nullable|string|max:80',
            'bank_name' => 'nullable|string|max:80',
            'bank_account' => ['nullable', 'string', 'regex:/^[0-9]{6,18}$/'],
            'bank_ifsc' => ['nullable', 'string', 'regex:/^[A-Za-z]{4}0[A-Za-z0-9]{6}$/'],
            'pay_instructions' => 'nullable|string|max:400',
        ], [
            'upi_id.regex' => 'Enter a valid UPI ID, for example name@bank.',
            'bank_account.regex' => 'Account number must be 6-18 digits.',
            'bank_ifsc.regex' => 'Enter a valid IFSC code, for example SBIN0001234.',
        ]);

        // A method that is switched on must have its details filled in
        if ($on('pay_upi') === '1' && !$request->filled('upi_id')) {
            return back()->withInput()->withErrors(['upi_id' => 'Enter the UPI ID to enable UPI payments.']);
        }
        if ($on('pay_bank') === '1' && (!$request->filled('bank_account') || !$request->filled('bank_ifsc') || !$request->filled('bank_holder'))) {
            return back()->withInput()->withErrors(['bank_account' => 'Account holder, account number and IFSC are required to enable bank transfer.']);
        }

        $values = [
            'advance_enabled' => $on('advance_enabled'),
            'advance_percent' => (string) (int) $request->advance_percent,
            'pay_razorpay' => $on('pay_razorpay'),
            'gateway_fee_percent' => (string) round((float) ($request->filled('gateway_fee_percent') ? $request->gateway_fee_percent : 2), 2),
            'pay_upi' => $on('pay_upi'),
            'upi_id' => trim((string) $request->upi_id),
            'upi_name' => trim((string) $request->upi_name),
            'pay_bank' => $on('pay_bank'),
            'bank_holder' => trim((string) $request->bank_holder),
            'bank_name' => trim((string) $request->bank_name),
            'bank_account' => trim((string) $request->bank_account),
            'bank_ifsc' => strtoupper(trim((string) $request->bank_ifsc)),
            'pay_cash' => $on('pay_cash'),
            'pay_instructions' => trim((string) $request->pay_instructions),
        ];

        foreach ($values as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        SiteSetting::flush();

        return redirect()->route('admin.payment-settings.index')->with('success', 'Payment settings saved. The app and website use them immediately.');
    }
}
