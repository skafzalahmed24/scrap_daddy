@extends('layouts.app')

@php
    $slides = collect([$article->cover_image])->merge($article->images->pluck('image'))->filter()->unique()->values();
    $paragraphs = preg_split('/\n\s*\n/', trim((string) $article->content), -1, PREG_SPLIT_NO_EMPTY);
    $url = route('blog.show', $article->slug);
    $desc = $article->metaDescription();
    $img = $article->cover_image ? asset($article->cover_image) : asset('scraplogo.jpeg');
    $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP;
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $article->title,
        'description' => $desc,
        'image' => $slides->map(fn ($p) => asset($p))->values()->all() ?: [$img],
        'datePublished' => $article->created_at->toAtomString(),
        'dateModified' => $article->updated_at->toAtomString(),
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
        'author' => ['@type' => 'Organization', 'name' => 'Scrap Daddy'],
        'publisher' => ['@type' => 'Organization', 'name' => 'Scrap Daddy', 'logo' => ['@type' => 'ImageObject', 'url' => asset('scraplogo.jpeg')]],
    ];
    $crumbs = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => route('blog.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $article->title, 'item' => $url],
        ],
    ];
@endphp

@section('title', $article->title . ' | Scrap Daddy Blog')

@push('meta')
    <meta name="description" content="{{ $desc }}">
    <link rel="canonical" href="{{ $url }}">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="Scrap Daddy">
    <meta property="og:title" content="{{ $article->title }}">
    <meta property="og:description" content="{{ $desc }}">
    <meta property="og:url" content="{{ $url }}">
    <meta property="og:image" content="{{ $img }}">
    <meta property="article:published_time" content="{{ $article->created_at->toAtomString() }}">
    <meta property="article:modified_time" content="{{ $article->updated_at->toAtomString() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $article->title }}">
    <meta name="twitter:description" content="{{ $desc }}">
    <meta name="twitter:image" content="{{ $img }}">
    <script type="application/ld+json">{!! json_encode($schema, $jsonFlags) !!}</script>
    <script type="application/ld+json">{!! json_encode($crumbs, $jsonFlags) !!}</script>
@endpush

