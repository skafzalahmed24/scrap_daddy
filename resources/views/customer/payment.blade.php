@extends('layouts.app')

@section('title', 'Pay - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
    <style>
        .pay-wrap { max-width: 560px; margin: 0 auto; padding: 14px 14px 130px; }
        .pay-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
        .pay-back { width: 40px; height: 40px; border-radius: 14px; background: #fff; border: 1px solid #D8E8D9; display: flex; align-items: center; justify-content: center; color: #1E293B; text-decoration: none; }
        .pay-top h1 { font-size: 1.15rem; font-weight: 800; margin: 0; }
        .pay-hero { background: #fff; border: 1px solid #D8E8D9; border-radius: 22px; padding: 20px; box-shadow: 0 8px 24px rgba(15,23,42,.05); text-align: center; }
        .pay-hero .lbl { color: #64748B; font-size: .85rem; }
        .pay-hero .amt { font-size: 2.3rem; font-weight: 800; letter-spacing: -.03em; color: #14201A; line-height: 1.15; }
        .pay-chip { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 999px; font-size: .78rem; font-weight: 700; }
        .chip-due { background: #FEF3DC; color: #B7770A; } .chip-partial { background: #E8F1FE; color: #2F6FE4; } .chip-verifying { background: #EFE9FE; color: #7A4CE0; } .chip-paid { background: #E4F4E8; color: #2B7A3D; }
        .pay-steps { display: flex; gap: 10px; margin-top: 16px; }
        .pay-step { flex: 1; background: #F4FCF4; border-radius: 14px; padding: 10px; text-align: left; }
        .pay-step small { display: block; color: #64748B; font-size: .72rem; } .pay-step b { font-size: 1rem; }
        .pay-step.dim { background: #F1F5F9; }
        .pay-card { background: #fff; border: 1px solid #D8E8D9; border-radius: 20px; padding: 16px; margin-top: 14px; }
        .pay-card h2 { font-size: .95rem; font-weight: 800; margin: 0 0 12px; }
        .pay-row { display: flex; justify-content: space-between; padding: 6px 0; color: #475569; font-size: .92rem; }
        .pay-row b { color: #14201A; }
        .method { display: flex; align-items: center; gap: 12px; padding: 13px 14px; border: 1.5px solid #E2E8F0; border-radius: 16px; cursor: pointer; margin-bottom: 10px; background: #fff; transition: .15s; }
        .method input { display: none; }
        .method .ic { width: 42px; height: 42px; border-radius: 13px; display: flex; align-items: center; justify-content: center; background: #F4FCF4; color: #3D8B4D; font-size: 1.05rem; flex-shrink: 0; }
        .method b { display: block; color: #14201A; } .method small { color: #64748B; }
        .method.active { border-color: #66BB6A; background: #F3FBF4; box-shadow: 0 4px 14px rgba(61,139,77,.12); }
        .method .dot { margin-left: auto; width: 20px; height: 20px; border-radius: 50%; border: 2px solid #CBD5E1; flex-shrink: 0; }
        .method.active .dot { border-color: #3D8B4D; background: radial-gradient(#3D8B4D 45%, #fff 50%); }
        .mpanel { display: none; background: #F8FAFC; border-radius: 14px; padding: 14px; margin: -2px 0 12px; font-size: .9rem; }
        .mpanel.show { display: block; }
        .copyline { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 7px 0; border-bottom: 1px dashed #E2E8F0; }
        .copyline:last-child { border: 0; } .copyline span { color: #64748B; font-size: .82rem; } .copyline b { word-break: break-all; }
        .pay-bottom { position: fixed; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,.94); -webkit-backdrop-filter: blur(12px); backdrop-filter: blur(12px); border-top: 1px solid #E2E8F0; padding: 12px 14px calc(12px + env(safe-area-inset-bottom)); z-index: 50; }
        .pay-bottom-in { max-width: 560px; margin: 0 auto; }
        .pay-btn { width: 100%; border: 0; background: #3D8B4D; color: #fff; font-weight: 800; border-radius: 16px; padding: 15px; font-size: 1.02rem; box-shadow: 0 8px 20px rgba(61,139,77,.28); }
        .pay-btn:disabled { opacity: .6; }
        @media (max-width: 991px) { .pay-bottom { bottom: 78px; } .pay-wrap { padding-bottom: 190px; } }
    </style>
@endpush

@section('content')
@php
    $due = $pay['due_now'];
    $methods = $pay['methods'];
    $chip = ['paid' => 'chip-paid', 'partial' => 'chip-partial', 'verifying' => 'chip-verifying'][$pay['status']] ?? 'chip-due';
    $icons = ['razorpay' => 'fa-credit-card', 'upi' => 'fa-mobile-screen', 'bank' => 'fa-building-columns', 'cash' => 'fa-money-bill-wave'];
    $verifying = $pay['status'] === 'verifying';
@endphp
<div class="pay-wrap">
    <div class="pay-top">
        <a class="pay-back" href="{{ route('customer.orders') }}" aria-label="Back"><i class="fa-solid fa-chevron-left"></i></a>
        <h1>Payment</h1>
        <span style="width:40px"></span>
    </div>

    @if(session('success'))<div class="alert alert-success rounded-4 border-0">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger rounded-4 border-0">{{ session('error') }}</div>@endif

    <div class="pay-hero">
        <span class="pay-chip {{ $chip }}">{{ $pay['status_label'] }}</span>
        <div class="lbl mt-3">{{ $due['kind'] === 'advance' ? 'Advance to pay now' : ($due['kind'] === 'balance' ? 'Balance to pay' : 'Amount to pay') }}</div>
        <div class="amt">₹{{ number_format($due['amount'], 2) }}</div>
        <div class="lbl">Order #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} &middot; {{ $order->display_title }}</div>

        @if($due['kind'] === 'advance')
            <div class="pay-steps">
                <div class="pay-step"><small>Advance now ({{ rtrim(rtrim(number_format($due['percent'], 2), '0'), '.') }}%)</small><b>₹{{ number_format($due['amount'], 2) }}</b></div>
                <div class="pay-step dim"><small>Balance after pickup</small><b>₹{{ number_format($due['balance_after'], 2) }}</b></div>
            </div>
        @endif
    </div>

    <div class="pay-card">
        <h2>Bill</h2>
        <div class="pay-row"><span>Order amount</span><b>₹{{ number_format($pay['order_amount'], 2) }}</b></div>
        @if($pay['discount'] > 0)<div class="pay-row"><span class="text-success"><i class="fa-solid fa-gift me-1"></i>Rewards discount</span><b class="text-success">-₹{{ number_format($pay['discount'], 2) }}</b></div>@endif
        @if($pay['paid'] > 0)<div class="pay-row"><span>Already paid</span><b class="text-success">₹{{ number_format($pay['paid'], 2) }}</b></div>@endif
        <div class="pay-row" style="border-top:1px dashed #E2E8F0;margin-top:6px;padding-top:10px;"><span><b>Total remaining</b></span><b>₹{{ number_format($pay['remaining'], 2) }}</b></div>
    </div>

    @if($pay['discount'] == 0 && $pay['paid'] <= 0 && ($user->reward_coins ?? 0) > 0)
        <div class="pay-card" style="border-style:dashed;border-color:#4A9A58;">
            <div class="d-flex justify-content-between align-items-center gap-2">
                <div><b style="color:#3D8B4D;"><i class="fa-solid fa-gift"></i> Available rewards</b><div class="text-muted small">Use <b>{{ $user->reward_coins }}</b> coins for ₹{{ number_format($user->reward_coins * $coinValue, 1) }} off</div></div>
                <form action="{{ route('customer.payment.apply-rewards', $order->id) }}" method="POST" class="m-0">@csrf<button class="btn btn-sm btn-primary rounded-pill px-3">Apply</button></form>
            </div>
        </div>
    @endif

    @if($verifying)
        <div class="pay-card text-center">
            <i class="fa-solid fa-hourglass-half" style="font-size:2rem;color:#7A4CE0;"></i>
            <h2 class="mt-2">Request raised</h2>
            <p class="text-muted mb-2">We received your payment details and are verifying them. You will see it here as soon as it is confirmed.</p>
            <a class="btn btn-outline-primary rounded-pill" href="{{ route('customer.payment.status', $order->id) }}">View status</a>
        </div>
    @elseif(count($methods) === 0)
        <div class="pay-card text-center text-muted">No payment method is available right now. Please contact support.</div>
    @else
        <div class="pay-card">
            <h2>Choose how to pay</h2>
            @foreach($methods as $i => $m)
                <label class="method {{ $i === 0 ? 'active' : '' }}" data-m="{{ $m['key'] }}">
                    <input type="radio" name="method" value="{{ $m['key'] }}" {{ $i === 0 ? 'checked' : '' }}>
                    <div class="ic"><i class="fa-solid {{ $icons[$m['key']] ?? 'fa-wallet' }}"></i></div>
                    <div><b>{{ $m['label'] }}</b><small>{{ $m['hint'] }}</small></div>
                    <span class="dot"></span>
                </label>
                <div class="mpanel {{ $i === 0 ? 'show' : '' }}" data-p="{{ $m['key'] }}">
                    @if($m['key'] === 'upi')
                        <div class="copyline"><div><span>UPI ID</span><br><b id="upiId">{{ $m['upi_id'] }}</b></div><button type="button" class="btn btn-sm btn-outline-primary js-copy" data-copy="{{ $m['upi_id'] }}">Copy</button></div>
                        <div class="copyline"><div><span>Pay to</span><br><b>{{ $m['upi_name'] }}</b></div></div>
                        <div class="copyline"><div><span>Amount</span><br><b>₹{{ number_format($due['amount'], 2) }}</b></div>
                            <a class="btn btn-sm btn-primary" href="upi://pay?pa={{ urlencode($m['upi_id']) }}&pn={{ urlencode($m['upi_name']) }}&am={{ $due['amount'] }}&cu=INR&tn={{ urlencode('Order '.$order->id) }}">Open UPI app</a></div>
                        <label class="form-label small fw-semibold mt-3 mb-1">UTR / transaction number</label>
                        <input type="text" class="form-control" id="ref_upi" maxlength="40" placeholder="e.g. 412345678901">
                    @elseif($m['key'] === 'bank')
                        @foreach(['Account holder' => 'bank_holder', 'Bank' => 'bank_name', 'Account number' => 'bank_account', 'IFSC' => 'bank_ifsc'] as $label => $k)
                            @if(!empty($m[$k]))<div class="copyline"><div><span>{{ $label }}</span><br><b>{{ $m[$k] }}</b></div><button type="button" class="btn btn-sm btn-outline-primary js-copy" data-copy="{{ $m[$k] }}">Copy</button></div>@endif
                        @endforeach
                        <div class="copyline"><div><span>Amount</span><br><b>₹{{ number_format($due['amount'], 2) }}</b></div></div>
                        <label class="form-label small fw-semibold mt-3 mb-1">UTR / reference number</label>
                        <input type="text" class="form-control" id="ref_bank" maxlength="40" placeholder="e.g. N123456789012">
                    @elseif($m['key'] === 'cash')
                        <div>Our executive will collect <b>₹{{ number_format($due['amount'], 2) }}</b> in cash. Your payment is marked complete once our team confirms it.</div>
                    @else
                        <div>You will pay <b>₹{{ number_format($due['amount'], 2) }}</b> securely with Razorpay. Your order updates automatically.</div>
                    @endif
                </div>
            @endforeach
            @if(!empty(\App\Models\SiteSetting::get('pay_instructions')))<div class="text-muted small mt-1"><i class="fa-solid fa-circle-info me-1"></i>{{ \App\Models\SiteSetting::get('pay_instructions') }}</div>@endif
        </div>
    @endif

    @if($pay['payments'])
        <div class="pay-card">
            <h2>Payment history</h2>
            @foreach($pay['payments'] as $p)
                <div class="pay-row"><span>{{ ucfirst($p['kind']) }} &middot; {{ $p['method_label'] }}</span><b>₹{{ number_format($p['amount'], 2) }} <small class="text-muted">{{ $p['status'] === 'paid' ? '✔' : ($p['status'] === 'pending' ? '⏳' : '✖') }}</small></b></div>
            @endforeach
        </div>
    @endif
</div>

@if(!$verifying && count($methods))
<div class="pay-bottom"><div class="pay-bottom-in">
    <button type="button" class="pay-btn" id="payBtn">Pay ₹{{ number_format($due['amount'], 2) }}</button>
    <div class="text-center text-muted small mt-2"><i class="fa-solid fa-lock me-1"></i> Secure payment</div>
</div></div>
@endif

<form id="rzpForm" action="{{ route('customer.payment.callback') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="order_id" value="{{ $order->id }}">
    <input type="hidden" name="razorpay_payment_id" id="rzp_pid"><input type="hidden" name="razorpay_order_id" id="rzp_oid"><input type="hidden" name="razorpay_signature" id="rzp_sig">
</form>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(function () {
    const orderId = {{ $order->id }};
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const btn = document.getElementById('payBtn');
    const toast = t => (window.sdToast ? sdToast(t) : alert(t));
    let method = document.querySelector('.method.active')?.dataset.m;

    document.querySelectorAll('.method').forEach(el => el.addEventListener('click', () => {
        method = el.dataset.m;
        document.querySelectorAll('.method').forEach(x => x.classList.toggle('active', x === el));
        document.querySelectorAll('.mpanel').forEach(p => p.classList.toggle('show', p.dataset.p === method));
    }));
    document.querySelectorAll('.js-copy').forEach(b => b.addEventListener('click', async () => {
        try { await navigator.clipboard.writeText(b.dataset.copy); toast('Copied'); } catch (e) { toast(b.dataset.copy); }
    }));

    async function post(url, body) {
        const r = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, credentials: 'same-origin', body: JSON.stringify(body) });
        let d = {}; try { d = await r.json(); } catch (e) {}
        return d;
    }

    if (btn) btn.addEventListener('click', async function () {
        btn.disabled = true; const label = btn.textContent;
        try {
            if (method === 'razorpay') {
                btn.textContent = 'Starting secure payment…';
                const d = await post("{{ url('/customer/payment/gateway-order') }}", { order_id: orderId });
                if (d.status !== 1) { toast(d.message || 'Could not start the payment'); return; }
                const g = d.data;
                new Razorpay({
                    key: g.key_id, amount: g.amount_paise, currency: g.currency, order_id: g.gateway_order_id,
                    name: 'Scrap Daddy', description: (g.kind === 'advance' ? 'Advance' : 'Payment') + ' - Order #' + orderId,
                    image: "{{ $site['logo_url'] }}",
                    prefill: { name: @json($user->full_name ?? ''), contact: @json($user->phone_number ?? '') },
                    theme: { color: '#3D8B4D' },
                    handler: function (resp) {
                        document.getElementById('rzp_pid').value = resp.razorpay_payment_id;
                        document.getElementById('rzp_oid').value = resp.razorpay_order_id;
                        document.getElementById('rzp_sig').value = resp.razorpay_signature;
                        document.getElementById('rzpForm').submit();
                    },
                    modal: { ondismiss: function () { btn.disabled = false; btn.textContent = label; } }
                }).on('payment.failed', r => toast('Payment failed: ' + r.error.description)).open();
                return;
            }

            const reference = method === 'upi' ? document.getElementById('ref_upi')?.value.trim() : (method === 'bank' ? document.getElementById('ref_bank')?.value.trim() : '');
            if ((method === 'upi' || method === 'bank') && !/^[A-Za-z0-9\-_\/]{6,40}$/.test(reference || '')) { toast('Enter the UTR / transaction number (6-40 letters or digits).'); return; }
            btn.textContent = 'Submitting…';
            const d = await post("{{ url('/customer/payment/direct') }}", { order_id: orderId, method: method, reference: reference || null });
            if (d.status === 1) { window.location.href = "{{ route('customer.payment.status', $order->id) }}"; return; }
            toast(d.message === 'Validation error' ? (Object.values(d.data?.errors || {})[0]?.[0] || d.message) : (d.message || 'Could not submit'));
        } finally {
            if (!document.hidden) { btn.disabled = false; btn.textContent = label; }
        }
    });
})();
</script>
@endsection
