@extends('layouts.app')

@section('title', 'Complete Payment - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
@endpush

@section('content')
<div class="payment-wrapper">
    <div class="payment-card-centered">
        <div class="wallet-icon">
            <i class="fa-solid fa-wallet"></i>
            <i class="fa-solid fa-circle-check check-badge"></i>
        </div>
        
        <h1 class="title-main">Complete Your <span>Payment</span></h1>
        <p class="subtitle-main">Please pay the pickup service fee to complete your order.</p>
        
        <div class="dashed-box">
            <div class="amt-label">Amount to Pay</div>
            <div class="amt-value">₹{{ number_format($amount / 100, 2) }}</div>
            
            <div class="features-row">
                <div class="feature-item">
                    <i class="fa-solid fa-shield-halved"></i>
                    <div>
                        <h6>100% Secure</h6>
                        <p>Safe & Encrypted</p>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="fa-solid fa-bolt"></i>
                    <div>
                        <h6>Instant Payment</h6>
                        <p>Quick & Easy</p>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="fa-solid fa-headset"></i>
                    <div>
                        <h6>Reliable Support</h6>
                        <p>24x7 Assistance</p>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('customer.payment.callback') }}" method="POST">
                @csrf
                <script
                    src="https://checkout.razorpay.com/v1/checkout.js"
                    data-key="{{ env('RAZORPAY_KEY') }}"
                    data-amount="{{ $amount }}"
                    data-currency="INR"
                    data-order_id="{{ $razorpayOrder['id'] }}"
                    data-buttontext="Pay Securely with Razorpay"
                    data-name="Scrap Daddy"
                    data-description="Pickup Service Fee"
                    data-theme.color="#15803d">
                </script>
                <input type="hidden" name="razorpay_order_id" value="{{ $razorpayOrder['id'] }}">
            </form>
        </div>
        
        <div class="or-divider">or</div>
        
        <a href="{{ route('customer.orders') }}" class="cancel-link">Cancel and go back</a>
        
        <div class="security-footer">
            <div class="security-text">
                <div class="icon-circle"><i class="fa-solid fa-lock"></i></div>
                <div>
                    <h6>Your payment is safe & secure with industry-leading protection.</h6>
                    <p>We do not store your card details.</p>
                </div>
            </div>
            <div class="security-logos">
                <i class="fa-brands fa-cc-stripe"></i>
                <i class="fa-brands fa-cc-visa"></i>
                <i class="fa-brands fa-cc-mastercard"></i>
            </div>
        </div>
    </div>
</div>
@endsection
