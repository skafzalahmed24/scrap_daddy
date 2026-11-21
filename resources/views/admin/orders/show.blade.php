@extends('layouts.admin')

@section('title', 'Order #'.str_pad($order->id, 4, '0', STR_PAD_LEFT))

@php
    $code = '#'.str_pad($order->id, 4, '0', STR_PAD_LEFT);
    $pill = fn ($s) => '<span class="pr-pill pill-'.e($s).'">'.e(ucfirst(str_replace('_', ' ', $s))).'</span>';
    $img = fn ($p) => $p ? (preg_match('#^https?://#', $p) ? $p : asset($p)) : null;
    $due = $pay['due_now'];
    $adv = $pay['advance'];
    $statusPill = ['paid' => 'paid', 'verifying' => 'pending', 'partial' => 'accepted', 'due' => 'unpaid', 'none' => 'unpaid', 'cancelled' => 'cancelled'][$pay['status']] ?? 'unpaid';
    $pendingReview = $payments->where('status', 'pending')->where('method', '!=', 'razorpay');
@endphp

@section('content')
<a href="{{ route('admin.orders.index') }}" class="text-decoration-none pr-sub d-inline-block mb-2"><i class="fa-solid fa-arrow-left me-1"></i> All orders</a>

<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <h1 class="page-title mb-1">Order {{ $code }} <span class="pr-sub fw-normal">&middot; {{ $order->display_title }}</span></h1>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            {!! $pill($order->status) !!}
            <span class="pr-pill pill-{{ $statusPill }}">{{ $pay['status_label'] }}</span>
            <span class="pr-sub">Placed {{ $order->created_at->format('d M Y, h:i A') }}</span>
        </div>
    </div>
    <div class="text-end">
        <a class="btn btn-sm btn-outline-primary mb-2" target="_blank" href="{{ route('admin.orders.receipt', $order->id) }}"><i class="fa-solid fa-file-invoice me-1"></i> Receipt / Invoice (PDF)</a>
        <div class="pr-sub">Order amount</div>
        <div class="pr-money" style="font-size:1.7rem;">{{ $order->total_amount > 0 ? '₹'.number_format($order->total_amount, 2) : 'Not set' }}</div>
    </div>
</div>

@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

@if($pendingReview->count())
    <div class="alert" style="background:#FEF3DC;color:#8A5A07;">
        <i class="fa-solid fa-triangle-exclamation me-1"></i> <b>{{ $pendingReview->count() }} payment(s) waiting for your verification</b> - check the money arrived, then approve it below.
    </div>
@endif

