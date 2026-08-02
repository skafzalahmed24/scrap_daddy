@extends('layouts.app')

@section('title', 'Order Details - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
    <style>
        .order-details-container {
            background-color: #ffffff;
            min-height: 100vh;
        }
        
        @media (min-width: 992px) {
            .order-details-container {
                background-color: transparent;
                min-height: auto;
            }
        }

        .od-header {
            display: flex;
            align-items: center;
            padding: 15px 0;
            margin-bottom: 20px;
        }
        .od-back-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            text-decoration: none;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            position: absolute;
        }
        .od-title {
            flex-grow: 1;
            text-align: center;
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0;
            color: #222;
        }

        .od-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #f0f0f0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            padding: 20px;
            margin-bottom: 20px;
        }

        .od-item-header {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .od-item-img-box {
            width: 70px;
            height: 70px;
            background-color: #f1f8e9;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 10px;
        }
        .od-item-img-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .od-item-name {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 2px;
            color: #222;
        }
        .od-item-id {
            color: #888;
            font-size: 0.85rem;
            margin: 0;
        }

        .od-section-title {
            font-weight: 700;
            font-size: 1rem;
            color: #222;
            margin-bottom: 15px;
        }
        .od-divider {
            height: 1px;
            background-color: #eee;
            margin-bottom: 15px;
        }

        .od-detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.9rem;
        }
        .od-detail-label {
            color: #777;
            font-weight: 500;
        }
        .od-detail-value {
            color: #222;
            font-weight: 600;
            text-align: right;
            max-width: 60%;
        }

        .od-images-grid {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 5px;
            scrollbar-width: none;
        }
        .od-images-grid::-webkit-scrollbar {
            display: none;
        }
        .od-image-box {
            width: 80px;
            height: 80px;
            background-color: #f5f5f5;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #ebebeb;
            overflow: hidden;
        }
        .od-image-box i {
            color: #aaa;
            font-size: 1.5rem;
        }
        .od-image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Timeline CSS */
        .od-timeline {
            position: relative;
            padding-left: 25px;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .od-timeline::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 5px;
            bottom: 25px;
            width: 2px;
            background-color: #1b5e20;
        }
        
        .od-timeline-item {
            position: relative;
            margin-bottom: 35px;
        }
        .od-timeline-item:last-child {
            margin-bottom: 0;
        }
        
        .od-timeline-icon {
            position: absolute;
            left: -25px;
            top: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #1b5e20;
            color: #fff;
            transform: translateX(-50%);
            z-index: 2;
            border: 2px solid #fff;
        }
        .od-timeline-icon.cancelled {
            background-color: #dc3545;
        }
        
        .od-timeline-icon.truck {
            background-color: #fff;
            border: none;
        }
        .od-timeline-icon.truck i {
            color: #1b5e20;
            font-size: 1.2rem;
            background: #fff;
            padding: 2px 0;
        }

        .od-timeline-content h6 {
            font-weight: 700;
            font-size: 0.95rem;
            color: #222;
            margin-bottom: 4px;
            margin-top: 2px;
        }
        .od-timeline-content p {
            color: #777;
            font-size: 0.85rem;
            margin-bottom: 2px;
            line-height: 1.3;
        }
        .od-timeline-content small {
            color: #999;
            font-size: 0.75rem;
        }

        .od-timeline-item.uncompleted ~ .od-timeline-item .od-timeline-icon,
        .od-timeline-item.uncompleted .od-timeline-icon {
            background-color: #fff;
            border: 2px solid #ccc;
            color: transparent;
        }
        .od-timeline-item.uncompleted ~ .od-timeline-item::before,
        .od-timeline-item.uncompleted::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 24px;
            bottom: -35px;
            width: 2px;
            background-color: #ccc;
            transform: translateX(-50%);
            z-index: 1;
        }
        .od-timeline-item:last-child::before {
            display: none;
        }
    </style>
@endpush

@section('content')

