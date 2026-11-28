@extends('layouts.app')

@php
    $phone = $site['toll_free'] ?: $site['phone'];
    $tel = preg_replace('/[^0-9+]/', '', (string) $phone);
    $wa = preg_replace('/\D+/', '', (string) ($site['whatsapp'] ?: config('whatsapp.admin_number')));
    $address = trim(implode(', ', array_filter([$site['address_line1'], $site['address_line2'], $site['city'], $site['state'], $site['pincode']])));
@endphp

@section('title', 'Contact Us - Scrap Daddy')

@push('meta')
    <meta name="description" content="Contact Scrap Daddy: call, WhatsApp or email us to book a scrap pickup or ask a question.">
    <link rel="canonical" href="{{ route('site.contact') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Contact Us - Scrap Daddy">
    <meta property="og:url" content="{{ route('site.contact') }}">
    <meta property="og:image" content="{{ $site['logo_url'] }}">
@endpush

@push('styles')
    @include('site._styles')
@endpush

@section('content')
<div class="st-wrap">
    <header class="st-hero">
        <span class="st-badge"><i class="fa-solid fa-headset"></i> Contact</span>
        <h1>Contact us</h1>
        <p>Questions about a pickup, rates or your order? Reach us any way that is easiest for you.</p>
    </header>

    <div class="st-grid c3 mb-4">
        @if($phone)
        <a class="st-contact" href="tel:{{ $tel }}"><span class="ic" style="background:#3D8B4D;"><i class="fa-solid fa-phone"></i></span><span><small>Call us</small><b>{{ $phone }}</b></span></a>
        @endif
        @if($wa)
        <a class="st-contact" href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"><span class="ic" style="background:#25D366;"><i class="fa-brands fa-whatsapp"></i></span><span><small>WhatsApp</small><b>Chat with us</b></span></a>
        @endif
        @if($site['email'])
        <a class="st-contact" href="mailto:{{ $site['email'] }}"><span class="ic" style="background:#2b6cb0;"><i class="fa-solid fa-envelope"></i></span><span><small>Email</small><b>{{ $site['email'] }}</b></span></a>
        @endif
    </div>

    @if($address)
    <section class="st-card">
        <h2><i class="fa-solid fa-location-dot me-2" style="color:#3D8B4D;"></i>Our address</h2>
        <p class="mb-2">{{ $site['company_name'] }}<br>{{ $address }}</p>
        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($address) }}" target="_blank" rel="noopener" class="fw-bold text-decoration-none" style="color:#3D8B4D;">Open in Google Maps <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i></a>
    </section>
    @endif

    <div class="st-cta">
        <b>Want a pickup instead? It takes about a minute.</b>
        <a href="{{ route('customer.orders.create') }}">Book a pickup <i class="fa-solid fa-arrow-right"></i></a>
    </div>
</div>
@endsection
