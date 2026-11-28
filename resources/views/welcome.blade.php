@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ time() }}">
    <style>
        @media (max-width: 767.98px) {
            .hero-section {
                height: 190px !important;
                min-height: 190px !important;
                max-height: 190px !important;
                width: 100% !important;
                background: #ffffff !important;
                overflow: hidden !important;
                margin-bottom: 15px !important;
            }
            .hero-mobile-wrapper, 
            .hero-mobile-wrapper .carousel, 
            .hero-mobile-wrapper .carousel-inner, 
            .hero-mobile-wrapper .carousel-item {
                width: 100% !important;
                height: 190px !important;
                max-height: 190px !important;
                background: #ffffff !important;
            }
            .hero-mobile-img {
                width: 100% !important;
                max-width: 100% !important;
                height: 190px !important;
                max-height: 190px !important;
                object-fit: cover !important;
                object-position: center !important;
                display: block !important;
                margin: 0 auto !important;
            }
            .hero-mobile-fallback {
                width: 100% !important;
                height: 190px !important;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            .custom-mobile-indicators {
                position: absolute;
                bottom: 6px;
                left: 0;
                right: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 5px;
                z-index: 5;
                margin: 0;
                padding: 0;
                background: transparent;
            }
            .custom-mobile-indicators button {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background-color: var(--primary-green);
                opacity: 0.35;
                border: none;
                padding: 0;
                margin: 0;
                transition: all 0.3s ease;
            }
            .custom-mobile-indicators button.active {
                opacity: 1;
                width: 18px;
                border-radius: 9px;
                background-color: var(--primary-green);
            }
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <!-- Desktop/Tablet View (Web Banners) -->
        <div class="d-none d-md-block h-100">
            @if($webBanners->count() > 0)
                <div id="heroCarouselWeb" class="carousel slide carousel-fade h-100" data-bs-ride="carousel">
                    <div class="carousel-inner h-100">
                        @foreach($webBanners as $index => $banner)
                            <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
                                @if(preg_match('/\.(mp4|webm|ogg)$/i', $banner->uploads))
                                    <video class="hero-media" autoplay muted loop playsinline>
                                        <source src="{{ asset($banner->uploads) }}" type="video/{{ pathinfo($banner->uploads, PATHINFO_EXTENSION) }}">
                                    </video>
                                @else
                                    <img src="{{ asset($banner->uploads) }}" class="hero-media" alt="{{ $banner->title }}">
                                @endif
                                
                                <div class="hero-overlay">
                                    <div class="container">
                                        <div class="hero-content">
                                            <h1 class="hero-title">{{ $banner->title }}</h1>
                                            <p class="hero-subtitle">{{ $banner->short_description }}</p>
                                            <div class="hero-buttons d-flex gap-3 mt-4">
                                                <a href="{{ url('/#categories') }}" class="btn btn-primary rounded-pill shadow-sm px-4 py-2 fw-bold" style="background: var(--primary-green); border: none; color: white;"><i class="fa-solid fa-truck-fast me-2"></i> Book Pickup Now <i class="fa-solid fa-arrow-right ms-2"></i></a>
                                                <a href="{{ route('categories.explore') }}" class="btn btn-light rounded-pill shadow-sm px-4 py-2 fw-bold" style="color: var(--primary-green);"><i class="fa-solid fa-layer-group me-2"></i> Explore Categories <i class="fa-solid fa-arrow-right ms-2"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($webBanners->count() > 1)
                    <button class="carousel-control-prev" style="z-index: 3;" type="button" data-bs-target="#heroCarouselWeb" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" style="z-index: 3;" type="button" data-bs-target="#heroCarouselWeb" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    @endif
                </div>
            @else
                <img src="https://images.unsplash.com/photo-1587293852726-70cdb56c2866?q=80&w=2072&auto=format&fit=crop" class="hero-media" alt="Background">
                <div class="hero-overlay">
                    <div class="container">
                        <div class="hero-content">
                            <h1 class="hero-title">Global Scrap Recycling Made Simple</h1>
                            <p class="hero-subtitle">Source bulk scrap materials locally and deliver to businesses worldwide. We control quality & stock.</p>
                            <div class="hero-buttons d-flex gap-3 mt-4">
                                <a href="{{ url('/#categories') }}" class="btn btn-primary rounded-pill shadow-sm px-4 py-2 fw-bold" style="background: var(--primary-green); border: none; color: white;"><i class="fa-solid fa-truck-fast me-2"></i> Book Pickup Now <i class="fa-solid fa-arrow-right ms-2"></i></a>
                                <a href="{{ route('categories.explore') }}" class="btn btn-light rounded-pill shadow-sm px-4 py-2 fw-bold" style="color: var(--primary-green);"><i class="fa-solid fa-layer-group me-2"></i> Explore Categories <i class="fa-solid fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Mobile View (Mobile Banners) -->
        <style>
            /* all banners share one size, and each carries a booking button */
            .bn-img { aspect-ratio: 16 / 7; object-fit: cover; width: 100%; display: block; }
            .bn-cta { position: absolute; left: 14px; bottom: 14px; z-index: 3; display: inline-flex; align-items: center; gap: 8px; background: #fff; color: #2b7a3d !important; font-weight: 800; font-size: .82rem; border-radius: 999px; padding: 9px 16px; text-decoration: none; box-shadow: 0 6px 16px rgba(0,0,0,.22); }
            .bn-cta i { font-size: .7rem; }
        </style>
        <div class="d-block d-md-none hero-mobile-wrapper">
            @if($mobileBanners->count() > 0)
                <div id="heroCarouselMobile" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
                    <div class="carousel-inner">
                        @foreach($mobileBanners as $index => $banner)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <a href="{{ url('/#categories') }}" class="d-block w-100 text-decoration-none">
                                    @if($banner->uploads)
                                        @if(preg_match('/\.(mp4|webm|ogg)$/i', $banner->uploads))
                                            <video class="hero-mobile-img" autoplay muted loop playsinline style="width: 100% !important; height: 190px !important; object-fit: cover !important; display: block !important; margin: 0 auto !important;">
                                                <source src="{{ asset($banner->uploads) }}" type="video/{{ pathinfo($banner->uploads, PATHINFO_EXTENSION) }}">
                                            </video>
                                        @else
                                            <img src="{{ asset($banner->uploads) }}" class="hero-mobile-img" style="width: 100% !important; height: 190px !important; object-fit: cover !important; display: block !important; margin: 0 auto !important;" alt="{{ $banner->title }}">
                                        @endif
                                    @else
                                        <div class="hero-mobile-fallback p-3 text-center">
                                            <h2 class="hero-mobile-title">{{ $banner->title }}</h2>
                                            @if($banner->short_description)
                                                <p class="hero-mobile-subtitle">{{ $banner->short_description }}</p>
                                            @endif
                                            <span class="btn btn-primary btn-sm rounded-pill mt-1 fw-bold px-3" style="background: var(--primary-green); border: none; color: white;">Book Pickup <i class="fa-solid fa-arrow-right ms-1"></i></span>
                                        </div>
                                    @endif
                                </a>
                                @if($banner->uploads)
                                    <a href="{{ $banner->cta_link ?: route('customer.orders.create') }}" class="bn-cta">{{ $banner->cta_text ?: 'Book a Pickup' }} <i class="fa-solid fa-arrow-right"></i></a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if($mobileBanners->count() > 1)
                    <div class="custom-mobile-indicators">
                        @foreach($mobileBanners as $index => $banner)
                            <button type="button" data-bs-target="#heroCarouselMobile" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                    @endif
                </div>
            @else
                <a href="{{ url('/#categories') }}" class="d-block w-100 text-decoration-none">
                    <img src="https://images.unsplash.com/photo-1587293852726-70cdb56c2866?q=80&w=2072&auto=format&fit=crop" class="hero-mobile-img" style="width: 100% !important; height: 190px !important; object-fit: cover !important; display: block !important; margin: 0 auto !important;" alt="Background">
                </a>
            @endif
        </div>
    </section>

<!-- Features Strip -->
    <div class="container features-strip-container d-none d-md-block">
        <div class="features-strip">
            <div class="feature-item">
                <i class="fa-solid fa-warehouse feature-icon"></i>
                <div class="feature-text">
                    <h6>Our Own Store & Warehouse</h6>
                    <p>We Control Quality & Stock</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-box-open feature-icon"></i>
                <div class="feature-text">
                    <h6>No Minimum Quantity</h6>
                    <p>Sell Any Amount of Scrap</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-tags feature-icon"></i>
                <div class="feature-text">
                    <h6>Competitive Market Prices</h6>
                    <p>Better Margins For You</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-truck-fast feature-icon"></i>
                <div class="feature-text">
                    <h6>Free Doorstep Pickup</h6>
                    <p>Hassle-Free Collection</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-shield-halved feature-icon"></i>
                <div class="feature-text">
                    <h6>Secure & Reliable</h6>
                    <p>Instant Digital Payments</p>
                </div>
            </div>
        </div>
    </div>

@auth
    @php
        $user = auth()->user();
        $recentOrders = \App\Models\Order::with('subcategory')->where('user_uuid', $user->uuid)->latest()->take(5)->get();
    @endphp
    @if($recentOrders->count() > 0)
    <section class="container mt-5 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold m-0" style="color: #0d2b4d;">My Active Pickups</h4>
            <a href="{{ route('customer.orders') }}" class="text-decoration-none fw-bold" style="color: #3D8B4D;">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>
        
        <div class="d-flex gap-3 overflow-auto pb-3" style="scrollbar-width: none;">
            @foreach($recentOrders as $order)
                @if(in_array($order->status, ['pending', 'accepted']))
                @include('partials.customer.pickup-tracker-card', ['order' => $order])
                @endif
            @endforeach
        </div>
    </section>
    @endif
    @endauth



    

    <!-- Categories Section -->
    <section id="categories" class="categories-section">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="section-heading mb-0">Categories</h3>
                <a href="{{ route('categories.explore') }}" class="btn rounded-pill px-4 fw-bold shadow-sm" style="border: 2px solid var(--primary-green); color: var(--primary-green); background: white; transition: all 0.3s;" onmouseover="this.style.background='var(--primary-green)'; this.style.color='white';" onmouseout="this.style.background='white'; this.style.color='var(--primary-green)';">View More <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            
            <div class="categories-grid position-relative">
                @forelse($categories as $category)
                <a href="{{ route('category.show', $category->uuid) }}" class="category-circle-item text-decoration-none">
                    <div class="category-circle">
                        @if($category->image)
                            <img src="{{ asset($category->image) }}" alt="{{ $category->title }}">
                        @else
                            <i class="fa-solid fa-recycle"></i>
                        @endif
                    </div>
                    <span class="category-name">{{ $category->title }}</span>
                </a>
                @empty
                <div class="category-circle-item">
                    <div class="category-circle" style="border-color: #ffe69c;">
                        <i class="fa-solid fa-laptop text-warning"></i>
                    </div>
                    <span class="category-name">E-Waste</span>
                </div>
                <div class="category-circle-item">
                    <div class="category-circle" style="border-color: #d3d6d8;">
                        <i class="fa-solid fa-car text-secondary"></i>
                    </div>
                    <span class="category-name">Metals</span>
                </div>
                <div class="category-circle-item">
                    <div class="category-circle" style="border-color: #badbcc;">
                        <i class="fa-solid fa-newspaper text-success"></i>
                    </div>
                    <span class="category-name">Paper</span>
                </div>
                <div class="category-circle-item">
                    <div class="category-circle" style="border-color: #b6effb;">
                        <i class="fa-solid fa-bottle-water text-info"></i>
                    </div>
                    <span class="category-name">Plastic</span>
                </div>
                @endforelse
                
                <!-- Extra padding element to prevent last item cutoff in flex scroll -->
        </div>
    </section>

     <div class="container py-3">
         @include('partials.home-sections')
     </div>


    <style>
        .sdw-section { padding: 72px 0; }
        .sdw-how { background: linear-gradient(180deg, #ffffff 0%, #f1f8f1 100%); }
        .sdw-testi { background: #f4f7fa; }
        .sdw-head { text-align: center; max-width: 640px; margin: 0 auto 44px; }
        .sdw-eyebrow { display: inline-block; padding: 6px 16px; border-radius: 50px; background: rgba(74,154,88,.1); color: var(--primary-green, #3D8B4D); font-weight: 600; font-size: .85rem; margin-bottom: 14px; }
        .sdw-title { font-weight: 800; color: var(--primary-blue, #0d2b4d); font-size: 2.2rem; margin: 0 0 10px; letter-spacing: -.3px; }
        .sdw-sub { color: #6b7280; font-size: 1.05rem; margin: 0; }

        .sdw-step { position: relative; height: 100%; background: #fff; border: 1px solid #e8f0e8; border-radius: 20px; padding: 32px 26px; text-align: center; box-shadow: 0 6px 22px rgba(0,0,0,.04); transition: transform .25s ease, box-shadow .25s ease; overflow: hidden; }
        .sdw-step:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(0,0,0,.08); }
        .sdw-step-n { position: absolute; top: 12px; right: 18px; font-size: 2.6rem; font-weight: 900; color: rgba(74,154,88,.08); line-height: 1; }
        .sdw-step-ic { width: 68px; height: 68px; border-radius: 20px; color: #fff; font-size: 1.6rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; box-shadow: 0 8px 18px rgba(0,0,0,.15); }
        .sdw-step h4 { font-size: 1.2rem; font-weight: 700; color: #111; margin: 0 0 8px; }
        .sdw-step p { color: #6b7280; margin: 0; font-size: .95rem; line-height: 1.55; }
        .sdw-cta { display: inline-flex; align-items: center; padding: 14px 30px; border-radius: 50px; background: var(--primary-green, #3D8B4D); color: #fff; font-weight: 700; text-decoration: none; box-shadow: 0 8px 20px rgba(74,154,88,.3); transition: transform .2s ease; }
        .sdw-cta:hover { transform: translateY(-2px); color: #fff; }

        .sdw-quote { height: 100%; display: flex; flex-direction: column; background: #fff; border-radius: 20px; padding: 26px; border: 1px solid #edf0f3; box-shadow: 0 6px 22px rgba(0,0,0,.04); transition: transform .25s ease, box-shadow .25s ease; }
        .sdw-quote:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(0,0,0,.08); }
        .sdw-stars { color: #ffb300; font-size: .9rem; letter-spacing: 2px; margin-bottom: 14px; }
        .sdw-quote-text { color: #374151; font-size: .98rem; line-height: 1.65; flex: 1; margin: 0 0 20px; }
        .sdw-person { display: flex; align-items: center; gap: 12px; padding-top: 16px; border-top: 1px solid #f0f2f4; }
        .sdw-avatar { width: 46px; height: 46px; flex: 0 0 46px; border-radius: 50%; color: #fff; font-weight: 700; display: flex; align-items: center; justify-content: center; }
        .sdw-person h6 { margin: 0; font-weight: 700; color: #111; font-size: .95rem; }
        .sdw-person small { color: #6b7280; font-size: .8rem; }

        @media (max-width: 767.98px) {
            .sdw-section { padding: 44px 0; }
            .sdw-title { font-size: 1.6rem; }
            .sdw-sub { font-size: .95rem; }
            .sdw-head { margin-bottom: 28px; }
            .sdw-step { padding: 24px 20px; }
            .sdw-quote { padding: 22px; }
        }
    </style>
@endsection
