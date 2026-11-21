@extends('layouts.admin')

@section('title', 'Orders')

@php
    $initial = fn ($n) => strtoupper(mb_substr(trim($n ?: 'C'), 0, 1));
    $pill = fn ($s) => '<span class="pr-pill pill-'.e($s).'">'.e(ucfirst(str_replace('_', ' ', $s))).'</span>';
    $tabs = ['' => 'All', 'pending' => 'Pending', 'accepted' => 'Accepted', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
    $active = request('status', '');
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3">
    <div>
        <h1 class="page-title mb-1">Orders</h1>
        <div class="pr-sub">Key details here &middot; open <b>View</b> for the address, photos, items and status actions</div>
    </div>
</div>

@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

<div class="pr-tabs">
    @foreach($tabs as $key => $label)
        <a class="pr-tab {{ $active === $key ? 'active' : '' }}" href="{{ route('admin.orders.index', array_filter(['status' => $key, 'search' => request('search')])) }}">
            {{ $label }} <b>{{ $key === '' ? $counts->sum() : ($counts[$key] ?? 0) }}</b>
        </a>
    @endforeach
</div>

<div class="pr-card mb-3" style="padding:14px 16px;">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="d-flex gap-2">
        @if($active)<input type="hidden" name="status" value="{{ $active }}">@endif
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
            <input type="text" name="search" class="form-control border-start-0" placeholder="Search by order #, customer name, phone or PIN" value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-primary px-4">Search</button>
        @if(request('search'))<a href="{{ route('admin.orders.index', array_filter(['status' => $active])) }}" class="btn btn-light">Clear</a>@endif
    </form>
</div>

<div class="pr-card" style="padding:0;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th class="ps-4">Order</th><th>Customer</th><th>Pickup</th><th>Status</th><th>Payment</th><th class="text-end">Amount</th><th class="pe-4 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                @php
                    $items = $order->items_with_images;
                    $first = $items[0]['name'] ?? $order->display_title;
                    $more = max(0, count($items) - 1);
                    $info = $order->payment_info;
                @endphp
                <tr>
                    <td class="ps-4">
                        <span class="pr-id">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                        <div class="pr-sub">{{ $first }}@if($more) <b>+{{ $more }} more</b>@endif</div>
                    </td>
                    <td>
                        <div class="pr-person"><div class="pr-avatar">{{ $initial($order->user->full_name ?? '') }}</div>
                            <div><b>{{ $order->user->full_name ?? 'Customer' }}</b><small>{{ $order->user->phone_number ?? '' }}</small></div></div>
                    </td>
                    <td>{{ $order->pickup_date ? $order->pickup_date->format('d M Y') : '-' }}<div class="pr-sub">{{ $order->pickup_time }}</div></td>
                    <td>{!! $pill($order->status) !!}</td>
                    <td><span class="pr-pill pill-{{ ['paid' => 'paid', 'verifying' => 'pending', 'partial' => 'accepted', 'due' => 'unpaid', 'none' => 'unpaid', 'cancelled' => 'cancelled'][$info['status']] ?? 'unpaid' }}">{{ $info['label'] }}</span></td>
                    <td class="text-end pr-money">{{ $order->total_amount > 0 ? '₹'.number_format($order->total_amount, 0) : '-' }}</td>
                    <td class="pe-4 text-end"><a class="btn-view text-decoration-none" href="{{ route('admin.orders.show', $order->id) }}">View</a></td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="pr-empty"><i class="fa-regular fa-folder-open fs-3 d-block mb-2"></i>No orders found.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 p-3 border-top" style="border-color:var(--pr-line)!important;">
        <div class="pr-sub">Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }}</div>
        {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