<div class="dashboard-container order-details-container">
    <div class="row g-4">
        
        @include('partials.customer.sidebar')

        <!-- Main Content -->
        <div class="col-xl-9 col-lg-9">
            
            <!-- Mobile Header with Back Button -->
            <div class="od-header position-relative px-3 px-lg-0 pt-4 pt-lg-2">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('customer.orders') }}" class="od-back-btn">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
                <h1 class="od-title">Order Details</h1>
            </div>

            <div class="px-3 px-lg-0 pb-5">
                <!-- Items Info Card -->
                <div class="od-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="od-section-title mb-0">Order Items</h3>
                        <p class="od-item-id mb-0">Order #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="od-divider"></div>
                    
                    @if($order->items && count($order->items) > 0)
                        @foreach($order->items as $item)
                            <div class="od-item-header mb-3 pb-3 border-bottom">
                                <div class="od-item-img-box" style="width: 50px; height: 50px; padding: 5px;">
                                    <i class="fa-solid fa-couch" style="color: #689f38; font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="od-item-name" style="font-size: 1rem;">{{ $item['name'] ?? 'Item' }}</h2>
                                    </div>
                                    <div class="fw-bold bg-light px-3 py-1 rounded text-dark">
                                        Qty: {{ $item['quantity'] ?? 1 }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="od-item-header mb-3 pb-3 border-bottom">
                            <div class="od-item-img-box" style="width: 50px; height: 50px; padding: 5px;">
                                @if($order->subcategory && $order->subcategory->image)
                                    <img src="{{ asset(str_starts_with($order->subcategory->image, 'storage/') ? $order->subcategory->image : 'storage/' . $order->subcategory->image) }}" alt="{{ $order->subcategory->name }}">
                                @else
                                    <i class="fa-solid fa-couch" style="color: #689f38; font-size: 1.5rem;"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1 d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="od-item-name" style="font-size: 1rem;">{{ $order->subcategory->name ?? 'Scrap Items' }}</h2>
                                </div>
                                <div class="fw-bold bg-light px-3 py-1 rounded text-dark">
                                    Qty: 1
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Pickup Details Card -->
                <div class="od-card">
                    <h3 class="od-section-title">Pickup Details</h3>
                    <div class="od-divider"></div>
                    
                    <div class="od-detail-row">
                        <span class="od-detail-label">Date</span>
                        <span class="od-detail-value">{{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('l, d M Y') : 'N/A' }}</span>
                    </div>
                    
                    <div class="od-detail-row">
                        <span class="od-detail-label">Time Slot</span>
                        <span class="od-detail-value">{{ $order->pickup_time ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="od-detail-row" style="margin-bottom: 0;">
                        <span class="od-detail-label">Location</span>
                    </div>
                    <div class="od-detail-value" style="text-align: left; max-width: 100%; margin-top: 5px;">
                        {{ $order->pickup_location ?? 'No specific location provided' }}
                    </div>
                </div>

                <!-- Uploaded Images Card -->
                <div class="od-card">
                    <h3 class="od-section-title">Uploaded Images</h3>
                    <div class="od-divider"></div>
                    
                    <div class="od-images-grid">
                        @if(!empty($order->images) && is_array($order->images) && count($order->images) > 0)
                            @foreach($order->images as $img)
                                <div class="od-image-box">
                                    <img src="{{ asset(str_starts_with($img, 'storage/') ? $img : 'storage/' . $img) }}" alt="Scrap Upload">
                                </div>
                            @endforeach
                            <!-- Fill placeholders if less than 4 images, like mockup -->
                            @for($i = count($order->images); $i < 4; $i++)
                                <div class="od-image-box">
                                    <i class="fa-regular fa-image"></i>
                                </div>
                            @endfor
                        @else
                            <div class="od-image-box"><i class="fa-regular fa-image"></i></div>
                            <div class="od-image-box"><i class="fa-regular fa-image"></i></div>
                            <div class="od-image-box"><i class="fa-regular fa-image"></i></div>
                            <div class="od-image-box"><i class="fa-regular fa-image"></i></div>
                        @endif
                    </div>
                </div>

                <!-- Pickup Progress -->
                <h3 class="od-section-title mt-4 ms-2 mb-4">Pickup Progress</h3>
                
                @php
                    $isAccepted = $order->status == 'accepted';
                    $isCompleted = $order->status == 'completed';
                    $isCancelled = $order->status == 'cancelled';
                @endphp

                <div class="od-timeline ms-2">
                    <!-- Step 1: Submitted -->
                    <div class="od-timeline-item">
                        <div class="od-timeline-icon">
                            <i class="fa-solid fa-check" style="font-size: 0.8rem;"></i>
                        </div>
                        <div class="od-timeline-content">
                            <h6>Submitted</h6>
                            <p>Your pickup request has been received.</p>
                            <small>{{ \Carbon\Carbon::parse($order->created_at)->format('l, h:i A') }}</small>
                        </div>
                    </div>

                    @if($isCancelled)
                        <!-- Step Cancelled -->
                        <div class="od-timeline-item">
                            <div class="od-timeline-icon cancelled">
                                <i class="fa-solid fa-xmark" style="font-size: 0.9rem;"></i>
                            </div>
                            <div class="od-timeline-content">
                                <h6>Cancelled</h6>
                                <p>Your pickup request was cancelled.</p>
                                <small>{{ \Carbon\Carbon::parse($order->updated_at)->format('l, h:i A') }}</small>
                            </div>
                            <style>
                                .od-timeline::before { background: linear-gradient(to bottom, #1b5e20 50%, #dc3545 50%); }
                            </style>
                        </div>
                    @else
                        <!-- Step 2: Confirmed -->
                        <div class="od-timeline-item {{ (!$isAccepted && !$isCompleted) ? 'uncompleted' : '' }}">
                            <div class="od-timeline-icon">
                                <i class="fa-solid fa-check" style="font-size: 0.8rem;"></i>
                            </div>
                            <div class="od-timeline-content">
                                <h6>Confirmed</h6>
                                <p>We have verified and confirmed the request.</p>
                                <small>{{ ($isAccepted || $isCompleted) ? \Carbon\Carbon::parse($order->updated_at)->format('l, h:i A') : 'Pending' }}</small>
                            </div>
                        </div>

                        <!-- Step 3: Assigned -->
                        <div class="od-timeline-item {{ (!$isAccepted && !$isCompleted) ? 'uncompleted' : '' }}">
                            <div class="od-timeline-icon {{ ($isAccepted || $isCompleted) ? 'truck' : '' }}">
                                @if($isAccepted || $isCompleted)
                                    <i class="fa-solid fa-truck"></i>
                                @else
                                    <i class="fa-solid fa-check" style="font-size: 0.8rem;"></i>
                                @endif
                            </div>
                            <div class="od-timeline-content">
                                <h6>Assigned</h6>
                                <p>Our executive has accepted your pickup request.</p>
                                <small>{{ ($isAccepted || $isCompleted) ? \Carbon\Carbon::parse($order->updated_at)->format('l, h:i A') : 'Pending' }}</small>
                            </div>
                        </div>

                        <!-- Step 4: Completed -->
                        <div class="od-timeline-item {{ (!$isCompleted) ? 'uncompleted' : '' }}">
                            <div class="od-timeline-icon">
                                <i class="fa-solid fa-check" style="font-size: 0.8rem;"></i>
                            </div>
                            <div class="od-timeline-content">
                                <h6>Completed</h6>
                                <p>Driver is heading to your location.</p>
                                <small>{{ ($isCompleted) ? \Carbon\Carbon::parse($order->updated_at)->format('l, h:i A') : 'Pending' }}</small>
                            </div>
                        </div>
                        
                        @if(!$isCompleted)
                        <style>
                            .od-timeline::before { background: linear-gradient(to bottom, #1b5e20 {{ $isAccepted ? '66%' : '33%' }}, #ccc {{ $isAccepted ? '66%' : '33%' }}); }
                        </style>
                        @endif
                    @endif

                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
