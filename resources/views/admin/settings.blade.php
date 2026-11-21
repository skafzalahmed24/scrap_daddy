@extends('layouts.admin')

@section('title', 'Site Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0">Site Settings</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf

    <div class="row g-4">
        <!-- Company & address -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-location-dot text-success me-2"></i>Company &amp; Address</h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Company name</label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $settings['company_name']) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Footer description</label>
                        <textarea name="footer_about" rows="2" class="form-control" maxlength="400">{{ old('footer_about', $settings['footer_about']) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Address line 1</label>
                        <input type="text" name="address_line1" class="form-control" value="{{ old('address_line1', $settings['address_line1']) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Address line 2 / Area</label>
                        <input type="text" name="address_line2" class="form-control" value="{{ old('address_line2', $settings['address_line2']) }}">
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $settings['city']) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">State</label>
                            <input type="text" name="state" class="form-control" value="{{ old('state', $settings['state']) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country', $settings['country']) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">PIN code</label>
                            <input type="text" name="pincode" class="form-control" maxlength="6" inputmode="numeric" value="{{ old('pincode', $settings['pincode']) }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact & social -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-headset text-success me-2"></i>Contact</h5>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Support email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $settings['email']) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Phone (shown in footer)</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $settings['phone']) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Help line / toll-free (shown in header &amp; help cards)</label>
                        <input type="text" name="toll_free" class="form-control" value="{{ old('toll_free', $settings['toll_free']) }}">
                        <div class="form-text">Leave empty to show the phone number above instead.</div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Referral: coins for the referrer</label>
                            <input type="number" min="0" name="referral_bonus_coins" class="form-control" value="{{ old('referral_bonus_coins', $settings['referral_bonus_coins']) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Referral: coins for the friend</label>
                            <input type="number" min="0" name="referral_friend_coins" class="form-control" value="{{ old('referral_friend_coins', $settings['referral_friend_coins']) }}" required>
                        </div>
                        <div class="form-text">Paid after the friend's first completed pickup.</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">WhatsApp number</label>
                        <input type="text" name="whatsapp" class="form-control" placeholder="e.g. +91 98765 43210" value="{{ old('whatsapp', $settings['whatsapp']) }}">
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1"><i class="fa-solid fa-share-nodes text-success me-2"></i>Social media</h5>
                    <p class="text-muted small mb-3">Full links. Only the ones you fill in are shown on the website.</p>
                    @foreach([
                        'instagram' => ['Instagram', 'fa-instagram', 'https://instagram.com/yourpage'],
                        'facebook' => ['Facebook', 'fa-facebook-f', 'https://facebook.com/yourpage'],
                        'youtube' => ['YouTube', 'fa-youtube', 'https://youtube.com/@yourchannel'],
                        'twitter' => ['X (Twitter)', 'fa-x-twitter', 'https://x.com/yourpage'],
                        'linkedin' => ['LinkedIn', 'fa-linkedin-in', 'https://linkedin.com/company/yourpage'],
                    ] as $key => [$label, $icon, $placeholder])
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white" style="width: 46px; justify-content: center;"><i class="fa-brands {{ $icon }}"></i></span>
                            <input type="url" name="{{ $key }}" class="form-control" placeholder="{{ $placeholder }}" aria-label="{{ $label }}" value="{{ old($key, $settings[$key]) }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-success px-4 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i>Save settings</button>
    </div>
</form>
@endsection
