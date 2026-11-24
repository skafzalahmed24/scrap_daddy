{{-- Pickup tracker card: same look as the mobile app card. Expects $order. --}}
@php
    $st = $order->status;
    $isCancelled = ($st === 'cancelled');
    $cardPay = $order->payment_info;
    // The payment chip only appears when there is something to say (pay now / paid / partial / verifying)
    $showPayChip = !$isCancelled && !in_array($cardPay['status'], ['none', 'cancelled'], true);
@endphp
<style>
    /* header that wraps neatly on narrow phones: title takes the space, badges drop below if needed */
    .sd-card-head { flex-wrap: wrap; }
    .sd-card-head .sd-ttl { flex: 1 1 150px; }
    .sd-card-head .sd-head-right { display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap; justify-content: flex-end; }
    .sd-view { width: 32px; height: 32px; border-radius: 50%; background: #eaf6ea; color: #3D8B4D !important; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; flex: 0 0 auto; font-size: .8rem; }
    .sd-view:hover { background: #d8eedc; }
</style>
<div class="sd-card">
    <div class="sd-card-head">
        <div class="sd-ttl">
            <div class="sd-truck-ic"><i class="fa-solid fa-truck"></i></div>
            <h6>
                {{ $st === 'completed' ? 'Pickup Completed' : ($isCancelled ? 'Pickup Cancelled' : 'Pickup Requested') }}
                <span class="sd-ref">Order #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
            </h6>
        </div>
        <div class="sd-head-right">
            @if($showPayChip)<span class="sd-pay-chip {{ $cardPay['status'] }}">{{ $cardPay['can_pay'] ? 'Pay now' : $cardPay['label'] }}</span>@endif
            <span class="sd-badge {{ $st }}">{{ ucfirst($st) }}</span>
            <a href="{{ route('customer.order.show', $order->id) }}" class="sd-view" title="View order" aria-label="View order"><i class="fa-solid fa-chevron-right"></i></a>
        </div>
    </div>

    @include('partials.customer.pickup-tracker', ['order' => $order])
</div>
