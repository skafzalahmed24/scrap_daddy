@extends('layouts.app')

@section('title', 'My Pickups - Scrap Daddy')

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
                $ordersList = \App\Models\Order::with('subcategory')->where('user_uuid', $user->uuid)->latest()->get();
                
                $pendingPayments = $ordersList->where('status', 'completed')->where('payment_status', 'pending');
                $activeOrders = $ordersList->whereIn('status', ['pending', 'accepted']);
                $otherOrders = $ordersList->whereNotIn('status', ['pending', 'accepted'])->reject(function($order) {
                    return $order->status == 'completed' && $order->payment_status == 'pending';
                });
                
                function getStatusDetails($order) {
                    if ($order->status == 'pending') {
                        return ['badgeClass' => 'b-orange', 'badgeText' => 'Order Placed', 'textClass' => 't-orange', 'icon' => 'fa-clock', 'subText' => 'Awaiting Admin'];
                    } elseif ($order->status == 'accepted') {
                        return ['badgeClass' => 'b-blue', 'badgeText' => 'Accepted', 'textClass' => 't-blue', 'icon' => 'fa-truck-fast', 'subText' => 'Team on the way'];
                    } elseif ($order->status == 'completed') {
                        if ($order->payment_status == 'pending') {
                            return ['badgeClass' => 'b-green', 'badgeText' => 'Completed', 'textClass' => 't-green', 'icon' => 'fa-check', 'subText' => 'Payment Pending'];
                        }
                        return ['badgeClass' => 'b-green', 'badgeText' => 'Completed', 'textClass' => 't-green', 'icon' => 'fa-check-circle', 'subText' => 'Paid & Closed'];
                    } else {
                        return ['badgeClass' => 'b-red', 'badgeText' => 'Cancelled', 'textClass' => 't-red', 'icon' => 'fa-xmark', 'subText' => 'Order Cancelled'];
                    }
                }
            @endphp
            
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('customer.orders.create') }}" class="btn btn-success px-4 py-2 shadow-sm rounded-pill fw-bold"><i class="fa-solid fa-plus me-2"></i>New Pickup</a>
            </div>

            <!-- Hero Banner -->
            @include('partials.customer.hero-banner', [
                'title' => 'My Pickups',
                'subtitle' => 'Track your current pickups and view your order history.'
            ])

            <div class="middle-card">
                
                <!-- Filter Buttons -->
                <div class="filter-btn-group">
                    <button class="filter-btn active" data-filter="all">
                        <i class="fa-solid fa-list-ul text-secondary"></i> All Pickups
                    </button>
                    <button class="filter-btn border-start border-end" style="border-radius: 0;" data-filter="action-required">
                        <i class="fa-solid fa-triangle-exclamation text-warning"></i> Action Required
                    </button>
                    <button class="filter-btn" data-filter="active-pickups">
                        <i class="fa-solid fa-paper-plane text-success"></i> Active Pickups
                    </button>
                    <button class="filter-btn border-start" style="border-radius: 0;" data-filter="other-pickups">
                        <i class="fa-solid fa-box text-purple" style="color: #6f42c1;"></i> Other Pickups
                    </button>
                </div>
                
                @if(session('success'))
                    <!-- Toast Notification -->
                    <div class="toast-container position-fixed top-0 end-0 p-3 mt-5" style="z-index: 1060;">
                        <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="6000">
                            <div class="d-flex">
                                <div class="toast-body fw-medium px-3 py-2" style="font-size: 0.95rem;">
                                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                                </div>
                                <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Action Required / Pending Payments -->
                <div class="pickup-section" id="section-action-required">
                @if($pendingPayments->isNotEmpty())
                <h5 class="section-header-h5 text-danger"><i class="fa-solid fa-circle-exclamation me-2"></i>Action Required</h5>
                <div class="mb-5">
                    @foreach($pendingPayments as $order)
                        @php $det = getStatusDetails($order); @endphp
                        <!-- Action Required Card Style (Light Theme) -->
                        <div class="card border-0 mb-4 bg-white" style="box-shadow: 0 4px 20px rgba(0,0,0,0.05); border-radius: 12px; border-left: 6px solid #fd7e14 !important;">
                            <div class="card-body p-4">
                                <!-- Header -->
                                <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                                    <div class="d-flex gap-3 align-items-center">
                                        <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(253, 126, 20, 0.1); color: #fd7e14; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-file-invoice-dollar fs-4"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1 fw-bold" style="color: #0d2b4d;">Payment Pending</h5>
                                            <p class="mb-0 text-muted" style="font-size: 0.9rem;">Pickup: <span class="fw-bold" style="color: #fd7e14;">{{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('D, d M') : '--' }}</span></p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('customer.payment.initiate', $order->id) }}" class="btn btn-sm text-white fw-bold d-flex align-items-center gap-2 px-3 py-2 shadow-sm" style="background: #fd7e14; border-radius: 8px;">
                                            <i class="fa-solid fa-credit-card"></i> Pay ₹{{ number_format($order->total_amount, 2) }}
                                        </a>
                                        <button type="button" class="btn btn-sm bg-white fw-bold d-flex align-items-center gap-2 px-3 py-2 shadow-sm" style="color: #2e7d32; border: 1px solid rgba(46, 125, 50, 0.3); border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#orderDetailsModal-{{ $order->id }}">
                                            <i class="fa-regular fa-eye"></i> View
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Info Box -->
                                <div class="p-3 rounded-4 mb-3 d-flex align-items-center gap-3" style="background: rgba(253, 126, 20, 0.05); border: 1px solid rgba(253, 126, 20, 0.1);">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 45px; height: 45px; background: #fd7e14; color: white;">
                                        <i class="fa-solid fa-circle-exclamation fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1 text-dark">Action Required</h6>
                                        <p class="mb-0 text-muted" style="font-size: 0.85rem;">Your pickup is complete, but payment is pending. Please complete your payment of <strong class="text-danger">₹{{ number_format($order->total_amount, 2) }}</strong> to close the request.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
                </div>

                <!-- Active Pickups -->
                <div class="pickup-section" id="section-active-pickups">
                <h5 class="section-header-h5">Active Pickups</h5>
                <div class="mb-5">
                    @forelse($activeOrders as $order)
                        @php $det = getStatusDetails($order); @endphp
                        @php 
                            $isAccepted = $order->status == 'accepted';
                            $themeColor = $isAccepted ? '#4285f4' : '#2e7d32'; 
                            $themeBg = $isAccepted ? 'rgba(66, 133, 244, 0.1)' : 'rgba(46, 125, 50, 0.1)';
                        @endphp
                        <!-- Active Tracker Card (Light Theme) -->
                        <div class="card border-0 mb-4 bg-white" style="box-shadow: 0 4px 20px rgba(0,0,0,0.05); border-radius: 12px; border-left: 6px solid {{ $themeColor }} !important;">
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
                                        <button type="button" class="btn btn-sm bg-white fw-bold d-flex align-items-center gap-2 px-3 py-2 shadow-sm" style="color: #2e7d32; border: 1px solid rgba(46, 125, 50, 0.3); border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#orderDetailsModal-{{ $order->id }}">
                                            <i class="fa-regular fa-eye"></i> View
                                        </button>
                                        <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this pickup?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm bg-white fw-bold d-flex align-items-center gap-2 px-3 py-2 shadow-sm" style="color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.3); border-radius: 8px;">
                                                <i class="fa-solid fa-xmark"></i> Cancel
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Stepper -->
                                <div class="overflow-auto pb-3 mb-4 mt-4" style="scrollbar-width: none;">
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
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($order->created_at)->format('d M, h:i A') }}</small>
                                        </div>

                                        <!-- Step 2: Confirmed -->
                                        <div class="position-relative" style="z-index: 2; width: 25%;">
                                            <div class="mx-auto mb-2 d-flex align-items-center justify-content-center bg-white" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid {{ $isAccepted ? '#2e7d32' : '#fbbc04' }}; color: {{ $isAccepted ? '#2e7d32' : '#fbbc04' }}; background: {{ $isAccepted ? '#e8f5e9' : '#fff8e1' }} !important;">
                                                <i class="fa-{{ $isAccepted ? 'solid fa-check' : 'regular fa-clock' }} fs-6"></i>
                                            </div>
                                            <h6 class="fw-bold mb-1" style="font-size: 0.85rem; color: {{ $isAccepted ? '#2e7d32' : '#fbbc04' }};">Confirmed</h6>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $isAccepted ? \Carbon\Carbon::parse($order->updated_at)->format('d M, h:i A') : 'Waiting confirmation' }}</small>
                                        </div>

                                        <!-- Step 3: Assigned -->
                                        <div class="position-relative" style="z-index: 2; width: 25%;">
                                            <div class="mx-auto mb-2 d-flex align-items-center justify-content-center bg-white" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #e0e0e0; color: #9e9e9e; background: #f5f5f5 !important;">
                                                <i class="fa-solid fa-user fs-6"></i>
                                            </div>
                                            <h6 class="fw-bold mb-1" style="font-size: 0.85rem; color: #6c757d;">Assigned</h6>
                                            <small class="text-muted" style="font-size: 0.75rem;">Pending</small>
                                        </div>

                                        <!-- Step 4: Completed -->
                                        <div class="position-relative" style="z-index: 2; width: 25%;">
                                            <div class="mx-auto mb-2 d-flex align-items-center justify-content-center bg-white" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #e0e0e0; color: #9e9e9e; background: #f5f5f5 !important;">
                                                <i class="fa-solid fa-flag fs-6"></i>
                                            </div>
                                            <h6 class="fw-bold mb-1" style="font-size: 0.85rem; color: #6c757d;">Completed</h6>
                                            <small class="text-muted" style="font-size: 0.75rem;">Pending</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Box -->
                                @php $progressWidth = $isAccepted ? 75 : 50; @endphp
                                <div class="p-4 rounded-4 mb-4" style="background: rgba(46, 125, 50, 0.05); border: 1px solid rgba(46, 125, 50, 0.1);">
                                    <h6 class="fw-bold mb-4 text-dark text-center">
                                        {{ $isAccepted ? 'Team is on the way...' : 'Confirming your request...' }}
                                    </h6>
                                    
                                    <div class="position-relative w-100" style="margin-bottom: 25px;">
                                        <!-- Moving Truck on Progress Bar -->
                                        <div class="position-absolute" style="top: -28px; left: {{ $progressWidth }}%; transform: translateX(-50%); z-index: 2; transition: left 1s ease-in-out;">
                                            <img src="{{ asset('icons8-ruck.gif') }}" alt="truck" style="width: 48px; height: 48px; object-fit: contain;">
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
                    @empty
                        <div class="text-center py-4 text-muted border rounded-4 bg-light">
                            <i class="fa-solid fa-clipboard-check fa-3x mb-3 opacity-25"></i>
                            <p>No active pickups.</p>
                        </div>
                    @endforelse
                </div>
                </div>

                <!-- Other Pickups -->
                <div class="pickup-section" id="section-other-pickups">
                <h5 class="section-header-h5">Other Pickups</h5>
                <div class="mb-3">
                    @forelse($otherOrders as $order)
                        @php $det = getStatusDetails($order); @endphp
                        
                        @if($order->status == 'cancelled')
                        <!-- Cancelled Card Style (Light Theme) -->
                        <div class="card border-0 mb-4 bg-white" style="box-shadow: 0 4px 20px rgba(0,0,0,0.05); border-radius: 12px; border-left: 6px solid #dc3545 !important;">
                            <div class="card-body p-4 pb-2">
                                <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                                    <div class="d-flex gap-3 align-items-center">
                                        <div style="width: 50px; height: 50px; border-radius: 50%; background: #dc3545; color: white; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-xmark fs-4"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1 fw-bold" style="color: #0d2b4d;">Request Cancelled</h5>
                                            <p class="mb-0 text-muted" style="font-size: 0.9rem;">Pickup: <span class="fw-bold" style="color: #dc3545;">{{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('D, d M') : '--' }}</span></p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('customer.orders.create') }}" class="btn btn-sm text-white fw-bold d-flex align-items-center gap-2 px-3 py-2 shadow-sm" style="background: #dc3545; border-radius: 8px;">
                                            <i class="fa-solid fa-arrow-rotate-right"></i> Re-request
                                        </a>
                                    </div>
                                </div>
                                
                                <hr class="opacity-10 mb-4" style="border-top: 1px dashed #e0e0e0;">
                                
                                <div class="p-3 rounded-3 mb-4" style="background: #fff5f5; border: 1px solid #ffcccc;">
                                    <p class="mb-0 text-danger d-flex align-items-center" style="font-size: 0.95rem;">
                                        <i class="fa-solid fa-circle-info me-3 fs-5"></i>
                                        You cancelled the pick-up request.
                                    </p>
                                </div>

                                <div class="text-center mt-3 pt-3 mb-3">
                                    <a href="{{ route('page.show', 'help-and-support') }}" class="text-muted text-decoration-none fw-medium" style="font-size: 0.95rem;">
                                        <i class="fa-solid fa-headset me-2" style="color: #dc3545;"></i>Need Support? We are happy to help
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Info tip box -->
                        <div class="p-3 rounded-3 mb-4 d-flex align-items-center gap-3" style="background: #f8fbff; border: 1px solid #e2eaf7;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #e2eaf7; color: #4285f4;">
                                <i class="fa-regular fa-lightbulb"></i>
                            </div>
                            <p class="mb-0 text-dark" style="font-size: 0.9rem;">
                                <span class="fw-bold" style="color: #0d2b4d;">Tip:</span> You can re-request a pickup anytime. We'll prioritize your request.
                            </p>
                        </div>
                        @else
                        <!-- Completed Card Style (Light Theme) -->
                        <div class="card border-0 mb-4 bg-white" style="box-shadow: 0 4px 20px rgba(0,0,0,0.05); border-radius: 12px; border-left: 6px solid #2e7d32 !important;">
                            <div class="card-body p-4">
                                <!-- Header -->
                                <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                                    <div class="d-flex gap-3 align-items-center">
                                        <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(46, 125, 50, 0.1); color: #2e7d32; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-check-circle fs-4"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1 fw-bold" style="color: #0d2b4d;">Request Completed</h5>
                                            <p class="mb-0 text-muted" style="font-size: 0.9rem;">Pickup: <span class="fw-bold" style="color: #2e7d32;">{{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('D, d M') : '--' }}</span></p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        @if($order->payment_status == 'pending')
                                            <a href="{{ route('customer.payment.initiate', $order->id) }}" class="btn btn-sm text-white fw-bold d-flex align-items-center gap-2 px-3 py-2 shadow-sm" style="background: #2e7d32; border-radius: 8px;">
                                                <i class="fa-solid fa-credit-card"></i> Pay Now
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-sm bg-white fw-bold d-flex align-items-center gap-2 px-3 py-2 shadow-sm" style="color: #2e7d32; border: 1px solid rgba(46, 125, 50, 0.3); border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#orderDetailsModal-{{ $order->id }}">
                                            <i class="fa-regular fa-eye"></i> View Details
                                        </button>
                                    </div>
                                </div>

                                <!-- Stepper for Completed -->
                                <div class="overflow-auto pb-3 mb-4 mt-4" style="scrollbar-width: none;">
                                    <div class="d-flex justify-content-between position-relative text-center" style="min-width: 450px;">
                                        <!-- connecting lines (ALL GREEN) -->
                                        <div class="position-absolute" style="top: 15px; left: 10%; right: 10%; height: 2px; z-index: 1;">
                                            <div class="d-flex w-100 h-100">
                                                <div style="flex: 1; border-top: 2px solid #2e7d32;"></div>
                                                <div style="flex: 1; border-top: 2px solid #2e7d32;"></div>
                                                <div style="flex: 1; border-top: 2px solid #2e7d32;"></div>
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
                                            <div class="mx-auto mb-2 d-flex align-items-center justify-content-center bg-white" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #2e7d32; color: #2e7d32; background: #e8f5e9 !important;">
                                                <i class="fa-solid fa-check fs-6"></i>
                                            </div>
                                            <h6 class="fw-bold mb-1" style="font-size: 0.85rem; color: #2e7d32;">Confirmed</h6>
                                        </div>
                                        <!-- Step 3: Assigned -->
                                        <div class="position-relative" style="z-index: 2; width: 25%;">
                                            <div class="mx-auto mb-2 d-flex align-items-center justify-content-center bg-white" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #2e7d32; color: #2e7d32; background: #e8f5e9 !important;">
                                                <i class="fa-solid fa-check fs-6"></i>
                                            </div>
                                            <h6 class="fw-bold mb-1" style="font-size: 0.85rem; color: #2e7d32;">Assigned</h6>
                                        </div>
                                        <!-- Step 4: Completed -->
                                        <div class="position-relative" style="z-index: 2; width: 25%;">
                                            <div class="mx-auto mb-2 d-flex align-items-center justify-content-center bg-white" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #2e7d32; color: #2e7d32; background: #e8f5e9 !important;">
                                                <i class="fa-solid fa-flag fs-6"></i>
                                            </div>
                                            <h6 class="fw-bold mb-1" style="font-size: 0.85rem; color: #2e7d32;">Completed</h6>
                                        </div>
                                    </div>
                                </div>

                                <!-- Success Box -->
                                <div class="p-3 rounded-4 mb-3 d-flex align-items-center gap-3" style="background: rgba(46, 125, 50, 0.05); border: 1px solid rgba(46, 125, 50, 0.1);">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 45px; height: 45px; background: #2e7d32; color: white;">
                                        <i class="fa-solid fa-clipboard-check fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">Successfully Completed!</h6>
                                        <p class="mb-0 text-muted" style="font-size: 0.85rem;">Your pickup request was completed successfully. Thank you for choosing Scrap Daddy!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @empty
                        <div class="text-center py-4 text-muted border rounded-4 bg-light">
                            <i class="fa-solid fa-box-archive fa-3x mb-3 opacity-25"></i>
                            <p>No past pickups history.</p>
                        </div>
                    @endforelse
                </div>
                </div>

            </div>
        </div>

        <!-- Right Sidebar Removed -->
    </div>
