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
                $user = auth()->user() ?? \App\Models\User::first() ?? new \App\Models\User(['full_name' => 'Shaik Afzal', 'phone_number' => '+91 9876543210']);
                
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
            
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('customer.orders.create') }}" class="btn btn-success px-4 py-2 shadow-sm rounded-pill fw-bold"><i class="fa-solid fa-plus me-2"></i>New Pickup</a>
            </div>

            <!-- Hero Banner -->
            @include('partials.customer.hero-banner', [
                'title' => 'Payment History',
                'subtitle' => 'Track your earnings and pending payments from scrap pickups.'
            ])

            <div class="middle-card">


                @forelse($payments as $payment)
                    <div class="payment-item">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-light d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px;">
                                    <i class="fa-solid fa-indian-rupee-sign fs-4 text-secondary"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Order #ORD-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</h6>
                                    <p class="text-muted mb-0 small">{{ \Carbon\Carbon::parse($payment->updated_at)->format('d M Y, h:i A') }}</p>
                                </div>
                            </div>
                            <div class="text-end d-flex align-items-center gap-3">
                                <div class="text-end">
                                    <h5 class="fw-bold mb-1 {{ $payment->payment_status == 'completed' ? 'text-success' : 'text-warning' }}">
                                        ₹{{ number_format($payment->total_amount ?? 0, 2) }}
                                    </h5>
                                    <span class="badge {{ $payment->payment_status == 'completed' ? 'bg-success' : 'bg-warning' }} bg-opacity-10 {{ $payment->payment_status == 'completed' ? 'text-success' : 'text-warning' }} border {{ $payment->payment_status == 'completed' ? 'border-success' : 'border-warning' }} rounded-pill px-2 py-1">
                                        {{ ucfirst($payment->payment_status) }}
                                    </span>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#orderDetailsModal-{{ $payment->id }}">View Details</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
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

<!-- Order Detail Modals -->
@foreach($payments as $order)
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
