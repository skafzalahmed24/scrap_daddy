@extends('layouts.app')

@section('title', 'About Us - Scrap Daddy')

@push('meta')
    <meta name="description" content="Scrap Daddy is a doorstep scrap pickup service: certified weighing, fair rates and instant payment for paper, plastic, metal, e-waste and appliances.">
    <link rel="canonical" href="{{ route('site.about') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="About Us - Scrap Daddy">
    <meta property="og:description" content="Doorstep scrap pickup with certified weighing and instant payment.">
    <meta property="og:url" content="{{ route('site.about') }}">
    <meta property="og:image" content="{{ $site['logo_url'] }}">
@endpush

@push('styles')
    @include('site._styles')
@endpush

@section('content')
<div class="st-wrap">
    <header class="st-hero">
        <span class="st-badge"><i class="fa-solid fa-circle-info"></i> About us</span>
        <h1>About Scrap Daddy</h1>
        <p>{{ $site['footer_about'] ?: 'We collect, weigh and pay you instantly at your doorstep.' }}</p>
    </header>

    <section class="st-card">
        @if($page && trim(strip_tags((string) $page->content)) !== '')
            {!! $page->content !!}
        @else
            <h2>Who we are</h2>
            <p>{{ $site['company_name'] }} makes selling scrap simple. Instead of carrying your paper, plastic, metal or old appliances to a scrap shop, you book a pickup, and our team comes to your door, weighs everything in front of you and pays you on the spot.</p>
            <h2 class="mt-4">What we do</h2>
            <ul>
                <li><b>Doorstep pickup</b> at a 1-hour time slot you choose.</li>
                <li><b>Certified digital weighing</b>, so you always see the exact weight.</li>
                <li><b>Instant payment</b> by UPI, bank transfer or cash, plus reward coins on every pickup.</li>
                <li><b>Responsible recycling</b>: what you give us goes back into use instead of landfill.</li>
            </ul>
        @endif
    </section>

    @if($steps->count())
        <h2 class="fw-bold mb-3" style="font-size:1.2rem;color:#1d2939;">How it works</h2>
        @include('site._steps', ['steps' => $steps])
        <div class="mb-4"></div>
    @endif

    <div class="st-cta">
        <b>Ready to sell your scrap?</b>
        <a href="{{ route('customer.orders.create') }}">Book a pickup <i class="fa-solid fa-arrow-right"></i></a>
    </div>
</div>
@endsection