</div>

<!-- Order Detail Modals -->
@foreach($orders as $order)
<div class="modal fade" id="orderDetailsModal-{{ $order->id }}" tabindex="-1" aria-labelledby="orderDetailsModalLabel-{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="orderDetailsModalLabel-{{ $order->id }}" style="color: var(--primary-blue, #0d2b4d);">Order #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @php $det = getStatusDetails($order); @endphp
                <div class="mb-4">
                    <span class="badge {{ $det['badgeClass'] }} px-3 py-2 fs-6 rounded-pill"><i class="fa-solid {{ $det['icon'] }} me-2"></i>{{ $det['badgeText'] }} - {{ $det['subText'] }}</span>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted mb-3"><i class="fa-solid fa-truck-pickup me-2"></i>Pickup Details</h6>
                        <div class="bg-light p-3 rounded-4 border border-light h-100">
                            <p class="mb-2"><strong class="text-dark">Date:</strong> {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('l, d M Y') : 'N/A' }}</p>
                            <p class="mb-2"><strong class="text-dark">Time Slot:</strong> <span class="badge bg-white text-dark border">{{ $order->pickup_time ?? 'N/A' }}</span></p>
                            <p class="mb-0"><strong class="text-dark">Location:</strong> {{ $order->pickup_location ?? 'No specific location provided' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted mb-3"><i class="fa-solid fa-box me-2"></i>Scrap Details</h6>
                        <div class="bg-light p-3 rounded-4 border border-light h-100">
                            <p class="mb-2"><strong class="text-dark">Category:</strong> {{ $order->category?->name ?? 'N/A' }}</p>
                            <p class="mb-2"><strong class="text-dark">Subcategory:</strong> <span class="badge bg-success bg-opacity-10 text-success border">{{ $order->subcategory?->name ?? 'N/A' }}</span></p>
                            @if($order->total_amount)
                            <hr class="my-2 opacity-10">
                            <p class="mb-0"><strong class="text-dark">Estimated Payout:</strong> <span class="fw-bold text-success">₹{{ number_format($order->total_amount, 2) }}</span></p>
                            @endif
                        </div>
                    </div>
                    
                    @if($order->notes)
                    <div class="col-12">
                        <h6 class="fw-bold text-muted mb-2"><i class="fa-regular fa-comment-dots me-2"></i>Additional Notes</h6>
                        <div class="bg-light p-3 rounded-4 border border-light text-secondary">
                            {{ $order->notes }}
                        </div>
                    </div>
                    @endif

                    @if(!empty($order->images) && is_array($order->images) && count($order->images) > 0)
                    <div class="col-12">
                        <h6 class="fw-bold text-muted mb-3"><i class="fa-regular fa-image me-2"></i>Uploaded Images</h6>
                        <div class="d-flex gap-3 flex-wrap">
                            @foreach($order->images as $img)
                                <img src="{{ asset(str_starts_with($img, 'storage/') ? $img : 'storage/' . $img) }}" alt="Scrap Upload {{ $img }}" class="rounded-4 shadow-sm border border-2 border-white" style="width: 120px; height: 120px; object-fit: cover;">
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toastEl = document.getElementById('successToast');
        if (toastEl) {
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        // Filter functionality
        const filterBtns = document.querySelectorAll('.filter-btn');
        const sections = {
            'action-required': document.getElementById('section-action-required'),
            'active-pickups': document.getElementById('section-active-pickups'),
            'other-pickups': document.getElementById('section-other-pickups')
        };

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active button
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');

                if (filter === 'all') {
                    // Show all
                    Object.values(sections).forEach(sec => { if(sec) sec.style.display = 'block'; });
                } else {
                    // Hide all, show selected
                    Object.values(sections).forEach(sec => { if(sec) sec.style.display = 'none'; });
                    if (sections[filter]) {
                        sections[filter].style.display = 'block';
                    }
                }
            });
        });
    });
</script>
@endpush
