@extends('layouts.app')

@php
    $pageUrl = route('blog.index');
    $desc = \Illuminate\Support\Str::limit(trim($intro), 158, '...');
    $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP;
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Blog',
        'name' => 'Scrap Daddy Blog',
        'description' => $desc,
        'url' => $pageUrl,
        'publisher' => ['@type' => 'Organization', 'name' => 'Scrap Daddy', 'logo' => ['@type' => 'ImageObject', 'url' => $site['logo_url']]],
        'blogPost' => $articles->map(fn ($a) => [
            '@type' => 'BlogPosting',
            'headline' => $a->title,
            'url' => route('blog.show', $a->slug),
            'datePublished' => $a->created_at->toAtomString(),
            'image' => $a->cover_image ? asset($a->cover_image) : null,
        ])->values()->all(),
    ];
@endphp

@section('title', $heading . ' - Scrap Daddy Blog')

@push('meta')
    <meta name="description" content="{{ $desc }}">
    {{-- page 2, 3 ... are real pages of their own --}}
    <link rel="canonical" href="{{ $articles->currentPage() > 1 ? $articles->url($articles->currentPage()) : $pageUrl }}">
    @if($articles->previousPageUrl())<link rel="prev" href="{{ $articles->previousPageUrl() }}">@endif
    @if($articles->nextPageUrl())<link rel="next" href="{{ $articles->nextPageUrl() }}">@endif
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Scrap Daddy">
    <meta property="og:title" content="{{ $heading }} - Scrap Daddy Blog">
    <meta property="og:description" content="{{ $desc }}">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:image" content="{{ $site['logo_url'] }}">
    <meta name="twitter:card" content="summary_large_image">
    <script type="application/ld+json">{!! json_encode($schema, $jsonFlags) !!}</script>
@endpush

@push('styles')
<style>
    .bl-wrap { max-width: 1080px; margin: 0 auto; padding: 18px 14px 48px; }
    .bl-hero { position: relative; overflow: hidden; border-radius: 28px; padding: 26px 22px; background: linear-gradient(135deg, #17603a, #3D8B4D 55%, #69be7b); color: #fff; box-shadow: 0 14px 30px rgba(61,139,77,.28); margin-bottom: 22px; }
    .bl-hero:before { content: ''; position: absolute; width: 220px; height: 220px; border-radius: 50%; background: rgba(255,255,255,.10); right: -60px; top: -80px; }
    .bl-hero h1 { position: relative; font-weight: 800; font-size: 1.7rem; margin: 0 0 8px; }
    .bl-hero p { position: relative; margin: 0; opacity: .94; max-width: 640px; line-height: 1.5; }
    .bl-badge { position: relative; display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,.2); border-radius: 999px; padding: 5px 14px; font-size: .75rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; margin-bottom: 12px; }

    .bl-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
    @media (min-width: 576px) { .bl-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 992px) { .bl-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    .bl-card { display: flex; flex-direction: column; background: #fff; border-radius: 24px; overflow: hidden; text-decoration: none; color: inherit; box-shadow: 0 10px 24px rgba(16,24,40,.09); transition: transform .15s ease, box-shadow .15s ease; }
    .bl-card:hover { transform: translateY(-3px); box-shadow: 0 16px 32px rgba(16,24,40,.13); }
    .bl-card img { width: 100%; aspect-ratio: 16 / 10; object-fit: cover; display: block; background: #eaf6ea; }
    .bl-body { padding: 14px 16px 16px; display: flex; flex-direction: column; flex: 1; }
    .bl-body h2 { font-size: 1.05rem; font-weight: 800; color: #1d2939; margin: 0 0 6px; line-height: 1.3; }
    .bl-body p { color: #667085; font-size: .88rem; line-height: 1.45; margin: 0 0 12px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .bl-more { margin-top: auto; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; font-size: .84rem; color: #3D8B4D; }
    .bl-empty { text-align: center; padding: 56px 16px; color: #667085; background: #fff; border-radius: 24px; }
    .bl-pages { display: flex; justify-content: center; margin-top: 26px; }
</style>
@endpush

@section('content')
<div class="bl-wrap">
    <header class="bl-hero">
        <span class="bl-badge"><i class="fa-solid fa-lightbulb"></i> Blog</span>
        <h1>{{ $heading }}</h1>
        <p>{{ $intro }}</p>
    </header>

    @if($articles->count())
        <div class="bl-grid">
            @foreach($articles as $a)
                <a class="bl-card" href="{{ route('blog.show', $a->slug) }}">
                    @if($a->cover_image)<img src="{{ asset($a->cover_image) }}" alt="{{ $a->title }}" {{ $loop->index < 3 ? '' : 'loading=lazy' }}>@endif
                    <div class="bl-body">
                        <h2>{{ $a->title }}</h2>
                        @if($a->summary)<p>{{ $a->summary }}</p>@endif
                        <span class="bl-more">Read article <i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="bl-pages">{{ $articles->links() }}</div>
    @else
        <div class="bl-empty"><i class="fa-solid fa-book-open fa-2x mb-3 opacity-50"></i><div>No articles yet. Please check back soon.</div></div>
    @endif
</div>
@endsection