@push('styles')
<style>
    .ln-wrap { max-width: 760px; margin: 0 auto; padding: 14px 14px 40px; }
    .ln-crumbs { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; font-size: .82rem; color: #667085; margin: 6px 0 12px; }
    .ln-crumbs a { color: #3D8B4D; text-decoration: none; font-weight: 600; }
    .ln-crumbs i { font-size: .6rem; opacity: .6; }

    .ln-gallery { position: relative; border-radius: 28px; overflow: hidden; box-shadow: 0 16px 36px rgba(16,24,40,.16); background: #eef7ef; }
    .ln-gallery .carousel-item img { width: 100%; aspect-ratio: 16 / 9; object-fit: cover; display: block; }
    .ln-gallery .carousel-indicators { margin-bottom: 36px; }
    .ln-gallery .carousel-indicators [data-bs-target] { width: 8px; height: 8px; border-radius: 50%; border: 0; }
    .ln-gallery .carousel-control-prev, .ln-gallery .carousel-control-next { width: 14%; }
    .ln-gallery .ln-ctl { width: 36px; height: 36px; border-radius: 50%; background: rgba(0,0,0,.38); display: flex; align-items: center; justify-content: center; color: #fff; }

    .ln-card { background: #fff; border-radius: 28px; padding: 24px 22px; margin-top: -28px; position: relative; box-shadow: 0 12px 30px rgba(16,24,40,.09); }
    .ln-badge { display: inline-flex; align-items: center; gap: 6px; background: #eaf6ea; color: #2b7a3d; font-weight: 700; font-size: .72rem; letter-spacing: .04em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px; }
    .ln-card h1 { font-size: 1.65rem; font-weight: 800; color: #1d2939; margin: 12px 0 6px; line-height: 1.2; }
    .ln-meta { color: #98a2b3; font-size: .8rem; margin-bottom: 12px; }
    .ln-sum { color: #475467; font-size: 1.02rem; margin-bottom: 14px; }
    .ln-body { color: #344054; font-size: 1rem; line-height: 1.8; }
    .ln-body p { margin: 0 0 14px; }

    .ln-cta { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; justify-content: space-between; margin-top: 22px; padding: 18px; border-radius: 22px; background: linear-gradient(135deg, #17603a, #3D8B4D 60%, #69be7b); color: #fff; }
    .ln-cta b { font-size: 1.05rem; }
    .ln-cta a { background: #fff; color: #1f6f43; font-weight: 800; border-radius: 999px; padding: 10px 20px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }

    .ln-more h2 { font-weight: 800; color: #1d2939; margin: 30px 0 12px; font-size: 1.15rem; }
    .ln-more-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    @media (min-width: 640px) { .ln-more-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
    .ln-more a { text-decoration: none; color: inherit; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 18px rgba(16,24,40,.08); display: block; }
    .ln-more img { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; display: block; background: #eef7ef; }
    .ln-more b { display: block; padding: 8px 10px 10px; font-size: .84rem; color: #1d2939; line-height: 1.25; }
</style>
@endpush

@section('content')
<div class="ln-wrap">
    <nav class="ln-crumbs" aria-label="Breadcrumb">
        <a href="{{ url('/') }}">Home</a> <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('blog.index') }}">Blog</a> <i class="fa-solid fa-chevron-right"></i>
        <span>{{ \Illuminate\Support\Str::limit($article->title, 40) }}</span>
    </nav>

    @if($slides->count())
    <div id="lnGallery" class="carousel slide ln-gallery" data-bs-ride="carousel">
        @if($slides->count() > 1)
        <div class="carousel-indicators">
            @foreach($slides as $i => $s)<button type="button" data-bs-target="#lnGallery" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}" aria-label="Image {{ $i + 1 }}"></button>@endforeach
        </div>
        @endif
        <div class="carousel-inner">
            @foreach($slides as $i => $s)
                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}"><img src="{{ asset($s) }}" alt="{{ $article->title }}{{ $slides->count() > 1 ? ' - image ' . ($i + 1) : '' }}" {{ $i === 0 ? '' : 'loading=lazy' }}></div>
            @endforeach
        </div>
        @if($slides->count() > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#lnGallery" data-bs-slide="prev" aria-label="Previous"><span class="ln-ctl"><i class="fa-solid fa-chevron-left"></i></span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#lnGallery" data-bs-slide="next" aria-label="Next"><span class="ln-ctl"><i class="fa-solid fa-chevron-right"></i></span></button>
        @endif
    </div>
    @endif

    <article class="ln-card">
        <span class="ln-badge"><i class="fa-solid fa-lightbulb"></i> Know your scrap</span>
        <h1>{{ $article->title }}</h1>
        <div class="ln-meta"><time datetime="{{ $article->created_at->toAtomString() }}">{{ $article->created_at->format('d M Y') }}</time></div>
        @if($article->summary)<p class="ln-sum">{{ $article->summary }}</p>@endif
        <div class="ln-body">
            @foreach($paragraphs as $para)<p>{!! nl2br(e($para)) !!}</p>@endforeach
        </div>

        <div class="ln-cta">
            <b>Ready to sell your scrap?</b>
            <a href="{{ route('customer.orders.create') }}">Book a pickup <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </article>

    @if($more->count())
    <section class="ln-more" aria-label="More articles">
        <h2>More to explore</h2>
        <div class="ln-more-grid">
            @foreach($more as $m)
                <a href="{{ route('blog.show', $m->slug) }}">
                    @if($m->cover_image)<img src="{{ asset($m->cover_image) }}" alt="{{ $m->title }}" loading="lazy">@endif
                    <b>{{ $m->title }}</b>
                </a>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
