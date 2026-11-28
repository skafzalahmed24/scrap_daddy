@extends('layouts.app')

@section('title', 'Scrap Pickup & Junk Removal Services - Scrap Daddy')

@push('meta')
    <meta name="description" content="Scrap pickup and junk removal at your doorstep: paper, plastic, metal, e-waste, appliances and furniture. Certified weighing and instant payment.">
    <link rel="canonical" href="{{ route('site.services') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Scrap Pickup & Junk Removal Services - Scrap Daddy">
    <meta property="og:url" content="{{ route('site.services') }}">
    <meta property="og:image" content="{{ $site['logo_url'] }}">
@endpush

@push('styles')
    @include('site._styles')
@endpush

@section('content')
<div class="st-wrap">
    <header class="st-hero">
        <span class="st-badge"><i class="fa-solid fa-truck-ramp-box"></i> Services</span>
        <h1>Scrap pickup &amp; junk removal</h1>
        <p>We pick up what you no longer need, weigh it fairly and pay you instantly. Choose a category to get started.</p>
    </header>

    @if($categories->count())
    <div class="st-grid c4 mb-4">
        @foreach($categories as $cat)
            <a class="st-cat" href="{{ route('customer.orders.create', ['category' => $cat->uuid]) }}">
                @if($cat->image)<img src="{{ asset($cat->image) }}" alt="{{ $cat->title }}" loading="lazy">@endif
                <b>{{ $cat->title }}</b>
                @if($cat->description)<small>{{ \Illuminate\Support\Str::limit($cat->description, 70) }}</small>@endif
            </a>
        @endforeach
    </div>
    @endif

    @if($steps->count())
        <h2 class="fw-bold mb-3" style="font-size:1.2rem;color:#1d2939;">{{ $howSection->title ?? 'How it works' }}</h2>
        @include('site._steps', ['steps' => $steps])
        <div class="mb-4"></div>
    @endif

    <div class="st-cta">
        <b>Ready to get rid of your scrap?</b>
        <a href="{{ route('customer.orders.create') }}">Book a pickup <i class="fa-solid fa-arrow-right"></i></a>
    </div>
</div>
@endsection
