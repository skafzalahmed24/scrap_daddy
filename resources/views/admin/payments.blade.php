@extends('layouts.admin')

@section('title', 'Payments')

@php
    $pill = fn ($s) => '<span class="pr-pill pill-'.e($s).'">'.e(ucfirst($s)).'</span>';
    $tabs = ['' => ['All', $stats['paid_count'] + $stats['pending_count']], 'verify' => ['To verify', $stats['verify_count']], 'partial' => ['Advance paid', $stats['advance_count']], 'paid' => ['Paid', $stats['paid_count']], 'pending' => ['Awaiting payment', $stats['pending_count']]];
    $active = $status ?? '';
@endphp

@section('content')
<div class="mb-3">
    <h1 class="page-title mb-1">Payments</h1>
    <div class="pr-sub">Track what has been paid, what is still awaited, and new payments from the last 24 hours</div>
</div>

<div class="pr-kpis">
    <div class="pr-kpi"><div class="ic ic-green"><i class="fa-solid fa-indian-rupee-sign"></i></div><div class="lbl">Total received (net)</div><div class="val">₹{{ number_format($stats['received'], 0) }}</div><div class="sub">₹{{ number_format($stats['received_today'], 0) }} today</div></div>
    <a class="pr-kpi" href="{{ route('admin.payments.index', ['status' => 'pending']) }}"><div class="ic ic-rose"><i class="fa-solid fa-hourglass-half"></i></div><div class="lbl">Awaiting payment</div><div class="val">₹{{ number_format($stats['pending_amount'], 0) }}</div><div class="sub">{{ $stats['pending_count'] }} pickup(s)</div></a>
    <a class="pr-kpi" href="{{ route('admin.payments.index', ['status' => 'paid']) }}"><div class="ic ic-blue"><i class="fa-solid fa-circle-check"></i></div><div class="lbl">Payments received</div><div class="val">{{ $stats['paid_count'] }}</div><div class="sub">{{ $stats['new_count'] }} new in last 24h</div></a>
    <div class="pr-kpi"><div class="ic ic-amber"><i class="fa-solid fa-gift"></i></div><div class="lbl">Rewards redeemed</div><div class="val">₹{{ number_format($stats['rewards_used'], 0) }}</div><div class="sub">discounts given</div></div>
</div>

<div class="pr-tabs">
    @foreach($tabs as $key => [$label, $count])
        <a class="pr-tab {{ $active === $key ? 'active' : '' }}" href="{{ route('admin.payments.index', array_filter(['status' => $key, 'search' => request('search')])) }}">{{ $label }} <b>{{ $count }}</b></a>
    @endforeach
</div>

<div class="pr-card mb-3" style="padding:14px 16px;">
    <form action="{{ route('admin.payments.index') }}" method="GET" class="d-flex gap-2">
        @if($active)<input type="hidden" name="status" value="{{ $active }}">@endif
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
            <input type="text" name="search" class="form-control border-start-0" placeholder="Search by order #, payment ID, customer or phone" value="{{ request('search') }}">
        </div>
        <button class="btn btn-primary px-4">Search</button>
        @if(request('search'))<a href="{{ route('admin.payments.index', array_filter(['status' => $active])) }}" class="btn btn-light">Clear</a>@endif
    </form>
</div>

