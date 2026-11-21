@extends('layouts.app')

@section('title', 'Payment status - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
    <style>
        .st-wrap { max-width: 480px; margin: 0 auto; padding: 18px 14px 120px; }
        .st-hero { text-align: center; padding: 26px 10px 8px; }
        .st-ring { width: 104px; height: 104px; border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; font-size: 2.6rem; color: #fff; animation: stPop .55s cubic-bezier(.2,1.4,.4,1) both; }
        .ring-paid { background: #3D8B4D; box-shadow: 0 0 0 10px rgba(61,139,77,.14), 0 0 0 22px rgba(61,139,77,.07); }
        .ring-verifying { background: #E6A21A; box-shadow: 0 0 0 10px rgba(230,162,26,.16), 0 0 0 22px rgba(230,162,26,.08); }
        .ring-partial { background: #2F6FE4; box-shadow: 0 0 0 10px rgba(47,111,228,.14), 0 0 0 22px rgba(47,111,228,.07); }
        @keyframes stPop { from { transform: scale(.3); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .st-title { font-size: 1.35rem; font-weight: 800; margin-bottom: 4px; }
        .st-amt { font-size: 2.4rem; font-weight: 800; letter-spacing: -.03em; }
        .st-sub { color: #64748B; font-size: .92rem; }
        .st-card { background: #fff; border: 1px solid #D8E8D9; border-radius: 22px; padding: 6px 18px; margin-top: 22px; box-shadow: 0 8px 24px rgba(15,23,42,.05); }
        .st-row { display: flex; justify-content: space-between; gap: 14px; padding: 13px 0; border-bottom: 1px dashed #E2E8F0; font-size: .92rem; }
        .st-row:last-child { border: 0; } .st-row span { color: #64748B; } .st-row b { text-align: right; word-break: break-all; }
        .st-note { background: #F4FCF4; border-radius: 16px; padding: 12px 14px; margin-top: 16px; font-size: .88rem; color: #2F6B3A; }
        .st-note.amber { background: #FEF3DC; color: #8A5A07; }
        .st-actions { position: fixed; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,.95); border-top: 1px solid #E2E8F0; padding: 12px 14px calc(12px + env(safe-area-inset-bottom)); }
        .st-actions-in { max-width: 480px; margin: 0 auto; display: flex; gap: 10px; }
        .st-actions .btn { flex: 1; border-radius: 14px; padding: 13px; font-weight: 700; }
        @media (max-width: 991px) { .st-actions { bottom: 78px; } }
    </style>
@endpush

@section('content')
@php
    $status = $pay['status'];
    $last = collect($pay['payments'])->first(fn ($p) => $p['status'] !== 'rejected');
    $shownAmount = $last['amount'] ?? $pay['paid'];
    $ring = ['paid' => 'ring-paid', 'verifying' => 'ring-verifying', 'partial' => 'ring-partial'][$status] ?? 'ring-verifying';
    $icon = ['paid' => 'fa-check', 'verifying' => 'fa-hourglass-half', 'partial' => 'fa-check'][$status] ?? 'fa-clock';
    $title = ['paid' => 'Payment successful', 'verifying' => 'Request raised', 'partial' => 'Advance received'][$status] ?? $pay['status_label'];
    $sub = ['paid' => 'Thank you! Your pickup payment is complete.', 'verifying' => 'We are verifying your payment. This usually takes a little while.', 'partial' => 'Your advance is confirmed. The balance is due after pickup.'][$status] ?? '';
@endphp
<div class="st-wrap">
    <div class="st-hero">
        <div class="st-ring {{ $ring }}"><i class="fa-solid {{ $icon }}"></i></div>
        <div class="st-title">{{ $title }}</div>
        <div class="st-amt">₹{{ number_format($shownAmount, 2) }}</div>
        <div class="st-sub mt-1">{{ $sub }}</div>
    </div>

    <div class="st-card">
        <div class="st-row"><span>Order</span><b>#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} · {{ $order->display_title }}</b></div>
        @if($last)
            <div class="st-row"><span>Method</span><b>{{ $last['method_label'] }}{{ $last['kind'] === 'advance' ? ' · Advance' : '' }}</b></div>
            @if($last['reference'])<div class="st-row"><span>Reference</span><b>{{ $last['reference'] }}</b></div>@endif
            <div class="st-row"><span>Date &amp; time</span><b>{{ \Carbon\Carbon::parse($last['created_at'])->format('d M Y, h:i A') }}</b></div>
        @endif
        <div class="st-row"><span>Total payable</span><b>₹{{ number_format($pay['total'], 2) }}</b></div>
        <div class="st-row"><span>Paid so far</span><b style="color:#2B7A3D;">₹{{ number_format($pay['paid'], 2) }}</b></div>
        @if($pay['remaining'] > 0)<div class="st-row"><span>Balance</span><b>₹{{ number_format($pay['remaining'], 2) }}</b></div>@endif
        <div class="st-row"><span>Status</span><b>{{ $pay['status_label'] }}</b></div>
    </div>

    @if($status === 'verifying')
        <div class="st-note amber"><i class="fa-solid fa-circle-info me-1"></i> You do not need to pay again. We will update this page and your order as soon as the payment is confirmed.</div>
    @elseif($status === 'partial')
        <div class="st-note"><i class="fa-solid fa-circle-info me-1"></i> Balance of <b>₹{{ number_format($pay['remaining'], 2) }}</b> can be paid after the pickup is completed.</div>
    @endif
</div>

<div class="st-actions"><div class="st-actions-in">
    <a class="btn btn-outline-primary" href="{{ route('customer.order.show', $order->id) }}">Order details</a>
    @if($pay['remaining'] > 0 && $status !== 'verifying' && $order->status === 'completed')
        <a class="btn btn-primary" href="{{ route('customer.payment.initiate', $order->id) }}">Pay balance</a>
    @else
        <a class="btn btn-primary" href="{{ route('customer.orders') }}">Done</a>
    @endif
</div></div>
@endsection
