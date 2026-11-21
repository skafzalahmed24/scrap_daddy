@extends('layouts.admin')

@section('title', 'Payment Settings')

@push('styles')
<style>
    .pr-switch { position: relative; display: inline-block; width: 50px; height: 28px; flex-shrink: 0; }
    .pr-switch input { opacity: 0; width: 0; height: 0; }
    .pr-switch span { position: absolute; inset: 0; background: #CBD5E1; border-radius: 999px; transition: .2s; cursor: pointer; }
    .pr-switch span::before { content: ''; position: absolute; left: 3px; top: 3px; width: 22px; height: 22px; background: #fff; border-radius: 50%; transition: .2s; box-shadow: 0 1px 4px rgba(0,0,0,.25); }
    .pr-switch input:checked + span { background: #3D8B4D; }
    .pr-switch input:checked + span::before { transform: translateX(22px); }
    .pm-head { display: flex; align-items: center; justify-content: space-between; gap: 14px; }
    .pm-head h3 { margin: 0; font-size: 1.02rem; font-weight: 800; color: var(--pr-ink); }
    .pm-head p { margin: 2px 0 0; color: var(--pr-muted); font-size: .84rem; }
    .pm-body { margin-top: 16px; padding-top: 16px; border-top: 1px dashed var(--pr-line); }
    .pm-off .pm-body { display: none; }
    .pm-ic { width: 44px; height: 44px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="mb-3">
    <h1 class="page-title mb-1">Payment Settings</h1>
    <div class="pr-sub">Advance payment and the ways your customers can pay. Changes apply to the app and website immediately.</div>
</div>

@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

@php $on = fn ($k) => old($k, $s[$k] ?? '0') === '1' || old($k) === 'on' || old($k) === '1'; @endphp

<form method="POST" action="{{ route('admin.payment-settings.update') }}" id="pmForm">
    @csrf

    {{-- ===== Advance payment ===== --}}
    <div class="pr-card mb-3 {{ $on('advance_enabled') ? '' : 'pm-off' }}" data-card>
        <div class="pm-head">
            <div class="d-flex gap-3 align-items-center">
                <div class="pm-ic ic-amber"><i class="fa-solid fa-percent"></i></div>
                <div><h3>Advance payment</h3><p>Ask the customer to pay a part of the bill first; the balance is paid after the pickup.</p></div>
            </div>
            <label class="pr-switch"><input type="checkbox" name="advance_enabled" value="1" @checked($on('advance_enabled')) data-toggle><span></span></label>
        </div>
        <div class="pm-body">
            <label class="form-label fw-semibold small">Advance percentage</label>
            <div class="input-group" style="max-width:240px;">
                <input type="number" min="1" max="90" name="advance_percent" id="advPct" class="form-control" value="{{ old('advance_percent', $s['advance_percent']) }}" required>
                <span class="input-group-text">% of the bill</span>
            </div>
            <div class="form-text">Example: with 20%, a ₹1,000 pickup asks ₹200 in advance and ₹800 after completion. The customer sees the advance, the balance and the status in the app and on the website.</div>
        </div>
    </div>

    {{-- ===== Online ===== --}}
    <div class="pr-card mb-3 {{ $on('pay_razorpay') ? '' : 'pm-off' }}" data-card>
        <div class="pm-head">
            <div class="d-flex gap-3 align-items-center">
                <div class="pm-ic ic-blue"><i class="fa-solid fa-credit-card"></i></div>
                <div><h3>Online payment (Razorpay)</h3><p>Cards, UPI and netbanking with instant confirmation.</p></div>
            </div>
            <label class="pr-switch"><input type="checkbox" name="pay_razorpay" value="1" @checked($on('pay_razorpay')) data-toggle><span></span></label>
        </div>
        <div class="pm-body">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Gateway fee (%)</label>
                <div class="input-group" style="max-width:240px;">
                    <input type="number" step="0.01" min="0" max="10" name="gateway_fee_percent" class="form-control" value="{{ old('gateway_fee_percent', $s['gateway_fee_percent']) }}">
                    <span class="input-group-text">%</span>
                </div>
                <div class="form-text">What Razorpay deducts from each online payment (about 2%). Shown in the order's payment breakdown as the fee and your net amount.</div>
            </div>
            @if($razorpayReady)
                <span class="pr-pill pill-completed">Connected</span> <span class="pr-sub ms-2">{{ $razorpayMode }} mode &middot; key {{ $razorpayKeyHint }}</span>
            @else
                <span class="pr-pill pill-cancelled">Not connected</span>
                <div class="pr-sub mt-2">Add <code>RAZORPAY_KEY</code> and <code>RAZORPAY_SECRET</code> to the server's <code>.env</code>. Until then this method is hidden from customers.</div>
            @endif
        </div>
    </div>

    {{-- ===== UPI ===== --}}
    <div class="pr-card mb-3 {{ $on('pay_upi') ? '' : 'pm-off' }}" data-card>
        <div class="pm-head">
            <div class="d-flex gap-3 align-items-center">
                <div class="pm-ic ic-violet"><i class="fa-solid fa-mobile-screen"></i></div>
                <div><h3>UPI (direct)</h3><p>Customer pays to your UPI ID and enters the transaction number. You approve it.</p></div>
            </div>
            <label class="pr-switch"><input type="checkbox" name="pay_upi" value="1" @checked($on('pay_upi')) data-toggle><span></span></label>
        </div>
        <div class="pm-body row g-3">
            <div class="col-md-6"><label class="form-label fw-semibold small">UPI ID</label><input type="text" name="upi_id" class="form-control" placeholder="yourname@bank" value="{{ old('upi_id', $s['upi_id']) }}"></div>
            <div class="col-md-6"><label class="form-label fw-semibold small">Name shown to customer</label><input type="text" name="upi_name" class="form-control" placeholder="Scrap Daddy" value="{{ old('upi_name', $s['upi_name']) }}"></div>
        </div>
    </div>

    {{-- ===== Bank ===== --}}
    <div class="pr-card mb-3 {{ $on('pay_bank') ? '' : 'pm-off' }}" data-card>
        <div class="pm-head">
            <div class="d-flex gap-3 align-items-center">
                <div class="pm-ic ic-green"><i class="fa-solid fa-building-columns"></i></div>
                <div><h3>Bank transfer</h3><p>NEFT / IMPS to your account. Customer enters the UTR; you approve it.</p></div>
            </div>
            <label class="pr-switch"><input type="checkbox" name="pay_bank" value="1" @checked($on('pay_bank')) data-toggle><span></span></label>
        </div>
        <div class="pm-body row g-3">
            <div class="col-md-6"><label class="form-label fw-semibold small">Account holder</label><input type="text" name="bank_holder" class="form-control" value="{{ old('bank_holder', $s['bank_holder']) }}"></div>
            <div class="col-md-6"><label class="form-label fw-semibold small">Bank name</label><input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $s['bank_name']) }}"></div>
            <div class="col-md-6"><label class="form-label fw-semibold small">Account number</label><input type="text" name="bank_account" inputmode="numeric" class="form-control" value="{{ old('bank_account', $s['bank_account']) }}"></div>
            <div class="col-md-6"><label class="form-label fw-semibold small">IFSC</label><input type="text" name="bank_ifsc" class="form-control text-uppercase" maxlength="11" value="{{ old('bank_ifsc', $s['bank_ifsc']) }}"></div>
        </div>
    </div>

    {{-- ===== Cash ===== --}}
    <div class="pr-card mb-3 {{ $on('pay_cash') ? '' : 'pm-off' }}" data-card>
        <div class="pm-head">
            <div class="d-flex gap-3 align-items-center">
                <div class="pm-ic ic-rose"><i class="fa-solid fa-money-bill-wave"></i></div>
                <div><h3>Cash</h3><p>Customer chooses cash; your executive collects it and you record/approve it on the order.</p></div>
            </div>
            <label class="pr-switch"><input type="checkbox" name="pay_cash" value="1" @checked($on('pay_cash')) data-toggle><span></span></label>
        </div>
        <div class="pm-body pr-sub">No details needed. Customers see "Cash" and a message that your team will confirm.</div>
    </div>

    <div class="pr-card mb-4">
        <label class="form-label fw-semibold">Note shown on the payment screen <span class="text-muted fw-normal">(optional)</span></label>
        <textarea name="pay_instructions" rows="2" maxlength="400" class="form-control" placeholder="e.g. Payments are verified within 30 minutes (9 AM - 8 PM).">{{ old('pay_instructions', $s['pay_instructions']) }}</textarea>
    </div>

    <button class="btn btn-primary px-4 py-2"><i class="fa-solid fa-floppy-disk me-1"></i> Save payment settings</button>
</form>
@endsection

@push('scripts')
<script>
    // a switched-off method hides its detail fields
    document.querySelectorAll('[data-toggle]').forEach(function (t) {
        t.addEventListener('change', function () { t.closest('[data-card]').classList.toggle('pm-off', !t.checked); });
    });
</script>
@endpush