<div class="row g-3">
    {{-- ================= left column ================= --}}
    <div class="col-lg-7">
        <div class="pr-card mb-3">
            <div class="pr-card-head"><h3>Items to pick up</h3></div>
            @forelse($order->items_with_images as $i)
                <div class="pr-mini">
                    <div class="left">
                        @if(!empty($i['image']))<img class="pr-thumb" src="{{ $img($i['image']) }}" alt="">@else<div class="pr-avatar"><i class="fa-solid fa-recycle"></i></div>@endif
                        <div><b>{{ $i['name'] ?? 'Item' }}</b></div>
                    </div>
                    <span class="pr-money">Qty {{ $i['quantity'] ?? 1 }}</span>
                </div>
            @empty
                <div class="pr-empty">No items recorded.</div>
            @endforelse
        </div>

        <div class="pr-card mb-3">
            <div class="pr-card-head"><h3>Pickup details</h3></div>
            <dl class="pr-kv">
                <dt>Date</dt><dd>{{ $order->pickup_date ? $order->pickup_date->format('l, d M Y') : '-' }}</dd>
                <dt>Time slot</dt><dd>{{ $order->pickup_time ?? '-' }}</dd>
                <dt>Address</dt><dd>{{ $order->pickup_location ?? '-' }}</dd>
                <dt>PIN code</dt><dd>{{ $order->pin_code ?? '-' }}</dd>
                @if($order->notes)<dt>Customer notes</dt><dd style="white-space:pre-line;">{{ $order->notes }}</dd>@endif
            </dl>
            @if(!empty($order->images))
                <div class="pr-section">Photos</div>
                <div class="pr-photos">
                    @foreach($order->images as $p)<a href="{{ $img($p) }}" target="_blank" rel="noopener"><img src="{{ $img($p) }}" alt="photo"></a>@endforeach
                </div>
            @endif
        </div>

        <div class="pr-card">
            <div class="pr-card-head"><h3>Customer</h3>@if($order->user)<a href="{{ route('admin.users.show', $order->user->uuid) }}">Open profile &rarr;</a>@endif</div>
            <div class="pr-person">
                <div class="pr-avatar">{{ strtoupper(mb_substr($order->user->full_name ?? 'C', 0, 1)) }}</div>
                <div><b>{{ $order->user->full_name ?? 'Customer' }}</b><small>{{ $order->user->phone_number ?? '' }}{{ !empty($order->user->email) ? ' · '.$order->user->email : '' }}</small></div>
            </div>
        </div>
    </div>

    {{-- ================= right column ================= --}}
    <div class="col-lg-5">
        <div class="pr-card mb-3">
            <div class="pr-card-head"><h3>Status &amp; amount</h3></div>
            @if($pay['status'] === 'paid' && $order->status === 'completed')
                <div class="d-flex align-items-center gap-3" style="background:#E4F4E8;border-radius:14px;padding:14px;">
                    <div class="pr-avatar" style="background:#fff;color:#2B7A3D;"><i class="fa-solid fa-lock"></i></div>
                    <div><b style="color:#2B7A3D;">Completed and paid in full</b><div class="pr-sub">No further changes are needed. The amount of ₹{{ number_format($order->total_amount, 2) }} is settled.</div></div>
                </div>
            @else
            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="row g-2">
                @csrf
                <div class="col-sm-6">
                    <label class="form-label small fw-semibold mb-1">Order status</label>
                    <select name="status" class="form-select" id="stSel">
                        @foreach(['pending' => 'Pending', 'accepted' => 'Accepted', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $k => $l)
                            <option value="{{ $k }}" @selected($order->status === $k)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label small fw-semibold mb-1">Amount (₹) <span class="text-muted fw-normal">- agreed / final</span></label>
                    <input type="number" step="0.01" min="0" max="500000" name="total_amount" class="form-control" value="{{ $order->total_amount > 0 ? $order->total_amount : '' }}" placeholder="e.g. 1250" @disabled($order->payment_status === 'completed')>
                </div>
                <div class="col-12 d-grid"><button class="btn btn-primary">Save</button></div>
                @if($adv['enabled'])<div class="form-text">Advance payment is ON ({{ rtrim(rtrim(number_format($adv['percent'], 2), '0'), '.') }}%). Set the amount and the customer is asked for the advance.</div>@endif
            </form>
            @endif
        </div>

        <div class="pr-card mb-3">
            <div class="pr-card-head"><h3>Payment</h3><span class="pr-pill pill-{{ $statusPill }}">{{ $pay['status_label'] }}</span></div>
            <dl class="pr-kv">
                <dt>Order amount</dt><dd>₹{{ number_format($pay['order_amount'], 2) }}</dd>
                <dt>Reward discount</dt><dd>{{ $pay['discount'] > 0 ? '-₹'.number_format($pay['discount'], 2) : '-' }}</dd>
                <dt>Payable total</dt><dd>₹{{ number_format($pay['total'], 2) }}</dd>
                <dt>Received so far</dt><dd style="color:#2B7A3D;">₹{{ number_format($pay['paid'], 2) }}</dd>
                <dt>Balance due</dt><dd>₹{{ number_format($pay['remaining'], 2) }}</dd>
                @if($due['kind'] === 'advance')<dt>Advance asked</dt><dd>₹{{ number_format($due['amount'], 2) }} ({{ rtrim(rtrim(number_format($due['percent'], 2), '0'), '.') }}%)</dd>@endif
                @if($order->advance_amount > 0)<dt>Advance paid</dt><dd>₹{{ number_format($order->advance_amount, 2) }}</dd>@endif
            </dl>

            @if($received->count())
                <div class="pr-section">Paid via</div>
                @foreach($received as $r)
                    <div class="pr-mini" style="align-items:flex-start;">
                        <div class="left" style="align-items:flex-start;">
                            <div class="pr-avatar" style="background:#E4F4E8;color:#2B7A3D;"><i class="fa-solid {{ ['razorpay' => 'fa-credit-card', 'upi' => 'fa-mobile-screen', 'bank' => 'fa-building-columns', 'cash' => 'fa-money-bill-wave'][$r['method']] ?? 'fa-wallet' }}"></i></div>
                            <div>
                                <b>{{ $r['method'] === 'razorpay' ? 'Razorpay' : $r['label'] }} <span class="pr-sub fw-normal">{{ $r['kind'] === 'advance' ? 'advance' : ($r['kind'] === 'balance' ? 'balance' : 'full payment') }}</span></b>
                                @if($r['reference'])<small>{{ $r['method'] === 'razorpay' ? 'Payment ID' : 'Ref' }}: {{ $r['reference'] }}</small>@endif
                                <small class="d-block">{{ $r['at'] ? \Carbon\Carbon::parse($r['at'])->format('d M Y, h:i A') : '' }}</small>
                            </div>
                        </div>
                        <span class="pr-money">₹{{ number_format($r['amount'], 2) }}</span>
                    </div>
                @endforeach
                @if($onlineGross > 0)
                    <dl class="pr-kv mt-2 pt-2" style="border-top:1px dashed var(--pr-line);">
                        <dt>Received online</dt><dd>₹{{ number_format($onlineGross, 2) }}</dd>
                        <dt>Gateway fee ({{ rtrim(rtrim(number_format($feePct, 2), '0'), '.') }}%)</dt><dd style="color:#C42B45;">-₹{{ number_format($fee, 2) }}</dd>
                        <dt>Net to you</dt><dd style="color:#2B7A3D;">₹{{ number_format($onlineGross - $fee, 2) }}</dd>
                    </dl>
                @endif
            @endif

            @if($payments->where('status', '!=', 'paid')->count())
                <div class="pr-section">Pending / rejected payments</div>
                @foreach($payments->where('status', '!=', 'paid') as $p)
                    <div class="pr-mini" style="align-items:flex-start;">
                        <div class="left" style="align-items:flex-start;">
                            <div class="pr-avatar" style="background:#EDF1F6;color:#475569;"><i class="fa-solid {{ ['razorpay' => 'fa-credit-card', 'upi' => 'fa-mobile-screen', 'bank' => 'fa-building-columns', 'cash' => 'fa-money-bill-wave'][$p->method] ?? 'fa-wallet' }}"></i></div>
                            <div>
                                <b>₹{{ number_format($p->amount, 2) }} <span class="pr-sub fw-normal">{{ $p->kind }} · {{ $p->method_label }}</span></b>
                                <small>{{ $p->reference ? 'Ref: '.$p->reference.' · ' : '' }}{{ $p->created_at->format('d M, h:i A') }}</small>
                                @if($p->note)<small class="d-block">{{ $p->note }}</small>@endif
                            </div>
                        </div>
                        <div class="text-end">
                            {!! $pill($p->status === 'paid' ? 'paid' : ($p->status === 'pending' ? 'pending' : 'rejected')) !!}
                            @if($p->status === 'pending' && $p->method !== 'razorpay')
                                <form action="{{ route('admin.order-payments.review', $p->id) }}" method="POST" class="d-flex gap-1 mt-2 justify-content-end">
                                    @csrf
                                    <button name="action" value="approve" class="btn btn-sm btn-primary">Approve</button>
                                    <button name="action" value="reject" class="btn btn-sm btn-outline-danger" onclick="return confirm('Reject this payment?')">Reject</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif

            @if($pay['remaining'] > 0 && $order->status !== 'cancelled' && $order->total_amount > 0)
                <div class="pr-section">Record a payment received</div>
                <form action="{{ route('admin.orders.record-payment', $order->id) }}" method="POST" class="row g-2">
                    @csrf
                    <div class="col-6">
                        <select name="method" class="form-select">
                            <option value="cash">Cash</option><option value="upi">UPI</option><option value="bank">Bank transfer</option>
                        </select>
                    </div>
                    <div class="col-6"><input type="number" step="0.01" min="1" max="{{ $pay['remaining'] }}" name="amount" class="form-control" value="{{ $due['amount'] }}" required></div>
                    <div class="col-12"><input type="text" name="reference" class="form-control" maxlength="60" placeholder="Reference / receipt no. (optional)"></div>
                    <div class="col-12 d-grid"><button class="btn btn-outline-primary">Record payment</button></div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
