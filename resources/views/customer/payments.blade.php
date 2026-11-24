@extends('layouts.app')

@section('title', 'Payment History - Scrap Daddy')

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
                
                // Fetch payments history
                $payments = \App\Models\Order::with('subcategory')
                    ->where('user_uuid', $user->uuid)
                    ->whereNotNull('payment_status')
                    ->orderBy('updated_at', 'desc')
                    ->get();

                // Helper function to map status to UI colors and labels
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
            

            <!-- Hero Banner -->
            <div class="d-none d-lg-block">
                @include('partials.customer.hero-banner', [
                    'title' => 'Payment History',
                    'subtitle' => 'Track your earnings and pending payments from scrap pickups.'
                ])
            </div>

            <style>
                @media (max-width: 991px) {
                    .middle-card.payments-mobile-clean {
                        background: transparent !important;
                        border: none !important;
                        box-shadow: none !important;
                        padding: 0 !important;
                    }
                    .dashboard-container {
                        padding-top: 15px !important;
                    }
                }
            </style>

            <!-- Mobile Header with Back Button -->
            <div class="d-flex align-items-center mb-4 d-lg-none bg-white p-3 rounded-4 shadow-sm border border-light">
                <a href="{{ route('customer.profile') }}" class="text-dark text-decoration-none d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h5 class="mb-0 fw-bold ms-3" style="color: var(--primary-blue, #0d2b4d);">Payment History</h5>
            </div>

            <div class="middle-card payments-mobile-clean">

                @forelse($payments as $payment)
                    <div class="payment-item bg-white">
                        <div class="row align-items-center gy-3">
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 50px; height: 50px;">
                                        <i class="fa-solid fa-indian-rupee-sign fs-4 text-secondary"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">Order #ORD-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</h6>
                                        <p class="text-muted mb-0 small">{{ \Carbon\Carbon::parse($payment->updated_at)->format('d M Y, h:i A') }}</p>
                                        @if(($payment->discount_applied ?? 0) > 0)
                                            <p class="mb-0 small" style="color:#3D8B4D;font-weight:600;"><i class="fa-solid fa-gift me-1"></i>Order ₹{{ number_format($payment->total_amount, 2) }} &minus; coins discount ₹{{ number_format($payment->discount_applied, 2) }}</p>
                                        @endif
                                        @if(($payment->paid_amount ?? 0) > 0)
                                            <p class="text-muted mb-0 small">Paid ₹{{ number_format($payment->paid_amount, 2) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex justify-content-between align-items-center justify-content-md-end gap-md-4">
                                    <div class="text-start text-md-end">
                                        <h5 class="fw-bold mb-1 {{ $payment->payment_status == 'completed' ? 'text-success' : 'text-warning' }}">
                                            ₹{{ number_format(max(0, ($payment->total_amount ?? 0) - ($payment->discount_applied ?? 0)), 2) }}
                                        </h5>
                                        <span class="badge {{ $payment->payment_status == 'completed' ? 'bg-success' : 'bg-warning' }} bg-opacity-10 {{ $payment->payment_status == 'completed' ? 'text-success' : 'text-warning' }} border {{ $payment->payment_status == 'completed' ? 'border-success' : 'border-warning' }} rounded-pill px-3 py-1">
                                            {{ ucfirst($payment->payment_status) }}
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <a href="{{ route('customer.order.show', $payment->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4 py-2 fw-bold">View Details</a>
                                        @if(($payment->paid_amount ?? 0) > 0)
                                            <a href="{{ route('customer.payment.receipt', $payment->id) }}" class="btn btn-outline-success btn-sm rounded-pill px-4 py-2 fw-bold"><i class="fa-solid fa-file-invoice me-1"></i>Receipt</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted bg-white rounded-4 border">
                        <i class="fa-solid fa-receipt fs-1 mb-3 opacity-25"></i>
                        <h5>No Payment History Found</h5>
                        <p>You have not made any payments or received payouts yet.</p>
                    </div>
                @endforelse

            </div>
        </div>

        <!-- Right Sidebar Removed -->
    </div>
</div>
@endsection
