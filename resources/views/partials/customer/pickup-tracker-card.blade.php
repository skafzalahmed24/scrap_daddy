{{-- Pickup tracker card: same look as the mobile app card. Expects $order. --}}
@php $st = $order->status; $isCancelled = ($st === 'cancelled'); @endphp
<div class="sd-card">
    <div class="sd-card-head">
        <div class="sd-ttl">
            <div class="sd-truck-ic"><i class="fa-solid fa-truck"></i></div>
            <h6>
                {{ $st === 'completed' ? 'Pickup Completed' : ($isCancelled ? 'Pickup Cancelled' : 'Pickup Requested') }}
                <span class="sd-ref">Order #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
            </h6>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="sd-badge {{ $st }}">{{ ucfirst($st) }}</span>
            <a href="{{ route('customer.order.show', $order->id) }}" class="text-decoration-none" style="color:#4A9A58; font-size:.85rem; font-weight:700;">View</a>
        </div>
    </div>

    @include('partials.customer.pickup-tracker', ['order' => $order])

    <div class="sd-card-foot">
        <a href="{{ route('customer.support') }}"><i class="fa-solid fa-headset"></i>Need Support? We are happy to help</a>
    </div>
</div>
