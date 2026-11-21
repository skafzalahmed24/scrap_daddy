@extends('layouts.app')

@section('title', 'Rewards - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
    <style>
        .rw-hero {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            padding: 26px;
            color: #fff;
            background: linear-gradient(135deg, #3D8B4D 0%, #4A9A58 55%, #43a047 100%);
            box-shadow: 0 10px 28px rgba(61,139,77, .28);
        }
        .rw-hero::after {
            content: ''; position: absolute; right: -40px; top: -40px; width: 180px; height: 180px;
            border-radius: 50%; background: rgba(255,255,255,.1);
        }
        .rw-hero::before {
            content: ''; position: absolute; right: 50px; bottom: -60px; width: 140px; height: 140px;
            border-radius: 50%; background: rgba(255,255,255,.07);
        }
        .rw-hero .rw-label { font-size: .85rem; opacity: .85; display: flex; align-items: center; gap: 8px; }
        .rw-hero .rw-coin { width: 32px; height: 32px; border-radius: 50%; background: #ffc107; color: #7a5200; display: inline-flex; align-items: center; justify-content: center; font-size: .9rem; }
        .rw-hero .rw-balance { font-size: 2.8rem; font-weight: 800; line-height: 1.1; margin: 10px 0 4px; }
        .rw-hero .rw-balance small { font-size: 1rem; font-weight: 500; opacity: .85; }
        .rw-hero .rw-note { font-size: .85rem; opacity: .9; margin: 0; position: relative; z-index: 1; }

        .rw-info { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 16px; }
        .rw-info-item { background: #fff; border: 1px solid #f0f0f0; border-radius: 16px; padding: 14px; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,.03); }
        .rw-info-item i { width: 40px; height: 40px; border-radius: 12px; background: #e8f5e9; color: #4A9A58; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 8px; }
        .rw-info-item h6 { font-size: .85rem; font-weight: 700; margin: 0 0 2px; color: #222; }
        .rw-info-item p { font-size: .75rem; color: #777; margin: 0; }

        .rw-section-title { font-weight: 700; font-size: 1.05rem; margin: 28px 0 12px; color: #222; }

        .rw-item { display: flex; align-items: center; gap: 14px; background: #fff; border: 1px solid #f0f0f0; border-radius: 16px; padding: 14px; margin-bottom: 10px; box-shadow: 0 3px 10px rgba(0,0,0,.025); }
        .rw-icon { flex: 0 0 44px; height: 44px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; }
        .rw-icon.earn { background: #e8f5e9; color: #4A9A58; }
        .rw-icon.redeem { background: #fdecea; color: #d32f2f; }
        .rw-body { flex: 1; min-width: 0; }
        .rw-body h6 { margin: 0 0 2px; font-weight: 700; font-size: .95rem; color: #222; }
        .rw-body small { color: #888; font-size: .78rem; display: block; }
        .rw-body .rw-meta { color: #777; font-size: .72rem; margin-top: 2px; }
        .rw-body .rw-meta.expired { color: #d32f2f; }
        .rw-amount { text-align: right; flex-shrink: 0; }
        .rw-amount .pill { display: inline-block; padding: 5px 12px; border-radius: 50px; font-weight: 700; font-size: .82rem; }
        .rw-amount .pill.earn { background: #e8f5e9; color: #4A9A58; }
        .rw-amount .pill.redeem { background: #fdecea; color: #d32f2f; }
        .rw-amount small { display: block; color: #888; font-size: .7rem; margin-top: 3px; }

        .rw-empty { text-align: center; padding: 36px 16px; background: #fff; border: 1.5px dashed #d9d9d9; border-radius: 16px; color: #888; }
        .rw-empty i { font-size: 2rem; color: #c5c5c5; margin-bottom: 8px; display: block; }

        @media (max-width: 575px) {
            .rw-hero { padding: 20px; border-radius: 18px; }
            .rw-hero .rw-balance { font-size: 2.3rem; }
            .rw-info { gap: 8px; }
            .rw-info-item { padding: 12px 6px; }
            .rw-item { padding: 12px; gap: 10px; }
        }
    </style>
@endpush

@section('content')

@php
    $rwUser = auth()->user();
    $lastReward = $rwUser->orders()->where('coins_earned', '>', 0)->latest()->first();
    $rwHistory = $rwUser->orders()
        ->where(function ($q) { $q->where('coins_earned', '>', 0)->orWhere('coins_redeemed', '>', 0); })
        ->latest()->get();
@endphp

<div class="dashboard-container">
    <div class="row g-4">

        @include('partials.customer.sidebar')

        <!-- Main Content -->
        <div class="col-xl-9 col-lg-9">
            <div class="px-3 px-lg-0 pt-3 pb-5">

                <h3 class="fw-bold text-dark mb-3">Rewards</h3>

                <!-- Balance -->
                <div class="rw-hero">
                    <div class="rw-label"><span class="rw-coin"><i class="fa-solid fa-coins"></i></span> Your Balance</div>
                    <div class="rw-balance">{{ $rwUser->getAvailableRewardCoins() }} <small>Coins</small></div>
                    @if($lastReward)
                        <p class="rw-note"><i class="fa-solid fa-arrow-trend-up me-1"></i> +{{ $lastReward->coins_earned }} coins from your last pickup on {{ $lastReward->created_at->format('d M, Y') }}</p>
                    @else
                        <p class="rw-note">Complete a pickup to start earning coins</p>
                    @endif
                </div>

                <!-- How it works -->
                <div class="rw-info">
                    <div class="rw-info-item">
                        <i class="fa-solid fa-truck-fast"></i>
                        <h6>Schedule</h6>
                        <p>Book a pickup</p>
                    </div>
                    <div class="rw-info-item">
                        <i class="fa-solid fa-coins"></i>
                        <h6>Earn</h6>
                        <p>Get coins on completion</p>
                    </div>
                    <div class="rw-info-item">
                        <i class="fa-solid fa-tags"></i>
                        <h6>Redeem</h6>
                        <p>Save on your next pickup</p>
                    </div>
                </div>

                <!-- History -->
                <h5 class="rw-section-title">Rewards History</h5>

                @forelse($rwHistory as $order)
                    @if($order->coins_earned > 0)
                        <div class="rw-item">
                            <div class="rw-icon earn"><i class="fa-solid fa-arrow-down"></i></div>
                            <div class="rw-body">
                                <h6>Order #{{ $order->id }}</h6>
                                <small>{{ $order->created_at->format('d M, Y \a\t h:i A') }}</small>
                                @if($order->available_coins > 0)
                                    <div class="rw-meta">
                                        {{ $order->available_coins }} coins remaining &middot;
                                        {{ $order->coins_expires_at ? 'Expires ' . \Carbon\Carbon::parse($order->coins_expires_at)->format('d M, Y') : 'Lifetime validity' }}
                                    </div>
                                @elseif($order->coins_expires_at && \Carbon\Carbon::parse($order->coins_expires_at)->isPast())
                                    <div class="rw-meta expired">Expired</div>
                                @endif
                            </div>
                            <div class="rw-amount"><span class="pill earn">+{{ $order->coins_earned }}</span><small>Earned</small></div>
                        </div>
                    @endif
                    @if($order->coins_redeemed > 0)
                        <div class="rw-item">
                            <div class="rw-icon redeem"><i class="fa-solid fa-arrow-up"></i></div>
                            <div class="rw-body">
                                <h6>Order #{{ $order->id }}</h6>
                                <small>{{ $order->created_at->format('d M, Y \a\t h:i A') }}</small>
                                <div class="rw-meta">Saved ₹{{ $order->discount_applied }}</div>
                            </div>
                            <div class="rw-amount"><span class="pill redeem">-{{ $order->coins_redeemed }}</span><small>Redeemed</small></div>
                        </div>
                    @endif
                @empty
                    <div class="rw-empty">
                        <i class="fa-solid fa-gift"></i>
                        No reward transactions yet.
                    </div>
                @endforelse

            </div>
        </div>
    </div>
</div>
@endsection
