@extends('layouts.app')

@php
    $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP;
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqs->map(fn ($f) => [
            '@type' => 'Question',
            'name' => $f->question,
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => trim(strip_tags((string) $f->answer))],
        ])->values()->all(),
    ];
@endphp

@section('title', 'FAQs - Scrap Daddy')

@push('meta')
    <meta name="description" content="Answers to common questions about selling scrap with Scrap Daddy: pickup, weighing, rates, payment and rewards.">
    <link rel="canonical" href="{{ route('site.faqs') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="FAQs - Scrap Daddy">
    <meta property="og:url" content="{{ route('site.faqs') }}">
    <meta property="og:image" content="{{ $site['logo_url'] }}">
    @if($faqs->count())<script type="application/ld+json">{!! json_encode($schema, $jsonFlags) !!}</script>@endif
@endpush

@push('styles')
    @include('site._styles')
@endpush

@section('content')
<div class="st-wrap">
    <header class="st-hero">
        <span class="st-badge"><i class="fa-solid fa-circle-question"></i> Help</span>
        <h1>Frequently asked questions</h1>
        <p>Quick answers about pickups, weighing, payment and rewards.</p>
    </header>

    @if($faqs->count())
    <div class="accordion st-faq" id="faqAcc">
        @foreach($faqs as $i => $f)
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $f->id }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">{{ $f->question }}</button>
                </h2>
                <div id="faq{{ $f->id }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAcc">
                    <div class="accordion-body">{!! nl2br(e(strip_tags((string) $f->answer))) !!}</div>
                </div>
            </div>
        @endforeach
    </div>
    @else
        <div class="st-card text-center text-muted"><i class="fa-solid fa-circle-question fa-2x mb-2 opacity-50"></i><div>No questions added yet. Please check back soon.</div></div>
    @endif

    <div class="st-cta mt-4">
        <b>Still need help?</b>
        <a href="{{ route('site.contact') }}">Contact us <i class="fa-solid fa-arrow-right"></i></a>
    </div>
</div>
@endsection
