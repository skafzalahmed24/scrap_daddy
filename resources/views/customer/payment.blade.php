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
            
            @if($order->discount_applied > 0)
                <div class="text-muted text-decoration-line-through mb-1">₹{{ number_format($order->total_amount, 2) }}</div>
                <div class="amt-value text-success mb-0">₹{{ number_format($amount / 100, 2) }}</div>
                <div class="badge bg-success-subtle text-success border border-success-subtle mb-3 mt-1"><i class="fa-solid fa-gift"></i> Rewards Applied (-₹{{ number_format($order->discount_applied, 2) }})</div>
            @else
                <div class="amt-value mb-4">₹{{ number_format($amount / 100, 2) }}</div>
                
                @if(isset($user) && $user->reward_coins > 0)
                <div class="rewards-card p-3 mb-4 rounded-3 text-start" style="background-color: #f8f9fa; border: 1px dashed #2e7d32;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 fw-bold" style="color: #2e7d32;"><i class="fa-solid fa-gift me-2"></i>Available Rewards</h6>
                            <p class="text-muted small mb-0">You have <strong>{{ $user->reward_coins }}</strong> coins (Value: <strong>₹{{ number_format($user->reward_coins * $coinValue, 2) }}</strong>).</p>
                        </div>
                        <form action="{{ route('customer.payment.apply-rewards', $order->id) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success rounded-pill fw-bold px-3">Apply</button>
                        </form>
                    </div>
                </div>
                @endif
            @endif
            
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
