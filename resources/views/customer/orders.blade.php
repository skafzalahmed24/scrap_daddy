@extends('layouts.app')

@section('title', 'My Orders - Scrap Daddy')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    body {
        background-color: #ffffff;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #1e293b;
    }

    .orders-screen-wrapper {
        max-width: 480px;
        margin: 0 auto;
        padding: 16px 16px 90px 16px;
        min-height: 100vh;
    }

    /* Header matching mockup */
    .orders-top-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 0 20px 0;
        position: relative;
    }

    .circle-back-btn {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1e293b;
        font-size: 1.15rem;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
    }

    .circle-back-btn:hover {
        background-color: #f8fafc;
        color: #0f172a;
    }

    .orders-header-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        text-align: center;
        letter-spacing: -0.2px;
    }

    /* Clean Order Card */
    .order-clean-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1.5px solid #f1f5f9;
        padding: 18px 20px;
        margin-bottom: 18px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .order-clean-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.06);
    }

    .order-category-row {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .order-category-thumb {
        width: 58px;
        height: 58px;
        border-radius: 14px;
        background-color: #edf7ee;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .order-category-thumb i {
        font-size: 1.6rem;
        color: #2e7d32;
    }

    .order-category-thumb img {
        width: 36px;
        height: 36px;
        object-fit: contain;
    }

    .order-title-block {
        flex-grow: 1;
        min-width: 0;
    }

    .order-title-name {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 3px;
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .order-schedule-badge {
        font-size: 0.85rem;
        font-weight: 700;
        margin: 0;
    }

    .schedule-orange {
        color: #ea580c;
    }

    .schedule-blue {
        color: #0284c7;
    }

    .schedule-green {
        color: #16a34a;
    }

    /* Dashed Separator */
    .order-card-divider {
        border-top: 1.5px dashed #e2e8f0;
        margin: 16px 0;
    }

    /* Address Section */
    .order-address-label {
        font-size: 0.92rem;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
    }

    .order-address-label i {
        color: #ea580c;
        font-size: 1rem;
    }

    .order-address-text {
        font-size: 0.84rem;
        line-height: 1.45;
        color: #64748b;
        margin: 0;
        word-break: break-word;
    }

    /* Buttons */
    .btn-pay-now-blue {
        width: 100%;
        background-color: #1976d2;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 1rem;
        padding: 13px 18px;
        border-radius: 12px;
        border: none;
        text-align: center;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 16px;
        transition: background-color 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 4px 12px rgba(25, 118, 210, 0.25);
    }

    .btn-pay-now-blue:hover {
        background-color: #1565c0;
        box-shadow: 0 6px 16px rgba(21, 101, 192, 0.35);
    }

    .btn-view-order {
        width: 100%;
        background-color: #f8fafc;
        color: #334155 !important;
        font-weight: 700;
        font-size: 0.92rem;
        padding: 11px 18px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        text-align: center;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 14px;
        transition: all 0.2s ease;
    }

    .btn-view-order:hover {
        background-color: #f1f5f9;
        color: #0f172a !important;
    }

    /* Empty state */
    .orders-empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #ffffff;
        border-radius: 20px;
        border: 1.5px dashed #e2e8f0;
        margin-top: 20px;
    }

    .orders-empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 14px;
    }
</style>
@endpush

@section('content')
<div class="orders-screen-wrapper">
    <!-- Top Header Navigation -->
    <div class="orders-top-nav">
        <a href="{{ route('customer.home') }}" class="circle-back-btn" title="Back to Home">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="orders-header-title">My Orders</h1>
        <div style="width: 40px;"></div> <!-- Spacer for perfect center alignment -->
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-3 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-3 border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Orders List -->
    @php
        $ordersList = $orders ?? collect();
    @endphp

    @forelse($ordersList as $order)
        @php
            // Determine Category Name & Icon
            $firstItem = is_array($order->items) && count($order->items) > 0 ? $order->items[0] : null;
            $catName = $order->subcategory->name ?? ($order->category->name ?? ($firstItem['name'] ?? 'Electronics'));
            
            // Subtitle Timing Format
            $pickupDateStr = $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('Y-m-d') : null;
            $todayStr = date('Y-m-d');
            $tomorrowStr = date('Y-m-d', strtotime('+1 day'));
            
            $timeText = $order->pickup_time ?? '7:00 AM';
            $scheduleClass = 'schedule-blue';
            $scheduleText = '';

            if ($pickupDateStr === $todayStr) {
                $scheduleText = 'Pickup by Today ' . $timeText;
                $scheduleClass = 'schedule-orange';
            } elseif ($pickupDateStr === $tomorrowStr) {
                $scheduleText = 'Pickup by Tomorrow ' . $timeText;
                $scheduleClass = 'schedule-orange';
            } elseif ($pickupDateStr) {
                $scheduleText = 'Pickup by ' . $pickupDateStr . ' ' . $timeText;
                $scheduleClass = 'schedule-blue';
            } else {
                $scheduleText = 'Pickup Scheduled ' . $timeText;
                $scheduleClass = 'schedule-blue';
            }

            if ($order->status === 'completed') {
                $scheduleText = 'Pickup Completed';
                $scheduleClass = 'schedule-green';
            } elseif ($order->status === 'cancelled') {
                $scheduleText = 'Pickup Cancelled';
                $scheduleClass = 'schedule-orange';
            }

            // Payment required check
            $needsPayment = ($order->payment_status === 'pending' && ($order->total_amount > 0 || $order->status === 'completed'));
        @endphp

        <div class="order-clean-card">
            <!-- Category and Pickup Schedule -->
            <div class="order-category-row">
                <div class="order-category-thumb">
                    @if(isset($order->subcategory) && $order->subcategory->image)
                        <img src="{{ asset($order->subcategory->image) }}" alt="{{ $catName }}">
                    @else
                        <i class="bi bi-box-seam"></i>
                    @endif
                </div>
                <div class="order-title-block">
                    <h2 class="order-title-name">{{ $catName }}</h2>
                    <p class="order-schedule-badge {{ $scheduleClass }}">{{ $scheduleText }}</p>
                </div>
            </div>

            <!-- Dashed line separator -->
            <div class="order-card-divider"></div>

            <!-- Pickup Address -->
            <div class="order-address-label">
                <i class="bi bi-geo-alt-fill"></i> Address
            </div>
            <p class="order-address-text">
                {{ $order->pickup_location ?: 'Kakinada Urban, Kannayya Kapu Nagar, Kakinada, Andhra Pradesh, 533001, India' }}
            </p>

            <!-- Pay Now Button (if pending payment) -->
            @if($needsPayment)
                <a href="{{ route('customer.payment.initiate', $order->id) }}" class="btn-pay-now-blue">
                    Pay Now
                </a>
            @else
                <a href="{{ route('customer.order.show', $order->id) }}" class="btn-view-order">
                    View Details
                </a>
            @endif
        </div>
    @empty
        <div class="orders-empty-state">
            <i class="bi bi-box2"></i>
            <h3 class="fw-bold text-dark fs-5 mb-1">No Orders Found</h3>
            <p class="text-muted small mb-4">You haven't scheduled any pickups yet.</p>
            <a href="{{ route('customer.orders.create') }}" class="btn text-white fw-bold px-4 py-2" style="background-color: #1b5e20; border-radius: 12px;">Schedule Pickup</a>
        </div>
    @endforelse
</div>
@endsection
