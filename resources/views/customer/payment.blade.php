@extends('layouts.app')

@section('title', 'Checkout - Scrap Daddy')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    body {
        background-color: #ffffff;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #1e293b;
    }

    .checkout-screen-wrapper {
        max-width: 480px;
        margin: 0 auto;
        padding: 16px 16px 100px 16px;
        min-height: 100vh;
        position: relative;
    }

    /* Header matching mockup */
    .checkout-top-nav {
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

    .checkout-header-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        text-align: center;
        letter-spacing: -0.2px;
    }

    /* Bill Breakdown Card */
    .bill-breakdown-card {
        background: #f8fafc;
        border-radius: 18px;
        padding: 24px 20px;
        margin-top: 10px;
        border: 1px solid #f1f5f9;
    }

    .bill-card-title {
        font-size: 1.12rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 20px;
    }

    .bill-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        font-size: 1rem;
    }

    .bill-label {
        color: #64748b;
        font-weight: 500;
    }

    .bill-value {
        color: #0f172a;
        font-weight: 600;
    }

    .bill-divider {
        border-top: 1.5px solid #e2e8f0;
        margin: 18px 0;
    }

    .bill-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1.1rem;
    }

    .bill-total-label {
        font-weight: 800;
        color: #0f172a;
    }

    .bill-total-value {
        font-weight: 800;
        color: #0f172a;
        font-size: 1.15rem;
    }

    /* Fixed Bottom Pay Button */
    .checkout-bottom-action {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #ffffff;
        padding: 16px 20px 24px 20px;
        z-index: 1000;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
    }

    .checkout-bottom-action-inner {
        max-width: 480px;
        margin: 0 auto;
    }

    .btn-pay-now-teal {
        width: 100%;
        height: 54px;
        background-color: #00695c;
        color: #ffffff;
        font-weight: 700;
        font-size: 1.05rem;
        border-radius: 12px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(0, 105, 92, 0.25);
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .btn-pay-now-teal:hover {
        background-color: #004d40;
        box-shadow: 0 6px 18px rgba(0, 77, 64, 0.35);
        color: #ffffff;
    }

    /* Rewards box */
    .rewards-apply-box {
        background: #ffffff;
        border: 1.5px dashed #2e7d32;
        border-radius: 16px;
        padding: 16px 18px;
        margin-top: 18px;
    }
</style>
@endpush

@section('content')
<div class="checkout-screen-wrapper">
    <!-- Top Header Navigation -->
    <div class="checkout-top-nav">
        <a href="{{ route('customer.orders') }}" class="circle-back-btn" title="Back to Orders">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="checkout-header-title">Checkout</h1>
        <div style="width: 40px;"></div> <!-- Spacer for perfect centering -->
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

    @php
        $initialAmount = $order->total_amount ?? ($amount / 100);
        $discountAmount = $order->discount_applied ?? 0;
        $payableAmount = $amount / 100;
    @endphp

    <!-- Bill Breakdown Card -->
    <div class="bill-breakdown-card">
        <h2 class="bill-card-title">Bill Breakdown</h2>

        <div class="bill-row">
            <span class="bill-label">Initial Amount</span>
            <span class="bill-value">₹{{ number_format($initialAmount, 1) }}</span>
        </div>

        @if($discountAmount > 0)
        <div class="bill-row">
            <span class="bill-label text-success"><i class="bi bi-gift-fill me-1"></i> Rewards Discount</span>
            <span class="bill-value text-success">-₹{{ number_format($discountAmount, 1) }}</span>
        </div>
        @endif

        <div class="bill-divider"></div>

        <div class="bill-total-row">
            <span class="bill-total-label">Total Payable</span>
            <span class="bill-total-value">₹{{ number_format($payableAmount, 1) }}</span>
        </div>
    </div>

    <!-- Optional Rewards Card if user has available coins -->
    @if($discountAmount == 0 && isset($user) && $user->reward_coins > 0)
    <div class="rewards-apply-box">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1 fs-6" style="color: #2e7d32;"><i class="bi bi-stars me-1"></i> Available Rewards</h3>
                <p class="text-muted small mb-0">Use <strong>{{ $user->reward_coins }}</strong> coins for ₹{{ number_format($user->reward_coins * $coinValue, 1) }} off</p>
            </div>
            <form action="{{ route('customer.payment.apply-rewards', $order->id) }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm text-white fw-bold px-3 py-1 rounded-pill" style="background-color: #2e7d32;">Apply</button>
            </form>
        </div>
    </div>
    @endif

    <!-- Razorpay Hidden Callback Form -->
    <form id="razorpayCallbackForm" action="{{ route('customer.payment.callback') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
    </form>
</div>

<!-- Fixed Bottom Pay Button -->
<div class="checkout-bottom-action">
    <div class="checkout-bottom-action-inner">
        <button type="button" class="btn-pay-now-teal" id="payNowBtn">
            Pay Now (₹{{ number_format($payableAmount, 1) }})
        </button>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.getElementById('payNowBtn').addEventListener('click', function(e) {
        e.preventDefault();

        const options = {
            "key": "{{ env('RAZORPAY_KEY') }}",
            "amount": "{{ $amount }}",
            "currency": "INR",
            "name": "Scrap Daddy",
            "description": "Pickup Service Fee - Order #{{ $order->id }}",
            "image": "{{ asset('scraplogo.jpeg') }}",
            "order_id": "{{ $razorpayOrder['id'] }}",
            "handler": function (response) {
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                document.getElementById('razorpay_signature').value = response.razorpay_signature;
                document.getElementById('razorpayCallbackForm').submit();
            },
            "prefill": {
                "name": "{{ $user->full_name ?? 'Customer' }}",
                "contact": "{{ $user->phone_number ?? '' }}"
            },
            "theme": {
                "color": "#00695c"
            }
        };

        const rzp = new Razorpay(options);
        rzp.on('payment.failed', function (response) {
            alert("Payment Failed: " + response.error.description);
        });
        rzp.open();
    });
</script>
@endsection
