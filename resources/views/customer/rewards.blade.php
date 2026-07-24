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
                        <h2 class="fw-bold text-dark mb-1">150 <span class="fs-6 text-muted fw-normal">pts</span></h2>
                        <p class="text-success mb-0" style="font-size: 0.85rem;"><i class="fa-solid fa-arrow-trend-up me-1"></i> +50 pts from last pickup</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
