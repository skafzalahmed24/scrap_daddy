@extends('layouts.app')

@section('title', 'Rewards - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
@endpush

@section('content')

<div class="dashboard-container">
    <div class="row g-4">
        
        @include('partials.customer.sidebar')

        <!-- Main Content -->
        <div class="col-xl-9 col-lg-9">
            <div class="content-area d-flex flex-column align-items-center justify-content-center text-center" style="min-height: 80vh;">
                <div style="background-color: rgba(46, 125, 50, 0.1); width: 80px; height: 80px;" class="rounded-circle d-flex align-items-center justify-content-center mb-4">
                    <i class="fa-solid fa-gift fs-1 text-success"></i>
                </div>
    <h3 class="fw-bold text-dark mb-3">Rewards & Offers</h3>
    <p class="text-muted mb-4 px-4">
        Earn reward points for every pickup. The rewards store is coming soon with exciting offers and coupons!
    </p>
    
                <div class="card border-0 w-100 shadow-sm mt-5" style="border-radius: 16px; background-color: #f8f9fa;">
                    <div class="card-body p-4 text-start">
                        <h6 class="fw-bold mb-3"><i class="fa-solid fa-coins me-2 text-warning"></i>Your Balance</h6>
                        <h2 class="fw-bold text-dark mb-1">{{ auth()->user()->getAvailableRewardCoins() }} <span class="fs-6 text-muted fw-normal">Coins</span></h2>
                        @php
                            $lastReward = auth()->user()->orders()->where('coins_earned', '>', 0)->latest()->first();
                        @endphp
                        @if($lastReward)
                            <p class="text-success mb-0" style="font-size: 0.85rem;"><i class="fa-solid fa-arrow-trend-up me-1"></i> +{{ $lastReward->coins_earned }} coins from last pickup on {{ $lastReward->created_at->format('d M, Y') }}</p>
                        @else
                            <p class="text-muted mb-0" style="font-size: 0.85rem;">Complete a pickup to earn coins</p>
                        @endif
                    </div>
                </div>

                <!-- History -->
                <div class="w-100 mt-5 text-start">
                    <h5 class="fw-bold mb-3">Rewards History</h5>
                    <div class="list-group">
                        @forelse(auth()->user()->orders()->where(function($q) { $q->where('coins_earned', '>', 0)->orWhere('coins_redeemed', '>', 0); })->latest()->get() as $order)
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 border-0 border-bottom">
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Order #{{ $order->id }}</h6>
                                    <small class="text-muted">{{ $order->created_at->format('d M, Y \a\t h:i A') }}</small>
                                </div>
                                <div class="text-end">
                                    @if($order->coins_earned > 0)
                                        <div class="mb-1">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">+ {{ $order->coins_earned }} Earned</span>
                                        </div>
                                        @if($order->available_coins > 0)
                                            <div class="small text-muted" style="font-size: 0.7rem;">
                                                {{ $order->available_coins }} coins remaining
                                                @if($order->coins_expires_at)
                                                    <br>Expires on {{ \Carbon\Carbon::parse($order->coins_expires_at)->format('d M, Y') }}
                                                @else
                                                    <br>Lifetime validity
                                                @endif
                                            </div>
                                        @elseif($order->coins_expires_at && \Carbon\Carbon::parse($order->coins_expires_at)->isPast())
                                            <div class="small text-danger" style="font-size: 0.7rem;">Expired</div>
                                        @endif
                                    @endif
                                    @if($order->coins_redeemed > 0)
                                        <div class="mt-2">
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2">- {{ $order->coins_redeemed }} Redeemed</span>
                                            <div class="small text-muted mt-1">(Saved ₹{{ $order->discount_applied }})</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted p-4 border rounded">
                                No reward transactions yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
