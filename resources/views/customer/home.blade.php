@extends('layouts.app')

@section('title', 'Customer Dashboard - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
@endpush

@section('content')

<div class="dashboard-container">
    <div class="row g-4">
        
        @include('partials.customer.sidebar')

        <!-- Main Content -->
        <div class="col-xl-9 col-lg-9">
            @php
                $user = auth()->user();
                $recentOrders = \App\Models\Order::with('subcategory')->where('user_uuid', $user->uuid)->latest()->take(5)->get();
                $totalPickups = \App\Models\Order::where('user_uuid', $user->uuid)->count();
                $rewardPoints = 150; // Mocked for now
            @endphp
            
            <!-- DESKTOP VIEW -->
            <div class="d-none d-lg-block">
            <!-- Hero Banner -->
            @include('partials.customer.hero-banner', [
                'title' => 'Hello ' . explode(' ', trim($user->full_name))[0] . '! 👋',
                'subtitle' => 'Thanks for being a part of a cleaner and greener tomorrow.'
            ])

            <!-- Stats Grid -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <div class="stat-card stat-green">
                        <div class="stat-icon"><i class="fa-solid fa-truck-fast"></i></div>
                        <div class="stat-info">
                            <h3>{{ $totalPickups }}</h3>
                            <p>Total Pickups</p>
                            <small>All time</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="stat-card stat-orange">
                        <div class="stat-icon"><i class="fa-solid fa-star"></i></div>
                        <div class="stat-info">
                            <h3>{{ $rewardPoints }}</h3>
                            <p>Reward Points</p>
                            <small>Available balance</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Pickups Horizontal List -->
            <div class="section-header">
                <h5>My Pickups</h5>
                <a href="{{ route('customer.orders') }}">View All Pickups <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            
            <div class="horizontal-scroll-container">
                @forelse($recentOrders as $order)
                
                @if(in_array($order->status, ['pending', 'accepted']))
                <!-- Active Tracker Card (Mockup Style) -->
                <div class="pickup-card border-0 text-white flex-shrink-0" style="background: linear-gradient(145deg, #1a1a1a, #2d2d2d); min-width: 320px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex gap-3 align-items-center">
                            <div style="color: #fbbc04;"><i class="fa-solid fa-paper-plane fs-4"></i></div>
                            <div>
                                <h6 class="mb-0 fw-bold fs-5 text-white">Request Submitted</h6>
                                <p class="mb-0 text-light" style="font-size: 0.8rem;">Pickup: <span class="fw-bold">{{ $order->pickup_date == date('Y-m-d') ? 'Today' : \Carbon\Carbon::parse($order->pickup_date)->format('d M') }}</span></p>
                            </div>
                        </div>
                        <a href="{{ route('customer.orders') }}" class="btn btn-sm text-white px-3" style="background: #2e7d32; border-radius: 20px;">View</a>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4 mt-4 text-secondary" style="font-size: 0.75rem;">
                        <span class="text-success fw-bold"><i class="fa-solid fa-check me-1"></i>Submitted <i class="fa-solid fa-chevron-right ms-1"></i></span>
                        <span class="text-warning fw-bold"><i class="fa-regular fa-clock me-1"></i>Confirmed <i class="fa-solid fa-chevron-right ms-1"></i></span>
                        <span>Assigned <i class="fa-solid fa-chevron-right ms-1"></i></span>
                        <span>Completed</span>
                    </div>

                    <div class="p-3 rounded-4 mb-3" style="background: rgba(255,255,255,0.05);">
                        <h6 class="fw-bold mb-2 text-white">Confirming your request...</h6>
                        <div class="progress mb-3" style="height: 4px; background: #444;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mb-0 text-secondary" style="font-size: 0.8rem;">Please wait for a while we are confirming your request. This may take 30 mins or more...</p>
                    </div>

                    <div class="text-center mt-3 border-top pt-3 border-secondary border-opacity-25">
                        <a href="#" class="text-light text-decoration-none" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-headset me-2"></i>Need Support? We are happy to help
                        </a>
                    </div>
                </div>
                @else
                <!-- Past Order Card -->
                <div class="pickup-card flex-shrink-0" style="min-width: 320px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex gap-2 align-items-center">
                            <div class="bg-light p-2 rounded"><i class="fa-solid fa-truck text-primary"></i></div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Tata Ace - Load Assist 🙋‍♂️</h6>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') }}, {{ $order->pickup_time }}</p>
                            </div>
                        </div>
                        <div class="fw-bold fs-5 text-dark">₹{{ $order->total_amount ?? '0' }} <i class="fa-solid fa-chevron-right fs-6 text-muted ms-1"></i></div>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-3">
                        <div class="d-flex gap-3 mb-3">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 10px;"><i class="fa-solid fa-arrow-up"></i></div>
                                <div class="border-start border-2 border-secondary border-opacity-25 flex-grow-1 my-1"></div>
                                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 10px;"><i class="fa-solid fa-arrow-down"></i></div>
                            </div>
                            <div class="d-flex flex-column justify-content-between w-100">
                                <div>
                                    <div class="text-muted" style="font-size: 0.75rem;">ScrapDaddy User &bull; +91 9876543210</div>
                                    <div class="text-dark text-truncate" style="font-size: 0.8rem; max-width: 200px;">{{ $order->pickup_location }}</div>
                                </div>
                                <div class="mt-3">
                                    <div class="text-muted" style="font-size: 0.75rem;">Scrapdaddy Hub</div>
                                    <div class="text-dark text-truncate" style="font-size: 0.8rem; max-width: 200px;">Veerannapalya Main Road, 65...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        @if($order->status == 'completed')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill"><i class="fa-solid fa-check-circle me-1"></i> COMPLETED</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill"><i class="fa-solid fa-xmark me-1"></i> CANCELLED</span>
                        @endif
                    </div>
                </div>
                @endif
                
                @empty
                <div class="w-100 text-center py-5 text-muted">
                    <i class="fa-solid fa-box-open fa-3x mb-3 opacity-50"></i>
                    <p>No recent pickups found. Schedule one today!</p>
                </div>
                @endforelse
            </div>

            <!-- Quick Actions -->
            <div class="section-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="quick-actions-row">
                <div class="quick-action-card">
                    <div class="quick-action-icon bg-success bg-opacity-10 text-success"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <h6>Track Pickup</h6>
                        <p>Track your pickup status</p>
                    </div>
                </div>
                <div class="quick-action-card">
                    <div class="quick-action-icon bg-success bg-opacity-10 text-success"><i class="fa-solid fa-gift"></i></div>
                    <div>
                        <h6>Reward Store</h6>
                        <p>Redeem your points</p>
                    </div>
                </div>
                <div class="quick-action-card">
                    <div class="quick-action-icon bg-success bg-opacity-10 text-success"><i class="fa-solid fa-users"></i></div>
                    <div>
                        <h6>Refer & Earn</h6>
                        <p>Invite friends & earn</p>
                    </div>
                </div>
                <div class="quick-action-card" onclick="window.location.href='{{ route('page.show', 'help-and-support') }}'">
                    <div class="quick-action-icon bg-success bg-opacity-10 text-success"><i class="fa-solid fa-headset"></i></div>
                    <div>
                        <h6>Help Center</h6>
                        <p>Get support</p>
                    </div>
                </div>
            </div>
            </div> <!-- End Desktop View -->

            <!-- MOBILE VIEW -->
            <div class="d-block d-lg-none pb-5" style="background-color: #f8f9fa; min-height: 100vh; margin: -30px -15px 0 -15px;">
                <!-- Blue Header Section -->
                <div class="pt-4 pb-5 px-3 mb-4" style="background: linear-gradient(135deg, #1a4275 0%, #295b9d 100%); border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                    <!-- Header Section -->
                    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="position-relative">
                                <img src="https://img.freepik.com/free-psd/3d-illustration-person-with-sunglasses_23-2149436188.jpg?w=250" alt="Avatar" class="rounded-circle border border-2 border-white shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle" style="width: 14px; height: 14px;"></span>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-white" style="font-size: 1.1rem;">Hello {{ explode(' ', trim($user->full_name))[0] }}</h5>
                                <small class="text-white-50" style="font-size: 0.75rem;">Recycle Today, Earn Tomorrow ♻</small>
                            </div>
                        </div>
                    </div>

                    <!-- Location Box -->
                    <div class="rounded-4 p-3 shadow-sm border border-white border-opacity-10 d-flex align-items-center gap-3" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
                        <div class="text-white"><i class="fa-solid fa-location-dot fs-5"></i></div>
                        <div>
                            <h6 class="mb-0 fw-bold text-white" style="font-size: 0.95rem;" id="locationTextMobile2">{{ $user->location ?? 'Detecting Location...' }}</h6>
                            <small class="text-white-50" style="font-size: 0.75rem;">{{ $user->location ? 'Your saved location' : 'Finding your location' }}</small>
                        </div>
                    </div>
                </div>

                <div class="px-3" style="margin-top: -30px; position: relative; z-index: 10;">



                <!-- Dynamic Pickups Section (Horizontally Scrollable) -->
                @php
                    $activeOrders = $recentOrders->whereIn('status', ['pending', 'accepted', 'assigned']);
                @endphp
                @if($activeOrders->count() > 0)
                <div class="d-flex overflow-x-auto gap-3 pb-3 mb-4" style="scroll-snap-type: x mandatory; scrollbar-width: none; -ms-overflow-style: none;">
                    @foreach($activeOrders as $order)
                    <div class="bg-white rounded-4 p-4 shadow-sm border border-light flex-shrink-0" style="width: 100%; min-width: 320px; scroll-snap-align: start;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fa-solid fa-truck"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-dark">
                                    @if($order->status == 'completed')
                                        Pickup Completed
                                    @elseif($order->status == 'cancelled')
                                        Pickup Cancelled
                                    @else
                                        Pickup Requested
                                    @endif
                                </h6>
                            </div>
                            <span class="badge {{ $order->status == 'pending' ? 'bg-warning' : ($order->status == 'completed' ? 'bg-success' : ($order->status == 'cancelled' ? 'bg-danger' : 'bg-primary')) }} rounded-pill" style="font-size: 0.7rem;">{{ ucfirst($order->status) }}</span>
                        </div>

                        <div class="position-relative mb-5 px-3">
                            @php
                                $progress = 0;
                                if($order->status == 'pending') $progress = 25;
                                elseif($order->status == 'accepted') $progress = 50;
                                elseif($order->status == 'assigned') $progress = 75;
                                elseif($order->status == 'completed') $progress = 100;
                            @endphp
                            <div class="progress position-absolute" style="height: 4px; top: 50%; left: 10%; right: 10%; transform: translateY(-50%); z-index: 1;">
                                <div class="progress-bar {{ $order->status == 'cancelled' ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ $order->status == 'cancelled' ? '100' : $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                                <!-- Submitted -->
                                <div class="text-center">
                                    <div class="rounded-circle bg-white border border-2 {{ $order->status == 'cancelled' ? 'border-danger' : 'border-success' }} d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 30px; height: 30px;">
                                        <i class="fa-solid fa-check {{ $order->status == 'cancelled' ? 'text-danger' : 'text-success' }} fs-6"></i>
                                    </div>
                                    <div class="{{ $order->status == 'cancelled' ? 'text-danger' : 'text-success' }} fw-bold" style="font-size: 0.7rem;">Submitted</div>
                                    <div class="text-muted" style="font-size: 0.6rem;">{{ $order->created_at ? $order->created_at->format('d M, h:i A') : 'N/A' }}</div>
                                </div>
                                <!-- Confirmed -->
                                <div class="text-center">
                                    <div class="rounded-circle bg-white {{ in_array($order->status, ['accepted', 'assigned', 'completed']) ? 'border border-2 border-success' : ($order->status == 'pending' ? 'border border-2 border-warning' : ($order->status == 'cancelled' ? 'border border-2 border-danger' : 'border border-2 border-secondary')) }} d-flex align-items-center justify-content-center mx-auto mb-2 position-relative" style="width: 30px; height: 30px;">
                                        <i class="fa-solid fa-truck {{ in_array($order->status, ['accepted', 'assigned', 'completed']) ? 'text-success' : ($order->status == 'pending' ? 'text-warning' : ($order->status == 'cancelled' ? 'text-danger' : 'text-muted')) }} fs-5"></i>
                                    </div>
                                    <div class="{{ in_array($order->status, ['accepted', 'assigned', 'completed']) ? 'text-success' : ($order->status == 'pending' ? 'text-warning' : ($order->status == 'cancelled' ? 'text-danger' : 'text-secondary')) }} fw-bold" style="font-size: 0.7rem;">Confirmed</div>
                                    <div class="text-muted" style="font-size: 0.6rem;">{{ $order->status == 'pending' ? 'Waiting confirm' : 'Confirmed' }}</div>
                                </div>
                                <!-- Assigned -->
                                <div class="text-center">
                                    <div class="rounded-circle {{ in_array($order->status, ['assigned', 'completed']) ? 'bg-white border border-2 border-success' : ($order->status == 'cancelled' ? 'bg-white border border-2 border-danger' : 'bg-light') }} d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 30px; height: 30px;">
                                        <i class="fa-solid fa-user {{ in_array($order->status, ['assigned', 'completed']) ? 'text-success' : ($order->status == 'cancelled' ? 'text-danger' : 'text-muted') }} fs-6"></i>
                                    </div>
                                    <div class="{{ in_array($order->status, ['assigned', 'completed']) ? 'text-success' : ($order->status == 'cancelled' ? 'text-danger' : 'text-secondary') }} fw-bold" style="font-size: 0.7rem;">Assigned</div>
                                    <div class="text-muted" style="font-size: 0.6rem;">{{ in_array($order->status, ['assigned', 'completed']) ? 'Driver assigned' : 'Pending' }}</div>
                                </div>
                                <!-- Completed -->
                                <div class="text-center">
                                    <div class="rounded-circle {{ $order->status == 'completed' ? 'bg-white border border-2 border-success' : ($order->status == 'cancelled' ? 'bg-white border border-2 border-danger' : 'bg-light') }} d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 30px; height: 30px;">
                                        <i class="fa-solid fa-flag {{ $order->status == 'completed' ? 'text-success' : ($order->status == 'cancelled' ? 'text-danger' : 'text-muted') }} fs-6"></i>
                                    </div>
                                    <div class="{{ $order->status == 'completed' ? 'text-success' : ($order->status == 'cancelled' ? 'text-danger' : 'text-secondary') }} fw-bold" style="font-size: 0.7rem;">Completed</div>
                                    <div class="text-muted" style="font-size: 0.6rem;">{{ $order->status == 'completed' ? 'Delivered' : ($order->status == 'cancelled' ? 'Cancelled' : 'Pending') }}</div>
                                </div>
                            </div>
                        </div>

                        @if($order->status == 'pending')
                        <div class="rounded-4 p-3" style="background-color: #f5f8ff;">
                            <h6 class="fw-bold text-dark mb-2">Confirming your request...</h6>
                            <p class="text-muted mb-0" style="font-size: 0.8rem; line-height: 1.4;">
                                Please wait while we confirm your pickup request. This usually takes 15-30 minutes.
                            </p>
                        </div>
                        @elseif($order->status == 'completed')
                        <div class="rounded-4 p-3 bg-success bg-opacity-10 border border-success">
                            <h6 class="fw-bold text-success mb-1">Pickup Successful!</h6>
                            <p class="text-success mb-0" style="font-size: 0.8rem;">You earned rewards for this pickup.</p>
                        </div>
                        @elseif($order->status == 'cancelled')
                        <div class="rounded-4 p-3 bg-danger bg-opacity-10 border border-danger">
                            <h6 class="fw-bold text-danger mb-1">Pickup Cancelled</h6>
                            <p class="text-danger mb-0" style="font-size: 0.8rem;">This pickup request was cancelled.</p>
                        </div>
                        @else
                        <div class="rounded-4 p-3" style="background-color: #f8f9fa;">
                            <h6 class="fw-bold text-dark mb-1">Scheduled for Pickup</h6>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">Your pickup is scheduled for {{ \Carbon\Carbon::parse($order->pickup_date)->format('d M') }}, {{ $order->pickup_time }}.</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                <!-- Hide scrollbar for webkit browsers via style block injected quickly -->
                <style>
                    .overflow-x-auto::-webkit-scrollbar { display: none; }
                </style>
                @else
                <!-- Fallback Empty State -->
                <div class="bg-white rounded-4 p-4 mb-4 shadow-sm border border-light text-center">
                    <i class="fa-solid fa-box-open text-muted fs-1 mb-3 opacity-50"></i>
                    <h6 class="fw-bold text-dark">No Recent Pickups</h6>
                    <p class="text-muted small mb-3">You don't have any active pickup requests right now.</p>
                    <a href="{{ route('customer.orders.create') ?? '#' }}" class="btn rounded-pill text-white fw-bold px-4" style="background: var(--primary-green, #1b5e20); font-size: 0.85rem;">Schedule a Pickup</a>
                </div>
                @endif

                <!-- Dynamic Banners -->
                @php
                    $banners = \App\Models\Banner::where('status', 1)->get();
                @endphp
                
                @if($banners->count() > 0)
                <div id="mobileBannerCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                    <div class="carousel-indicators" style="bottom: -35px;">
                        @foreach($banners as $index => $banner)
                            <button type="button" data-bs-target="#mobileBannerCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : 'false' }}" style="width: 8px; height: 8px; border-radius: 50%; background-color: #2e7d32;"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner rounded-4 shadow-sm" style="background: #f4f3ed;">
                        @foreach($banners as $index => $banner)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <div class="position-relative" style="height: 200px;">
                                @if($banner->uploads)
                                    <img src="{{ asset($banner->uploads) }}" class="d-block w-100 h-100" style="object-fit: cover;" alt="{{ $banner->title }}">
                                    <!-- Fallback overlay text if image exists but they want text on it -->
                                    <div class="position-absolute top-0 start-0 w-100 h-100 p-4" style="background: linear-gradient(to right, rgba(255,255,255,0.9) 30%, transparent);">
                                        <h3 class="fw-bolder mb-2" style="color: #2e3025; font-size: 1.5rem; line-height: 1.2;">{{ $banner->title }}</h3>
                                        @if($banner->short_description)
                                            <p class="text-muted small mb-3" style="max-width: 60%;">{{ $banner->short_description }}</p>
                                        @endif
                                        <button class="btn btn-sm rounded-pill text-white px-3 fw-bold" style="background: #5b7b3b; font-size: 0.75rem;">
                                            Recycle Now <i class="fa-solid fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                @else
                                    <div class="p-4 w-75 position-relative" style="z-index: 2;">
                                        <h3 class="fw-bolder mb-3" style="color: #2e3025; font-size: 1.5rem; line-height: 1.2;">
                                            {{ $banner->title }}
                                        </h3>
                                        @if($banner->short_description)
                                            <p class="text-muted small mb-3">{{ $banner->short_description }}</p>
                                        @endif
                                        <button class="btn btn-sm rounded-pill text-white px-3 fw-bold" style="background: #5b7b3b; font-size: 0.75rem;">
                                            Recycle Now <i class="fa-solid fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                    <!-- Absolute positioned decoration elements -->
                                    <i class="fa-solid fa-coins text-warning position-absolute" style="font-size: 1.5rem; top: 15%; left: 10%; opacity: 0.8;"></i>
                                    <i class="fa-solid fa-coins text-warning position-absolute" style="font-size: 2rem; top: 10%; right: 40%; opacity: 0.9;"></i>
                                    <div class="position-absolute" style="right: 5%; bottom: -10%; z-index: 1; opacity: 0.9;">
                                        <i class="fa-solid fa-box-open text-muted" style="font-size: 8rem; opacity: 0.1;"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <!-- Static Fallback Promotional Banner -->
                <div class="rounded-4 overflow-hidden position-relative mb-4" style="background: #f4f3ed; height: 200px;">
                    <div class="p-4 w-75 position-relative" style="z-index: 2;">
                        <h3 class="fw-bolder mb-3" style="color: #2e3025; font-size: 1.5rem; line-height: 1.2;">
                            Turn Your Scrap<br><span class="text-success">Into Rewards</span>
                        </h3>
                        <button class="btn btn-sm rounded-pill text-white px-3 fw-bold" style="background: #5b7b3b; font-size: 0.75rem;">
                            Recycle Now <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                    <!-- Absolute positioned decoration elements to mimic the mockup -->
                    <i class="fa-solid fa-coins text-warning position-absolute" style="font-size: 1.5rem; top: 15%; left: 10%; opacity: 0.8;"></i>
                    <i class="fa-solid fa-coins text-warning position-absolute" style="font-size: 2rem; top: 10%; right: 40%; opacity: 0.9;"></i>
                    <div class="position-absolute" style="right: 5%; bottom: -10%; z-index: 1; opacity: 0.9;">
                        <i class="fa-solid fa-box-open text-muted" style="font-size: 8rem; opacity: 0.1;"></i>
                    </div>
                </div>
                @endif

                <!-- Dynamic Categories -->
                @php
                    $mobileCategories = \App\Models\Category::where('status', 1)->take(6)->get();
                @endphp
                <div class="d-flex justify-content-between align-items-center mb-3 mt-5">
                    <h5 class="fw-bold text-dark mb-0">Categories</h5>
                    <a href="/explore-categories" class="text-success text-decoration-none fw-bold" style="font-size: 0.85rem;">View All <i class="fa-solid fa-chevron-right ms-1" style="font-size: 0.7rem;"></i></a>
                </div>
                <div class="row g-3 pb-4">
                    @forelse($mobileCategories as $category)
                    <div class="col-4">
                        <a href="{{ route('category.show', $category->uuid) }}" class="text-decoration-none">
                            <div class="bg-white p-2 rounded-4 shadow-sm text-center border border-light h-100 d-flex flex-column justify-content-center align-items-center transition-all" style="min-height: 110px;">
                                @if($category->image)
                                    <img src="{{ asset($category->image) }}" alt="{{ $category->title }}" style="width: 50px; height: 50px; object-fit: contain; margin-bottom: 12px;">
                                @else
                                    <i class="fa-solid fa-box-open text-muted fs-2 mb-2"></i>
                                @endif
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.8rem; line-height: 1.2;">{{ $category->title }}</h6>
                            </div>
                        </a>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted">
                        <small>No categories available.</small>
                    </div>
                    @endforelse
                </div>

                <!-- How it works -->
                <div class="mb-4 mt-2">
                    <h5 class="fw-bold text-dark mb-3">How it works</h5>
                    <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-4 shadow-sm border border-light">
                        <!-- Step 1 -->
                        <div class="text-center position-relative">
                            <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm" style="width: 45px; height: 45px;">
                                <i class="fa-solid fa-location-dot text-success fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark" style="font-size: 0.65rem;">Location</div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-muted" style="font-size: 0.6rem;"></i>
                        <!-- Step 2 -->
                        <div class="text-center position-relative">
                            <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm" style="width: 45px; height: 45px;">
                                <i class="fa-solid fa-sack-dollar text-success fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark" style="font-size: 0.65rem;">Add Scrap</div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-muted" style="font-size: 0.6rem;"></i>
                        <!-- Step 3 -->
                        <div class="text-center position-relative">
                            <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm" style="width: 45px; height: 45px;">
                                <i class="fa-solid fa-truck text-success fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark" style="font-size: 0.65rem;">Schedule</div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-muted" style="font-size: 0.6rem;"></i>
                        <!-- Step 4 -->
                        <div class="text-center position-relative">
                            <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm" style="width: 45px; height: 45px;">
                                <i class="fa-solid fa-coins text-warning fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark" style="font-size: 0.65rem;">Earn Rewards</div>
                        </div>
                    </div>
                </div>

                <!-- Refer a Friend -->
                <div class="bg-white p-3 rounded-4 shadow-sm border border-light d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-users text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 1rem;">Refer a Friend</h6>
                            <small class="text-muted">Invite friends & earn rewards</small>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-success fw-bold"></i>
                </div>
                </div> <!-- End Main mobile content wrapper -->
            </div> <!-- End Mobile View -->

        <!-- Right Sidebar Removed -->

    </div>
</div>
@endsection