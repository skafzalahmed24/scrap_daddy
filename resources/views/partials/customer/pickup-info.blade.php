{{-- Status message box under a pickup tracker. Dynamic: depends on order status, how long ago it was placed and the payment state. Expects $order. --}}
@once
<style>
    .sd-pay { display: inline-flex; align-items: center; gap: 6px; margin-top: 12px; padding: 9px 18px; border-radius: 50px; background: linear-gradient(135deg, #4A9A58, #3D8B4D); color: #fff !important; font-weight: 700; font-size: .85rem; text-decoration: none; box-shadow: 0 4px 12px rgba(61,139,77,.3); }
    .sd-info.is-pay { background: #fff8e6; border: 1px solid #ffd666; }
    .sd-info.is-verify { background: #f1ecfe; border: 1px solid #c9b8f7; }
    .sd-pay-chip { font-size: .68rem; font-weight: 700; padding: 4px 10px; border-radius: 50px; background: #fef3dc; color: #b7770a; white-space: nowrap; }
    .sd-pay-chip.paid { background: #e4f4e8; color: #2b7a3d; }
    .sd-pay-chip.partial { background: #e8f1fe; color: #2f6fe4; }
    .sd-pay-chip.verifying { background: #efe9fe; color: #7a4cE0; }
</style>
@endonce
@php
    $st = $order->status;
    $pi = $order->payment_info;
    $ref = '#ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
    $mins = $order->created_at ? (int) $order->created_at->diffInMinutes(now()) : 0;
    $when = \Carbon\Carbon::parse($order->pickup_date)->format('d M') . ', ' . $order->pickup_time;
    $payLabel = $pi['due_kind'] === 'advance' ? 'Pay advance' : ($pi['due_kind'] === 'balance' ? 'Pay balance' : 'Pay now');

    // [css class, title, text]
    if ($st === 'cancelled') {
        $box = ['is-bad', 'Pickup Cancelled', 'This pickup request was cancelled.'];
    } elseif ($pi['status'] === 'verifying') {
        $box = ['is-verify', 'Verifying your payment', 'We received your payment details and are verifying them. You will be updated shortly.'];
    } elseif ($st === 'completed') {
        $box = $pi['can_pay']
            ? ['is-pay', 'Pickup completed', 'A balance of ₹' . number_format($pi['due_now'], 0) . ' is pending for this pickup.']
            : ['is-ok', 'Pickup Successful!', 'You earned rewards for this pickup.'];
    } elseif ($pi['can_pay']) {
        $box = ['is-pay', 'Payment pending', ($pi['due_kind'] === 'advance' ? 'Pay the advance' : 'Pay ₹' . number_format($pi['due_now'], 0)) . ' to continue your pickup on ' . $when . '.'];
    } elseif ($st === 'pending') {
        if ($mins <= 30) {
            $box = ['', 'Confirming your request...', 'We received your request ' . ($mins < 1 ? 'just now' : $order->created_at->diffForHumans()) . '. Confirmation usually takes 15-30 minutes.'];
        } elseif ($mins <= 24 * 60) {
            $box = ['', 'Your request is with our team', 'We will connect with you shortly to confirm your pickup on ' . $when . '.'];
        } else {
            $box = ['', 'Still waiting for confirmation', 'Your request from ' . $order->created_at->format('d M') . ' is still pending. Our team will contact you soon. Need it sooner? Contact support with order ' . $ref . '.'];
        }
    } elseif ($st === 'accepted') {
        $box = ['is-plain', 'Pickup confirmed', 'Our team is arranging your pickup on ' . $when . '.'];
    } elseif ($st === 'assigned') {
        $box = ['is-plain', 'Team is on the way...', 'Your pickup team is assigned for ' . $when . '.'];
    } else {
        $box = ['is-plain', 'Scheduled for Pickup', 'Your pickup is scheduled for ' . $when . '.'];
    }
@endphp
<div class="sd-info {{ $box[0] }}">
    <h6>{{ $box[1] }}</h6>
    <p>{{ $box[2] }}</p>
    @if($pi['can_pay'] && $st !== 'cancelled')
        {{-- Pay now: opens the order detail screen, where the payment is made --}}
        <a href="{{ route('customer.order.show', $order->id) }}#payment" class="sd-pay"><i class="fa-solid fa-indian-rupee-sign"></i> {{ $payLabel }} ₹{{ number_format($pi['due_now'], 0) }}</a>
    @endif
</div>
