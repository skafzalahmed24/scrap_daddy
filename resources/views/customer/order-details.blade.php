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

        /* Item rows: category image + name + quantity (never wraps) */
        .od-item-row { display: flex; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 1px solid #f1f1f1; }
        .od-item-row:last-child { border-bottom: 0; padding-bottom: 0; }
        .od-item-thumb { flex: 0 0 56px; width: 56px; height: 56px; border-radius: 14px; background: #f1f8e9; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 6px; }
        .od-item-thumb img { width: 100%; height: 100%; object-fit: contain; }
        .od-item-thumb i { color: #689f38; font-size: 1.4rem; }
        .od-item-main { flex: 1; min-width: 0; }
        .od-item-main .od-item-name { font-size: 1rem; font-weight: 700; margin: 0; color: #222; overflow-wrap: anywhere; }
        .od-qty { flex: 0 0 auto; white-space: nowrap; font-weight: 700; font-size: .85rem; background: #f4f6f4; color: #222; padding: 6px 12px; border-radius: 50px; }

        .od-status { flex: 0 0 auto; white-space: nowrap; font-size: .72rem; font-weight: 700; padding: 5px 12px; border-radius: 50px; background: #ffc107; color: #212529; }
        .od-status-accepted, .od-status-assigned { background: #0d6efd; color: #fff; }
        .od-status-completed { background: #198754; color: #fff; }
        .od-status-cancelled { background: #dc3545; color: #fff; }

        .od-detail-row { gap: 16px; }
        .od-detail-value { overflow-wrap: anywhere; }

        @media (max-width: 575px) {
            .od-header { padding-top: 0 !important; margin-bottom: 14px; }
            .od-back-btn { width: 38px; height: 38px; left: 16px; }
            .od-item-thumb { flex-basis: 48px; width: 48px; height: 48px; }
            .od-item-row { gap: 10px; }
            .od-qty { padding: 5px 10px; font-size: .8rem; }
            .od-detail-row { font-size: .85rem; }
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
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 12px;
        }
        .od-image-box {
            aspect-ratio: 1 / 1;
            background-color: #f5f5f5;
            border-radius: 14px;
            display: block;
            border: 1px solid #ebebeb;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .od-image-box:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.1); }
        .od-image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .od-image-box.od-broken { background: #f5f5f5 url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' fill='%23bbb' viewBox='0 0 16 16'%3E%3Cpath d='M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z'/%3E%3Cpath d='M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z'/%3E%3C/svg%3E") center / 32px no-repeat; }
        .od-card .od-item-header:last-child { border-bottom: 0 !important; margin-bottom: 0 !important; padding-bottom: 0 !important; }
        .od-empty-images {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px;
            border: 1.5px dashed #d9d9d9;
            border-radius: 14px;
            background: #fafafa;
            color: #888;
            font-size: .9rem;
        }
        .od-empty-images i { font-size: 1.6rem; color: #bbb; }
        @media (max-width: 575px) {
            .od-images-grid { grid-template-columns: repeat(3, 1fr); gap: 8px; }
            .od-card { padding: 16px; border-radius: 14px; }
        }

        /* Single-Line Clean Vertical Timeline */
        .od-timeline {
            position: relative;
            padding-left: 36px;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .od-timeline-item {
            position: relative;
            padding-bottom: 30px;
        }
        .od-timeline-item:last-child {
            padding-bottom: 0;
        }

        /* Exactly 1 Single Vertical Line connecting the items */
        .od-timeline-item::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 24px;
            bottom: 0;
            width: 2px;
            background-color: #e2e8f0;
            z-index: 1;
        }
        .od-timeline-item:last-child::before {
            display: none !important;
        }

        .od-timeline-item.step-done::before {
            background-color: #4A9A58;
        }
        .od-timeline-item.step-cancelled::before {
            background-color: #dc3545;
        }

        /* Timeline Icons */
        .od-timeline-icon {
            position: absolute;
            left: -36px;
            top: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #ffffff;
            border: 2px solid #cbd5e1;
            color: transparent;
            z-index: 2;
            font-size: 0.75rem;
            transition: all 0.2s ease;
        }

        .od-timeline-item.step-done .od-timeline-icon {
            background-color: #4A9A58;
            border-color: #4A9A58;
            color: #ffffff;
        }

        .od-timeline-item.step-cancelled .od-timeline-icon {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #ffffff;
        }

        .od-timeline-item.step-active .od-timeline-icon {
            background-color: #ffffff;
            border-color: #4A9A58;
            color: #4A9A58;
        }

        .od-timeline-content h6 {
            font-weight: 700;
            font-size: 0.95rem;
            color: #1e293b;
            margin-bottom: 4px;
            margin-top: 0;
        }

        .od-timeline-content p {
            color: #64748b;
            font-size: 0.85rem;
            margin-bottom: 2px;
            line-height: 1.35;
        }

        .od-timeline-content small {
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 500;
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
                @php
                    $imgUrl = function ($path) {
                        if (!$path) return null;
                        return preg_match('#^https?://#', $path) ? $path : asset($path);
                    };
                    $orderItems = ($order->items && count($order->items) > 0)
                        ? $order->items
                        : [['name' => $order->subcategory->name ?? 'Scrap Items', 'quantity' => 1, 'subcategory_uuid' => $order->subcategory->uuid ?? null]];
                    $statusClass = $order->status;
                @endphp
                <div class="od-card">
                    <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                        <div>
                            <h3 class="od-section-title mb-1">Order Items</h3>
                            <p class="od-item-id mb-0">Order #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <span class="od-status od-status-{{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                    </div>
                    <div class="od-divider"></div>

                    @foreach($orderItems as $item)
                        @php
                            $src = $imgUrl($itemImages[$item['subcategory_uuid'] ?? ''] ?? ($order->subcategory->image ?? null));
                        @endphp
                        <div class="od-item-row">
                            <div class="od-item-thumb">
                                @if($src)
                                    <img src="{{ $src }}" alt="{{ $item['name'] ?? 'Item' }}" loading="lazy" onerror="this.replaceWith(Object.assign(document.createElement('i'), {className: 'fa-solid fa-recycle'}));">
                                @else
                                    <i class="fa-solid fa-recycle"></i>
                                @endif
                            </div>
                            <div class="od-item-main">
                                <h2 class="od-item-name">{{ $item['name'] ?? 'Item' }}</h2>
                            </div>
                            <div class="od-qty">Qty: {{ $item['quantity'] ?? 1 }}</div>
                        </div>
                    @endforeach
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

                @php
                    $pinfo = $order->payment_info;
                    $payBtn = $pinfo['due_kind'] === 'advance' ? 'Pay advance' : ($pinfo['due_kind'] === 'balance' ? 'Pay balance' : 'Pay now');
                @endphp
                @if($order->status !== 'cancelled' && ($pinfo['status'] !== 'none' || $pinfo['can_pay']))
                <!-- Payment Card -->
                <div class="od-card" id="payment">
                    <h3 class="od-section-title">Payment</h3>
                    <div class="od-divider"></div>
                    @if($pinfo['order_amount'] > 0)
                    <div class="od-detail-row">
                        <span class="od-detail-label">Order amount</span>
                        <span class="od-detail-value">₹{{ number_format($pinfo['order_amount'], 2) }}</span>
                    </div>
                    @endif
                    @if($pinfo['discount'] > 0)
                    <div class="od-detail-row">
                        <span class="od-detail-label">Coins discount</span>
                        <span class="od-detail-value" style="color:#3D8B4D;">- ₹{{ number_format($pinfo['discount'], 2) }}</span>
                    </div>
                    @endif
                    <div class="od-detail-row">
                        <span class="od-detail-label">Status</span>
                        <span class="od-detail-value">{{ $pinfo['label'] }}</span>
                    </div>
                    @if($pinfo['paid'] > 0)
                    <div class="od-detail-row">
                        <span class="od-detail-label">Paid</span>
                        <span class="od-detail-value">₹{{ number_format($pinfo['paid'], 0) }}</span>
                    </div>
                    @endif
                    @if($pinfo['can_pay'])
                    <a href="{{ route('customer.payment.initiate', $order->id) }}" class="btn w-100 text-white fw-bold mt-3 py-2" style="background:#3D8B4D;border-radius:12px;">{{ $payBtn }} ₹{{ number_format($pinfo['due_now'], 0) }}</a>
                    @endif
                </div>
                @endif

                <!-- Uploaded Images Card -->
                <div class="od-card">
                    <h3 class="od-section-title">Uploaded Images</h3>
                    <div class="od-divider"></div>
                    
                    @if(!empty($order->images) && is_array($order->images) && count($order->images) > 0)
                        <div class="od-images-grid">
                            @foreach($order->images as $img)
                                @php $photoUrl = preg_match('#^https?://#', $img) ? $img : asset($img); @endphp
                                <a href="{{ $photoUrl }}" target="_blank" rel="noopener" class="od-image-box">
                                    <img src="{{ $photoUrl }}" alt="Scrap Upload" loading="lazy" onerror="this.closest('.od-image-box').classList.add('od-broken'); this.remove();">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="od-empty-images">
                            <i class="fa-regular fa-image"></i>
                            <span>No photos were uploaded for this pickup</span>
                        </div>
                    @endif
                </div>

                <!-- Pickup Progress -->
                <div class="od-card">
                    <h3 class="od-section-title">Pickup Progress</h3>
                    <div class="od-divider"></div>
                    @include('partials.customer.pickup-tracker', ['order' => $order])
                </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
