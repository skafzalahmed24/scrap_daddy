@extends('layouts.app')

@section('title', 'Help & Support - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
    <style>
        .sp-wrap { max-width: 760px; margin: 0 auto; }
        .sp-card { display:flex; align-items:center; gap:14px; background:#fff; border:1px solid rgba(16,24,40,.06); border-radius:20px; padding:16px 18px; margin-bottom:12px; text-decoration:none; color:inherit; box-shadow:0 6px 20px rgba(16,24,40,.05); transition:transform .15s, box-shadow .2s; }
        .sp-card:hover { transform:translateY(-2px); box-shadow:0 12px 28px rgba(16,24,40,.08); color:inherit; }
        .sp-ic { flex:0 0 50px; height:50px; border-radius:16px; background:#eef7ef; color:#4a8f55; display:flex; align-items:center; justify-content:center; font-size:1.15rem; }
        .sp-card h6 { margin:0 0 2px; font-weight:700; color:#1f2a24; }
        .sp-card p { margin:0; color:#6b7770; font-size:.88rem; }
        .sp-form { background:#fff; border:1px solid rgba(16,24,40,.06); border-radius:20px; padding:20px; box-shadow:0 6px 20px rgba(16,24,40,.05); }
        .sp-ticket { background:#fff; border:1px solid rgba(16,24,40,.06); border-radius:16px; padding:14px 16px; margin-bottom:10px; }
        .sp-reply { background:#f3f8f4; border-radius:12px; padding:10px 12px; margin-top:10px; font-size:.9rem; }
    </style>
@endpush

@section('content')
<div class="container py-4">
    <div class="sp-wrap">
        <h3 class="fw-bold mb-1">Help &amp; Support</h3>
        <p class="text-muted mb-4">We're here to help. Pick how you'd like to reach us.</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <a class="sp-card" href="mailto:{{ $contact['email'] }}">
            <div class="sp-ic"><i class="fa-regular fa-envelope"></i></div>
            <div><h6>Contact Us</h6><p>{{ $contact['email'] }}</p></div>
        </a>
        <a class="sp-card" href="tel:{{ preg_replace('/[^0-9+]/', '', $contact['call_number']) }}">
            <div class="sp-ic"><i class="fa-solid fa-phone"></i></div>
            <div><h6>Call Us</h6><p>{{ $contact['call_number'] }}</p></div>
        </a>
        @if($contact['whatsapp_link'])
            <a class="sp-card" href="{{ $contact['whatsapp_link'] }}" target="_blank" rel="noopener">
                <div class="sp-ic"><i class="fa-brands fa-whatsapp"></i></div>
                <div><h6>WhatsApp</h6><p>{{ $contact['whatsapp'] }}</p></div>
            </a>
        @endif
        <a class="sp-card" href="#raiseTicket">
            <div class="sp-ic"><i class="fa-regular fa-file-lines"></i></div>
            <div><h6>Raise a Ticket</h6><p>Report an issue or share feedback</p></div>
        </a>

        <div class="sp-form mt-4" id="raiseTicket">
            <h5 class="fw-bold mb-3">Raise a ticket</h5>
            <form method="POST" action="{{ route('customer.support.ticket') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control" maxlength="150" minlength="3" value="{{ old('subject') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Message <span class="text-danger">*</span></label>
                    <textarea name="message" rows="4" class="form-control" maxlength="2000" minlength="10" placeholder="Tell us what happened (at least 10 characters)" required>{{ old('message') }}</textarea>
                </div>
                <button class="btn btn-primary px-4">Submit ticket</button>
            </form>
        </div>

        @if($tickets->count())
            <h5 class="fw-bold mt-4 mb-3">My tickets</h5>
            @foreach($tickets as $t)
                <div class="sp-ticket">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <strong>#{{ $t->id }} &middot; {{ $t->subject }}</strong>
                        <span class="badge rounded-pill {{ $t->status === 'closed' ? 'bg-success' : ($t->status === 'in_progress' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ str_replace('_', ' ', $t->status) }}</span>
                    </div>
                    <div class="text-muted small mb-1">{{ $t->created_at->format('d M Y, h:i A') }}</div>
                    <div style="white-space: pre-line;">{{ $t->message }}</div>
                    @if($t->admin_reply)
                        <div class="sp-reply"><strong>Support reply:</strong> {{ $t->admin_reply }}</div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