<div class="pr-card" style="padding:0;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th class="ps-4">Order</th><th>Customer</th><th>Method</th><th class="text-end">Amount</th><th class="text-end">Reward</th><th class="text-end">Net</th><th>Status</th><th>Date</th><th class="pe-4 text-end">Action</th></tr></thead>
            <tbody>
            @forelse($payments as $p)
                @php
                    $paid = $p->payment_status === 'completed';
                    $netAmt = ($p->total_amount ?? 0) - ($p->discount_applied ?? 0);
                    $isNew = $paid && $p->updated_at->gt(now()->subDay());
                    $detail = [
                        'code' => '#'.str_pad($p->id, 4, '0', STR_PAD_LEFT), 'title' => $p->display_title,
                        'customer' => $p->user->full_name ?? 'Customer', 'phone' => $p->user->phone_number ?? '-',
                        'amount' => (float) $p->total_amount, 'discount' => (float) ($p->discount_applied ?? 0), 'net' => (float) $netAmt,
                        'status' => $paid ? 'paid' : 'pending', 'payment_id' => $p->payment_id, 'coins_earned' => $p->coins_earned, 'coins_redeemed' => $p->coins_redeemed,
                        'date' => $p->updated_at->format('d M Y, h:i A'), 'order_url' => route('admin.orders.show', $p->id),
                    ];
                @endphp
                <tr>
                    <td class="ps-4"><span class="pr-id">#{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</span><div class="pr-sub">{{ $p->display_title }}</div></td>
                    <td><div class="pr-person"><div class="pr-avatar">{{ strtoupper(mb_substr($p->user->full_name ?? 'C', 0, 1)) }}</div><div><b>{{ $p->user->full_name ?? 'Customer' }}</b><small>{{ $p->user->phone_number ?? '' }}</small></div></div></td>
                    <td>{{ \App\Models\OrderPayment::METHODS[$p->payment_method] ?? '-' }}</td>
                    <td class="text-end pr-money">₹{{ number_format($p->total_amount, 0) }}</td>
                    <td class="text-end" style="color:#C42B45;">{{ ($p->discount_applied ?? 0) > 0 ? '-₹'.number_format($p->discount_applied, 0) : '-' }}</td>
                    <td class="text-end pr-money">₹{{ number_format($netAmt, 0) }}</td>
                    <td><span class="pr-pill pill-{{ ['paid' => 'paid', 'verifying' => 'pending', 'partial' => 'accepted', 'due' => 'unpaid', 'none' => 'unpaid', 'cancelled' => 'cancelled'][$p->payment_info['status']] ?? 'unpaid' }}">{{ $p->payment_info['label'] }}</span>@if($isNew) <span class="badge" style="background:#FEF3DC;color:#B7770A;">NEW</span>@endif</td>
                    <td>{{ $p->updated_at->format('d M Y') }}<div class="pr-sub">{{ $p->updated_at->diffForHumans() }}</div></td>
                    <td class="pe-4 text-end"><button type="button" class="btn-view js-pay-view" data-detail='@json($detail)'>View</button></td>
                </tr>
            @empty
                <tr><td colspan="9"><div class="pr-empty"><i class="fa-regular fa-credit-card fs-3 d-block mb-2"></i>No payments found.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 p-3 border-top" style="border-color:var(--pr-line)!important;">
        <div class="pr-sub">Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }}</div>
        {{ $payments->links('pagination::bootstrap-5') }}
    </div>
</div>

<div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><span id="pmCode"></span> &middot; <span id="pmTitle"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">
                <div id="pmPill" class="mb-2"></div>
                <div class="pr-section">Customer</div>
                <dl class="pr-kv"><dt>Name</dt><dd id="pmCustomer"></dd><dt>Phone</dt><dd id="pmPhone"></dd></dl>
                <div class="pr-section">Payment</div>
                <dl class="pr-kv"><dt>Order amount</dt><dd id="pmAmount"></dd><dt>Reward discount</dt><dd id="pmDiscount"></dd><dt>Net paid</dt><dd id="pmNet"></dd><dt>Payment ID</dt><dd id="pmPayId"></dd><dt>Coins earned</dt><dd id="pmEarned"></dd><dt>Coins redeemed</dt><dd id="pmRedeemed"></dd><dt>Updated</dt><dd id="pmDate"></dd></dl>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--pr-line);"><a id="pmOrder" class="btn btn-outline-primary">Open order</a></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = new bootstrap.Modal(document.getElementById('payModal'));
    const $ = id => document.getElementById(id);
    const money = n => '₹' + Number(n).toLocaleString('en-IN');
    document.querySelectorAll('.js-pay-view').forEach(btn => btn.addEventListener('click', () => {
        const d = JSON.parse(btn.dataset.detail);
        $('pmCode').textContent = d.code; $('pmTitle').textContent = d.title;
        $('pmPill').innerHTML = '<span class="pr-pill pill-' + d.status + '">' + (d.status === 'paid' ? 'Paid' : 'Awaiting payment') + '</span>';
        $('pmCustomer').textContent = d.customer; $('pmPhone').textContent = d.phone;
        $('pmAmount').textContent = money(d.amount); $('pmDiscount').textContent = d.discount > 0 ? '-' + money(d.discount) : '-';
        $('pmNet').textContent = money(d.net); $('pmPayId').textContent = d.payment_id || '-';
        $('pmEarned').textContent = d.coins_earned ?? 0; $('pmRedeemed').textContent = d.coins_redeemed ?? 0; $('pmDate').textContent = d.date;
        $('pmOrder').href = d.order_url;
        modal.show();
    }));
});
</script>
@endpush
