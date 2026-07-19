@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        @if($banners->count() > 0)
            <div id="heroCarousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel">
                <div class="carousel-inner h-100">
                    @foreach($banners as $index => $banner)
                        <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
                            @if(preg_match('/\.(mp4|webm|ogg)$/i', $banner->uploads))
                                <video class="hero-media" autoplay muted loop playsinline>
                                    <source src="/{{ $banner->uploads }}" type="video/{{ pathinfo($banner->uploads, PATHINFO_EXTENSION) }}">
                                </video>
                            @else
                                <img src="/{{ $banner->uploads }}" class="hero-media" alt="{{ $banner->title }}">
                            @endif
                            
                            <div class="hero-overlay">
                                <div class="container">
                                    <div class="hero-content">
                                        <h1 class="hero-title">{{ $banner->title }}</h1>
                                        <p class="hero-subtitle">{{ $banner->short_description }}</p>
                                        <div class="hero-buttons">
                                            <a href="#categories" class="btn btn-primary shadow-sm"><i class="fa-regular fa-calendar-check me-2"></i> Book Pickup Now <i class="fa-solid fa-arrow-right ms-2"></i></a>
                                            <a href="/explore-categories" class="btn btn-outline shadow-sm"><i class="fa-solid fa-border-all me-2"></i> Explore Categories <i class="fa-solid fa-arrow-right ms-2"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($banners->count() > 1)
                <button class="carousel-control-prev" style="z-index: 3;" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" style="z-index: 3;" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
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
                        <div class="hero-buttons">
                            <a href="#categories" class="btn btn-primary shadow-sm"><i class="fa-regular fa-calendar-check me-2"></i> Book Pickup Now <i class="fa-solid fa-arrow-right ms-2"></i></a>
                            <a href="/explore-categories" class="btn btn-outline shadow-sm"><i class="fa-solid fa-border-all me-2"></i> Explore Categories <i class="fa-solid fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    @auth
    @php
        $user = auth()->user() ?? \App\Models\User::first() ?? new \App\Models\User(['full_name' => 'Shaik Afzal', 'phone_number' => '+91 9876543210']);
        $recentOrders = \App\Models\Order::with('subcategory')->where('user_uuid', $user->uuid)->latest()->take(5)->get();
    @endphp
    <section class="container mt-5 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold m-0" style="color: #0d2b4d;">My Active Pickups</h4>
            <a href="{{ route('customer.orders') }}" class="text-decoration-none fw-bold" style="color: #1b5e20;">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>
        
        <div class="d-flex gap-3 overflow-auto pb-3" style="scrollbar-width: none;">
            @forelse($recentOrders as $order)
                @if(in_array($order->status, ['pending', 'accepted']))
                @php 
                    $isAccepted = $order->status == 'accepted';
                    $themeColor = $isAccepted ? '#4285f4' : '#2e7d32'; 
                    $themeBg = $isAccepted ? 'rgba(66, 133, 244, 0.1)' : 'rgba(46, 125, 50, 0.1)';
                @endphp
                <!-- Active Tracker Card (Light Theme) -->
                <div class="card border-0 bg-white flex-shrink-0 mb-2 rounded-4" style="box-shadow: 0 4px 20px rgba(0,0,0,0.05); min-width: 380px; max-width: 600px; width: 100%; border-left: 6px solid {{ $themeColor }} !important;">
                    <div class="card-body p-4">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                            <div class="d-flex gap-3 align-items-center">
                                <div style="width: 50px; height: 50px; border-radius: 50%; background: {{ $themeBg }}; color: {{ $themeColor }}; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-paper-plane fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold" style="color: #0d2b4d;">Request Submitted</h5>
                                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Pickup: <span class="fw-bold" style="color: {{ $themeColor }};">{{ $order->pickup_date == date('Y-m-d') ? 'Today' : \Carbon\Carbon::parse($order->pickup_date)->format('D, d M') }}</span></p>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('customer.orders') }}" class="btn btn-sm bg-white fw-bold d-flex align-items-center gap-2 px-3 py-2 shadow-sm text-decoration-none" style="color: #2e7d32; border: 1px solid rgba(46, 125, 50, 0.3); border-radius: 8px;">
                                    <i class="fa-regular fa-eye"></i> View
                                </a>
                            </div>
                        </div>

                        <!-- Stepper -->
                        <div class="overflow-auto pb-3 mb-5 mt-4" style="scrollbar-width: none;">
                            <div class="d-flex justify-content-between position-relative text-center" style="min-width: 450px;">
                                <!-- connecting lines -->
                                <div class="position-absolute" style="top: 15px; left: 10%; right: 10%; height: 2px; z-index: 1;">
                                    <div class="d-flex w-100 h-100">
                                        <div style="flex: 1; border-top: 2px solid #2e7d32;"></div>
                                        <div style="flex: 1; border-top: 2px dashed {{ $isAccepted ? '#2e7d32' : '#e0e0e0' }};"></div>
                                        <div style="flex: 1; border-top: 2px dashed #e0e0e0;"></div>
                                    </div>
                                </div>
                                
                                <!-- Step 1: Submitted -->
                                <div class="position-relative" style="z-index: 2; width: 25%;">
                                    <div class="mx-auto mb-2 d-flex align-items-center justify-content-center bg-white" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #2e7d32; color: #2e7d32; background: #e8f5e9 !important;">
                                        <i class="fa-solid fa-check fs-6"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1" style="font-size: 0.85rem; color: #2e7d32;">Submitted</h6>
                                </div>

                                <!-- Step 2: Confirmed -->
                                <div class="position-relative" style="z-index: 2; width: 25%;">
                                    <div class="mx-auto mb-2 d-flex align-items-center justify-content-center bg-white" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid {{ $isAccepted ? '#2e7d32' : '#fbbc04' }}; color: {{ $isAccepted ? '#2e7d32' : '#fbbc04' }}; background: {{ $isAccepted ? '#e8f5e9' : '#fff8e1' }} !important;">
                                        <i class="fa-{{ $isAccepted ? 'solid fa-check' : 'regular fa-clock' }} fs-6"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1" style="font-size: 0.85rem; color: {{ $isAccepted ? '#2e7d32' : '#fbbc04' }};">Confirmed</h6>
                                </div>

                                <!-- Step 3: Assigned -->
                                <div class="position-relative" style="z-index: 2; width: 25%;">
                                    <div class="mx-auto mb-2 d-flex align-items-center justify-content-center bg-white" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #e0e0e0; color: #9e9e9e; background: #f5f5f5 !important;">
                                        <i class="fa-solid fa-user fs-6"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1" style="font-size: 0.85rem; color: #6c757d;">Assigned</h6>
                                </div>

                                <!-- Step 4: Completed -->
                                <div class="position-relative" style="z-index: 2; width: 25%;">
                                    <div class="mx-auto mb-2 d-flex align-items-center justify-content-center bg-white" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #e0e0e0; color: #9e9e9e; background: #f5f5f5 !important;">
                                        <i class="fa-solid fa-flag fs-6"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1" style="font-size: 0.85rem; color: #6c757d;">Completed</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Box -->
                        @php $progressWidth = $isAccepted ? 75 : 50; @endphp
                        <div class="p-4 rounded-4 mb-3" style="background: rgba(46, 125, 50, 0.05); border: 1px solid rgba(46, 125, 50, 0.1);">
                            <h6 class="fw-bold mb-4 text-dark text-center">
                                {{ $isAccepted ? 'Team is on the way...' : 'Confirming your request...' }}
                            </h6>
                            
                            <div class="position-relative w-100" style="margin-bottom: 25px;">
                                <!-- Moving Truck on Progress Bar -->
                                <div class="position-absolute" style="top: -28px; left: {{ $progressWidth }}%; transform: translateX(-50%); z-index: 2; transition: left 1s ease-in-out;">
                                    <img src="{{ asset('icons8-truck.gif') }}" alt="truck" style="width: 48px; height: 48px; object-fit: contain;">
                                </div>
                                <!-- Progress Bar -->
                                <div class="progress" style="height: 8px; background: #e0e0e0; border-radius: 4px; overflow: visible;">
                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $progressWidth }}%; border-radius: 4px;" aria-valuenow="{{ $progressWidth }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <p class="mb-0 text-muted text-center" style="font-size: 0.85rem;">{{ $isAccepted ? 'Your pickup request has been accepted. Our executive will reach you soon.' : 'Please wait for a while we are confirming your request. This may take 30 mins or more...' }}</p>
                        </div>

                        <!-- Footer Support -->
                        <div class="text-center mt-3 pt-3">
                            <a href="{{ route('page.show', 'help-and-support') }}" class="text-muted text-decoration-none fw-medium" style="font-size: 0.95rem;">
                                <i class="fa-solid fa-headset me-2 text-success"></i>Need Support? We are happy to help
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            @empty
                <div class="w-100 text-center py-4 bg-light rounded-4 text-muted">
                    <i class="fa-solid fa-box-open fa-2x mb-2 opacity-50"></i>
                    <p class="mb-0">No active pickups found. Schedule one today!</p>
                </div>
            @endforelse
        </div>
    </section>
    @endauth

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

    <!-- Categories Section -->
    <section id="categories" class="categories-section">
        <div class="container">
            <div class="d-flex flex-column align-items-center mb-4 gap-2 text-center">
                <h3 class="section-heading mb-0">Shop by scrap category</h3>
                <a href="/explore-categories" class="btn btn-outline-success rounded-pill px-4" style="border-color: var(--primary-green); color: var(--primary-green);">View More <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            
            <div class="categories-grid position-relative">
                @forelse($categories as $category)
                <a href="{{ route('category.show', $category->uuid) }}" class="category-circle-item text-decoration-none">
                    <div class="category-circle">
                        @if($category->image)
                            <img src="/{{ $category->image }}" alt="{{ $category->title }}">
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

    <!-- Subcategories Section -->
    <section class="subcategories-section py-5 bg-light">
        <div class="container">
            <div class="d-flex flex-column align-items-center mb-4 gap-2 text-center">
                <h3 class="section-heading mb-0">Explore Subcategories</h3>
                <a href="#all-subcategories" class="btn btn-outline-success rounded-pill px-4" style="border-color: var(--primary-green); color: var(--primary-green);">View More <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            
            <div class="row row-cols-3 row-cols-md-5 g-4 justify-content-center">
                @if(isset($subcategories))
                    @forelse($subcategories->take(15) as $index => $sub)
                    <div class="col {{ $index >= 6 ? 'd-none d-md-flex' : 'd-flex' }} flex-column align-items-center">
                        <a href="{{ route('customer.orders.create') }}?subcategory={{ $sub->uuid }}" class="text-decoration-none text-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto mb-2" style="width: 100px; height: 100px; background-color: #ffffff; border: 2px solid var(--primary-green); overflow: hidden; transition: transform 0.3s; position: relative;">
                                @if($sub->image)
                                    <img src="/{{ $sub->image }}" alt="{{ $sub->name }}" style="width: 100%; height: 100%; object-fit: contain; padding: 15px;">
                                @else
                                    <i class="fa-solid fa-recycle fa-3x" style="color: var(--primary-green);"></i>
                                @endif
                            </div>
                            <h6 class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $sub->name }}</h6>
                        </a>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted">
                        No subcategories available yet.
                    </div>
                    @endforelse
                @endif
            </div>
        </div>
    </section>

    <!-- What We Do Section -->
    <section id="services" class="services-section py-5" style="background: linear-gradient(180deg, #ffffff 0%, #f1f8f1 100%); position: relative; overflow: hidden;">
        <!-- Aesthetic background circle -->
        <div style="position: absolute; top: -100px; right: -100px; width: 400px; height: 400px; background: rgba(46,125,50,0.04); border-radius: 50%; z-index: 0;"></div>
        
        <div class="container py-5" style="position: relative; z-index: 1;">
            <div class="row align-items-center g-5">
                
                <!-- Left Side: Heading & Intro -->
                <div class="col-lg-5">
                    <span class="badge mb-3 px-3 py-2 rounded-pill fw-semibold" style="background: rgba(46,125,50,0.1); color: var(--primary-green); font-size: 0.9rem;">Our Process</span>
                    <h3 class="section-heading mb-4" style="font-size: 3rem; line-height: 1.2;">How It Works</h3>
                    <p class="text-muted fs-5 mb-4">Selling your scrap has never been easier. We've streamlined our entire process to make sure you get paid instantly without any of the traditional hassle.</p>
                    <a href="#categories" class="btn btn-success px-4 py-3 rounded-pill fw-bold" style="background: var(--primary-green); border: none; box-shadow: 0 8px 20px rgba(46,125,50,0.3);">Start Selling Now <i class="fa-solid fa-arrow-right ms-2"></i></a>
                </div>

                <!-- Right Side: Vertical Steps -->
                <div class="col-lg-7">
                    <div class="process-steps">
                        
                        <!-- Step 1 -->
                        <div class="d-flex p-4 bg-white rounded-4 shadow-sm mb-4 service-card" style="border: 1px solid rgba(46,125,50,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; position: relative; overflow: hidden;">
                            <!-- Large faint number -->
                            <div style="position: absolute; right: -10px; top: -20px; font-size: 8rem; font-weight: 900; color: rgba(46,125,50,0.03); line-height: 1; z-index: 0;">01</div>
                            
                            <div class="me-4 flex-shrink-0" style="position: relative; z-index: 1;">
                                <div style="width: 70px; height: 70px; background: var(--primary-green); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 8px 15px rgba(46,125,50,0.3); transform: rotate(-5deg);">
                                    <i class="fa-solid fa-truck-fast fa-2x" style="transform: rotate(5deg);"></i>
                                </div>
                            </div>
                            <div style="position: relative; z-index: 1;">
                                <h4 class="fw-bold mb-2" style="color: #000000;">Doorstep Pickup</h4>
                                <p class="text-muted mb-0">Schedule a pickup at your convenience. Our team will arrive at your location, weigh the scrap, and transport it safely without any hassle.</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="d-flex p-4 bg-white rounded-4 shadow-sm mb-4 service-card" style="border: 1px solid rgba(46,125,50,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; position: relative; overflow: hidden;">
                            <div style="position: absolute; right: -10px; top: -20px; font-size: 8rem; font-weight: 900; color: rgba(46,125,50,0.03); line-height: 1; z-index: 0;">02</div>
                            
                            <div class="me-4 flex-shrink-0" style="position: relative; z-index: 1;">
                                <div style="width: 70px; height: 70px; background: var(--primary-blue); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 8px 15px rgba(13,43,77,0.2); transform: rotate(5deg);">
                                    <i class="fa-solid fa-scale-balanced fa-2x" style="transform: rotate(-5deg);"></i>
                                </div>
                            </div>
                            <div style="position: relative; z-index: 1;">
                                <h4 class="fw-bold mb-2" style="color: #000000;">Accurate Weighing</h4>
                                <p class="text-muted mb-0">We use certified digital scales to weigh your scrap materials accurately right in front of you, ensuring you get exactly the right value.</p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="d-flex p-4 bg-white rounded-4 shadow-sm service-card" style="border: 1px solid rgba(46,125,50,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; position: relative; overflow: hidden;">
                            <div style="position: absolute; right: -10px; top: -20px; font-size: 8rem; font-weight: 900; color: rgba(46,125,50,0.03); line-height: 1; z-index: 0;">03</div>
                            
                            <div class="me-4 flex-shrink-0" style="position: relative; z-index: 1;">
                                <div style="width: 70px; height: 70px; background: #ff9800; border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 8px 15px rgba(255,152,0,0.3); transform: rotate(-5deg);">
                                    <i class="fa-solid fa-money-bill-transfer fa-2x" style="transform: rotate(5deg);"></i>
                                </div>
                            </div>
                            <div style="position: relative; z-index: 1;">
                                <h4 class="fw-bold mb-2" style="color: #000000;">Instant Payment</h4>
                                <p class="text-muted mb-0">Get paid immediately! We offer instant digital transfers or cash right when we collect your scrap items. No waiting, no delays.</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials-section py-1" style="background-color: #eef2f6;">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <!-- Mobile/Tablet Heading (Shows before image on smaller screens) -->
                <div class="col-12 d-lg-none mb-0 pb-0">
                    <h2 class="fw-bold mb-2" style="color: var(--primary-blue); font-size: 2.5rem;">Trusted by Our Customers</h2>
                    <p class="text-muted fs-5 mb-0">Empowering businesses and individuals with a smarter way to sell scrap.</p>
                </div>

                <!-- Left Image Area -->
                <div class="col-lg-4">
                    <div class="rounded-4 overflow-hidden shadow w-100" style="height: 100%; min-height: 400px;">
                        <img src="{{ asset('testimonaial.png') }}" alt="Real Success Stories" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                    </div>
                </div>

                <!-- Right Content Area -->
                <div class="col-lg-8">
                    <div class="mb-5 d-none d-lg-block">
                        <h2 class="fw-bold mb-2" style="color: var(--primary-blue); font-size: 2.5rem;">Trusted by Our Customers</h2>
                        <p class="text-muted fs-5">Empowering businesses and individuals with a smarter way to sell scrap.</p>
                    </div>

                    <div class="row g-4">
                        <!-- Testimonial 1 -->
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 border-0 rounded-4 shadow-sm p-4" style="transition: transform 0.3s ease; position: relative; overflow: hidden; background-color: #ffffff;">
                                <!-- Watermark -->
                                <i class="fa-solid fa-quote-right" style="position: absolute; right: 10px; bottom: -10px; font-size: 8rem; color: rgba(46,125,50,0.06); z-index: 0; pointer-events: none;"></i>
                                <div style="position: relative; z-index: 1;">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle d-flex justify-content-center align-items-center text-white fw-bold me-3" style="width: 50px; height: 50px; flex-shrink: 0; background: var(--primary-blue);">JD</div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">John Doe</h6>
                                            <small class="text-muted">Retail Business Owner</small>
                                        </div>
                                    </div>
                                    <p class="card-text text-muted" style="font-size: 0.9rem;">"Scrap Daddy has completely changed the way we manage waste in our store. The pickup process is incredibly smooth, and the pricing is always transparent."</p>
                                    <div class="mt-auto pt-3 text-warning">
                                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 2 -->
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 border-0 rounded-4 shadow-sm p-4" style="transition: transform 0.3s ease; position: relative; overflow: hidden; background-color: #ffffff;">
                                <!-- Watermark -->
                                <i class="fa-solid fa-quote-right" style="position: absolute; right: 10px; bottom: -10px; font-size: 8rem; color: rgba(46,125,50,0.06); z-index: 0; pointer-events: none;"></i>
                                <div style="position: relative; z-index: 1;">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle d-flex justify-content-center align-items-center text-white fw-bold me-3" style="width: 50px; height: 50px; flex-shrink: 0; background: #0d2b4d;">AS</div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">Ahmed Sayed</h6>
                                            <small class="text-muted">Wholesale Trader</small>
                                        </div>
                                    </div>
                                    <p class="card-text text-muted" style="font-size: 0.9rem;">"We were looking for a trusted platform to sell bulk scrap, and they made everything simple. From scheduling to instant payment, highly reliable."</p>
                                    <div class="mt-auto pt-3 text-warning">
                                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 3 -->
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 border-0 rounded-4 shadow-sm p-4" style="transition: transform 0.3s ease; position: relative; overflow: hidden; background-color: #ffffff;">
                                <!-- Watermark -->
                                <i class="fa-solid fa-quote-right" style="position: absolute; right: 10px; bottom: -10px; font-size: 8rem; color: rgba(46,125,50,0.06); z-index: 0; pointer-events: none;"></i>
                                <div style="position: relative; z-index: 1;">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle d-flex justify-content-center align-items-center text-white fw-bold me-3" style="width: 50px; height: 50px; flex-shrink: 0; background: var(--primary-green);">MK</div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">Maria Khan</h6>
                                            <small class="text-muted">Individual Seller</small>
                                        </div>
                                    </div>
                                    <p class="card-text text-muted" style="font-size: 0.9rem;">"The platform helped me clear out years of household scrap quickly. The support team is always responsive whenever I need assistance."</p>
                                    <div class="mt-auto pt-3 text-warning">
                                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </section>
@endsection
