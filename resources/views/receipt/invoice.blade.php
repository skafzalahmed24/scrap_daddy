<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt {{ $invoiceNo }} - Scrapdaddy</title>
    <style>
        :root { --brand:#3D8B4D; --ink:#14201A; --muted:#64748B; --line:#D8E8D9; --soft:#F3FBF4; }
        * { box-sizing: border-box; }
        body { margin:0; background:#EEF2EE; color:var(--ink); font:14px/1.45 -apple-system,"Segoe UI",Roboto,Inter,Arial,sans-serif; }
        .bar { position:sticky; top:0; z-index:5; background:#fff; border-bottom:1px solid var(--line); padding:10px 16px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap; }
        .bar a, .bar button { border:0; border-radius:10px; padding:10px 18px; font-weight:700; font-size:14px; cursor:pointer; text-decoration:none; }
        .btn-pdf { background:var(--brand); color:#fff; }
        .btn-back { background:#E8EEE8; color:var(--ink); }
        .page { max-width:800px; margin:18px auto; background:#fff; border:1px solid var(--line); border-radius:14px; padding:34px 38px; }
        .head { display:flex; justify-content:space-between; align-items:center; border-bottom:3px solid var(--brand); padding-bottom:14px; }
        .head img { height:68px; }
        .head .t { text-align:right; } .head .t b { color:var(--brand); font-size:17px; letter-spacing:.04em; } .head .t div { color:var(--muted); font-size:12px; }
        .tag { display:inline-block; background:#FEF3DC; color:#8A5A07; border-radius:20px; padding:2px 10px; font-size:11px; font-weight:700; margin-left:6px; }
        .meta { display:grid; grid-template-columns:1fr 1fr; gap:4px 28px; margin:18px 0 4px; }
        .meta div { display:flex; justify-content:space-between; gap:10px; padding:3px 0; }
        .meta span { color:var(--muted); } .meta b { text-align:right; }
        h3 { margin:22px 0 8px; background:var(--soft); color:var(--brand); font-size:12px; letter-spacing:.07em; text-transform:uppercase; padding:6px 10px; border-radius:6px; }
        .kv { display:flex; justify-content:space-between; gap:16px; padding:6px 2px; border-bottom:1px solid var(--line); }
        .kv span { color:var(--muted); } .kv b { text-align:right; max-width:70%; }
        table { width:100%; border-collapse:collapse; } th { text-align:left; color:var(--muted); font-size:12px; padding:6px 4px; border-bottom:1px solid var(--line); }
        td { padding:8px 4px; border-bottom:1px solid var(--line); } td.r, th.r { text-align:right; }
        .pay { border:1px solid var(--line); border-radius:10px; padding:10px 14px; margin-bottom:10px; page-break-inside:avoid; }
        .pay .top { display:flex; justify-content:space-between; font-weight:700; }
        .pill { font-size:11px; font-weight:800; letter-spacing:.04em; }
        .pill.paid { color:var(--brand); } .pill.pending { color:#B7770A; } .pill.rejected { color:#D32F2F; }
        .pay .kv { border-bottom:1px dashed var(--line); padding:4px 0; font-size:13px; } .pay .kv:last-child { border:0; }
        .total b { font-size:15px; } .green { color:var(--brand); } .amber { color:#B7770A; }
        .admin-box { margin-top:18px; border:1px dashed #E6A21A; background:#FFFBF0; border-radius:10px; padding:10px 14px; }
        .foot { margin-top:26px; text-align:center; color:var(--muted); font-size:12px; border-top:1px solid var(--line); padding-top:12px; }
        .foot b { color:var(--brand); font-size:14px; display:block; margin-bottom:2px; }
        @media print {
            body { background:#fff; } .bar { display:none; }
            .page { border:0; border-radius:0; margin:0; max-width:none; padding:0; }
            @page { size:A4; margin:14mm; }
        }
        @media (max-width:600px) { .page { padding:20px 16px; margin:8px; } .meta { grid-template-columns:1fr; } }
    </style>
</head>
<body>
@php
    $inr = fn ($v) => '₹' . number_format((float) $v, 2);
    $user = $order->user;
    $kindLabel = ['advance' => 'Advance payment', 'balance' => 'Balance payment', 'full' => 'Full payment'];
    $payDate = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y, h:i A') : '-';
@endphp

<div class="bar">
    <button class="btn-pdf" onclick="window.print()">Download PDF</button>
    <a class="btn-back" href="{{ $admin ? route('admin.orders.show', $order->id) : route('customer.payment.status', $order->id) }}">Back</a>
</div>

<div class="page">
    <div class="head">
        <img src="{{ asset('images/sd_logo_mark.png') }}" alt="Scrapdaddy">
        <div class="t"><b>PAYMENT RECEIPT / INVOICE @if($admin)<span class="tag">ADMIN COPY</span>@endif</b><div>Pickup. Haul. Dispose.</div></div>
    </div>

    <div class="meta">
        <div><span>Invoice no</span><b>{{ $invoiceNo }}</b></div>
        <div><span>Customer</span><b>{{ $user->full_name ?? '-' }}</b></div>
        <div><span>Order no</span><b>#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</b></div>
        <div><span>Phone</span><b>{{ $user->phone_number ?? '-' }}</b></div>
        <div><span>Order date</span><b>{{ $order->created_at?->format('d M Y, h:i A') }}</b></div>
        <div><span>Order status</span><b>{{ ucfirst($order->status) }}</b></div>
        <div><span>Receipt date</span><b>{{ now()->format('d M Y, h:i A') }}</b></div>
        <div><span>Payment status</span><b>{{ $pay['status_label'] }}</b></div>
    </div>

    <h3>Pickup details</h3>
    <div class="kv"><span>Pickup date</span><b>{{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') : '-' }}</b></div>
    <div class="kv"><span>Time slot</span><b>{{ $order->pickup_time ?: '-' }}</b></div>
    <div class="kv"><span>Address</span><b>{{ $order->pickup_location ?: '-' }}</b></div>
    <div class="kv"><span>Category</span><b>{{ $order->display_title }}</b></div>

    <h3>Items</h3>
    <table>
        <thead><tr><th style="width:36px;">#</th><th>Item</th><th class="r">Quantity</th></tr></thead>
        <tbody>
        @forelse($items as $i => $it)
            <tr><td>{{ $i + 1 }}</td><td>{{ $it['name'] ?? '-' }}</td><td class="r"><b>{{ rtrim(rtrim(number_format((float) ($it['quantity'] ?? 0), 2), '0'), '.') }}</b></td></tr>
        @empty
            <tr><td>1</td><td>{{ $order->display_title }}</td><td class="r">-</td></tr>
        @endforelse
        </tbody>
    </table>
    @if($order->notes)<div class="kv"><span>Notes</span><b>{{ $order->notes }}</b></div>@endif

    <h3>Amount summary</h3>
    <div class="kv"><span>Order amount</span><b>{{ $inr($pay['order_amount']) }}</b></div>
    @if($pay['discount'] > 0)<div class="kv"><span>Reward coins discount</span><b class="green">- {{ $inr($pay['discount']) }}</b></div>@endif
    @php $extra = $pay['total'] - ($pay['order_amount'] - $pay['discount']); @endphp
    @if(abs($extra) > 0.009)<div class="kv"><span>Charges / fees</span><b>{{ $inr($extra) }}</b></div>@endif
    <div class="kv total"><span>Total payable</span><b>{{ $inr($pay['total']) }}</b></div>
    <div class="kv total"><span>Total paid</span><b class="green">{{ $inr($pay['paid']) }}</b></div>
    <div class="kv total"><span>Balance due</span><b class="{{ $pay['remaining'] > 0 ? 'amber' : '' }}">{{ $inr($pay['remaining']) }}</b></div>
    @if(!empty($pay['advance']['enabled']))<div class="kv"><span>Advance policy</span><b>{{ rtrim(rtrim(number_format($pay['advance']['percent'], 2), '0'), '.') }}% advance, balance after pickup</b></div>@endif

    @if(($pay['coins_redeemed'] ?? 0) > 0 || ($pay['coins_earned'] ?? 0) > 0)
        <h3>Rewards</h3>
        @if($pay['coins_redeemed'] > 0)
            <div class="kv"><span>Coins used on this order</span><b>{{ number_format($pay['coins_redeemed']) }} coins</b></div>
            <div class="kv"><span>Discount from coins</span><b class="green">- {{ $inr($pay['discount']) }}</b></div>
        @endif
        @if($pay['coins_earned'] > 0)
            <div class="kv"><span>{{ $pay['coins_credited'] ? 'Coins earned (added to wallet)' : 'Coins to be earned after full payment' }}</span><b class="green">{{ number_format($pay['coins_earned']) }} coins</b></div>
        @endif
    @endif

    <h3>Payment transactions</h3>
    @forelse($payments as $n => $p)
        <div class="pay">
            <div class="top"><span>{{ $n + 1 }}. {{ $kindLabel[$p->kind] ?? 'Payment' }}</span><span>{{ $inr($p->amount) }}</span></div>
            <div class="pill {{ $p->status }}">{{ strtoupper($p->status === 'rejected' ? 'rejected' : $p->status) }}</div>
            <div class="kv"><span>Method</span><b>{{ $p->method_label }}</b></div>
            <div class="kv"><span>Date</span><b>{{ $payDate($p->created_at) }}</b></div>
            <div class="kv"><span>{{ $p->method === 'razorpay' ? 'Razorpay payment id' : 'Transaction / UTR no' }}</span><b>{{ $p->reference ?: '-' }}</b></div>
            @if($p->gateway_order_id)<div class="kv"><span>Gateway order id</span><b>{{ $p->gateway_order_id }}</b></div>@endif
            @if($p->note)<div class="kv"><span>Note</span><b>{{ $p->note }}</b></div>@endif
            @if($admin && $p->reviewed_at)<div class="kv"><span>Verified by admin on</span><b>{{ $payDate($p->reviewed_at) }}</b></div>@endif
        </div>
    @empty
        <div class="kv"><span>No payments recorded yet.</span><b></b></div>
    @endforelse

    <h3>Payment recap</h3>
    <div class="kv"><span>Advance paid</span><b>{{ $inr($advancePaid) }}</b></div>
    <div class="kv"><span>Balance / full paid</span><b>{{ $inr($balancePaid) }}</b></div>
    @if($pendingAmt > 0)<div class="kv"><span>Awaiting verification</span><b class="amber">{{ $inr($pendingAmt) }}</b></div>@endif

    @if($admin)
        <div class="admin-box">
            <b>Admin settlement (not shown to the customer)</b>
            <div class="kv"><span>Online (Razorpay) collected</span><b>{{ $inr($onlineGross) }}</b></div>
            <div class="kv"><span>Estimated gateway fee ({{ rtrim(rtrim(number_format($feePct, 2), '0'), '.') }}%)</span><b>- {{ $inr($fee) }}</b></div>
            <div class="kv"><span>Net after gateway fee</span><b>{{ $inr($onlineGross - $fee) }}</b></div>
            <div class="kv"><span>Direct (cash / UPI / bank) received</span><b>{{ $inr($payments->where('status', 'paid')->where('method', '!=', 'razorpay')->sum('amount')) }}</b></div>
        </div>
    @endif

    <div class="foot"><b>Thank you for recycling with Scrapdaddy!</b>Keep the transaction ids above for any payment query. This is a computer-generated receipt and needs no signature. Invoice {{ $invoiceNo }}.</div>
</div>
</body>
</html>
