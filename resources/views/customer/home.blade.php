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
                $user = auth()->user() ?? \App\Models\User::first() ?? new \App\Models\User(['full_name' => 'Shaik Afzal', 'phone_number' => '+91 9876543210']);
                $recentOrders = \App\Models\Order::with('subcategory')->where('user_uuid', $user->uuid)->latest()->take(5)->get();
                $totalPickups = \App\Models\Order::where('user_uuid', $user->uuid)->count();
                $rewardPoints = 150; // Mocked for now
            @endphp
            
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('customer.orders.create') }}" class="btn btn-success px-4 py-2 shadow-sm rounded-pill fw-bold"><i class="fa-solid fa-plus me-2"></i>New Pickup</a>
            </div>
            
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

        </div>

        <!-- Right Sidebar Removed -->

    </div>
</div>
@endsection