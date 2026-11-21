@extends('layouts.admin')

@section('title', 'Dashboard')

@php
    $initial = fn ($n) => strtoupper(mb_substr(trim($n ?: 'C'), 0, 1));
    $pill = fn ($s) => '<span class="pr-pill pill-'.e($s).'">'.e(ucfirst(str_replace('_', ' ', $s))).'</span>';
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-4">
    <div>
        <h1 class="page-title mb-1">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, Admin</h1>
        <div class="pr-sub">{{ now()->format('l, d F Y') }} &middot; everything that needs attention is below</div>
    </div>
    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-primary"><i class="fa-solid fa-bolt me-1"></i> {{ $kpi['orders_pending'] }} pending pickups</a>
</div>

{{-- ============ KPI tiles ============ --}}
<div class="pr-kpis">
    <a class="pr-kpi" href="{{ route('admin.payments.index') }}">
        <div class="ic ic-green"><i class="fa-solid fa-indian-rupee-sign"></i></div>
        <div class="lbl">Revenue (net)</div>
        <div class="val">₹{{ number_format($kpi['revenue'], 0) }}</div>
        <div class="sub">₹{{ number_format($kpi['revenue_month'], 0) }} this month</div>
    </a>
    <a class="pr-kpi" href="{{ route('admin.orders.index') }}">
        <div class="ic ic-blue"><i class="fa-solid fa-truck-fast"></i></div>
        <div class="lbl">Total pickups</div>
        <div class="val">{{ number_format($kpi['orders_total']) }}</div>
        <div class="sub">{{ $kpi['orders_today'] }} today &middot; {{ $kpi['orders_completed'] }} completed</div>
    </a>
    <a class="pr-kpi" href="{{ route('admin.orders.index', ['status' => 'pending']) }}">
        <span class="kpi-badge" data-kpi-badge="orders" {!! $kpi['orders_pending'] ? '' : 'hidden' !!}>{{ $kpi['orders_pending'] }}</span>
        <div class="ic ic-amber"><i class="fa-solid fa-clock"></i></div>
        <div class="lbl">Pending pickups</div>
        <div class="val">{{ number_format($kpi['orders_pending']) }}</div>
        <div class="sub">waiting to be accepted</div>
    </a>
    <a class="pr-kpi" href="{{ route('admin.payments.index', ['status' => 'pending']) }}">
        <span class="kpi-badge" data-kpi-badge="payments" {!! $kpi['payments_pending_count'] ? '' : 'hidden' !!}>{{ $kpi['payments_pending_count'] }}</span>
        <div class="ic ic-rose"><i class="fa-solid fa-hourglass-half"></i></div>
        <div class="lbl">Awaiting payment</div>
        <div class="val">₹{{ number_format($kpi['payments_pending_amount'], 0) }}</div>
        <div class="sub">{{ $kpi['payments_pending_count'] }} completed pickup(s) unpaid</div>
    </a>
    <a class="pr-kpi" href="{{ route('admin.users.index') }}">
        <div class="ic ic-violet"><i class="fa-solid fa-users"></i></div>
        <div class="lbl">Customers</div>
        <div class="val">{{ number_format($kpi['customers']) }}</div>
        <div class="sub">+{{ $kpi['customers_week'] }} in the last 7 days</div>
    </a>
    <a class="pr-kpi" href="{{ route('admin.scrap-vehicles.index', ['status' => 'pending']) }}">
        <span class="kpi-badge" data-kpi-badge="vehicles" {!! $kpi['vehicles_pending'] ? '' : 'hidden' !!}>{{ $kpi['vehicles_pending'] }}</span>
        <div class="ic ic-slate"><i class="fa-solid fa-truck-monster"></i></div>
        <div class="lbl">Scrap vehicles</div>
        <div class="val">{{ number_format($kpi['vehicles_total']) }}</div>
        <div class="sub">{{ $kpi['vehicles_pending'] }} pending</div>
    </a>
    <a class="pr-kpi" href="{{ route('admin.tickets.index') }}">
        <span class="kpi-badge" data-kpi-badge="tickets" {!! $kpi['tickets_open'] ? '' : 'hidden' !!}>{{ $kpi['tickets_open'] }}</span>
        <div class="ic ic-blue"><i class="fa-solid fa-headset"></i></div>
        <div class="lbl">Open tickets</div>
        <div class="val">{{ $kpi['tickets_open'] }}</div>
        <div class="sub">support requests to answer</div>
    </a>
    <a class="pr-kpi" href="{{ route('admin.service-requests.index') }}">
        <span class="kpi-badge" data-kpi-badge="requests" {!! $kpi['requests_new'] ? '' : 'hidden' !!}>{{ $kpi['requests_new'] }}</span>
        <div class="ic ic-amber"><i class="fa-solid fa-location-dot"></i></div>
        <div class="lbl">New area requests</div>
        <div class="val">{{ $kpi['requests_new'] }}</div>
        <div class="sub">{{ $kpi['categories'] }} categories &middot; {{ $kpi['subcategories'] }} items</div>
    </a>
</div>

{{-- ============ Charts ============ --}}
<div class="pr-grid-2">
    <div class="pr-card">
        <div class="pr-card-head"><h3>Revenue {{ date('Y') }}</h3><span class="pr-sub">net of reward discounts</span></div>
        <div class="pr-chart-box"><canvas id="chRevenue"></canvas></div>
    </div>
    <div class="pr-card">
        <div class="pr-card-head"><h3>Pickups by status</h3></div>
        <div class="pr-chart-box"><canvas id="chStatus"></canvas></div>
    </div>
</div>

{{-- ============ Recent pickups ============ --}}
<div class="pr-card mb-3" style="padding:0;">
    <div class="pr-card-head" style="padding:18px 20px 0;"><h3>Latest pickups</h3><a href="{{ route('admin.orders.index') }}">View all orders &rarr;</a></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th class="ps-4">Order</th><th>Customer</th><th>Pickup</th><th>Status</th><th class="text-end">Amount</th><th class="pe-4 text-end"></th></tr></thead>
            <tbody>
            @forelse($recentOrders as $o)
                <tr>
                    <td class="ps-4"><span class="pr-id">#{{ str_pad($o->id, 4, '0', STR_PAD_LEFT) }}</span><div class="pr-sub">{{ $o->display_title }}</div></td>
                    <td><div class="pr-person"><div class="pr-avatar">{{ $initial($o->user->full_name ?? '') }}</div><div><b>{{ $o->user->full_name ?? 'Customer' }}</b><small>{{ $o->user->phone_number ?? '' }}</small></div></div></td>
                    <td>{{ $o->pickup_date ? $o->pickup_date->format('d M Y') : '-' }}<div class="pr-sub">{{ $o->pickup_time }}</div></td>
                    <td>{!! $pill($o->status) !!}</td>
                    <td class="text-end pr-money">{{ $o->total_amount > 0 ? '₹'.number_format($o->total_amount, 0) : '-' }}</td>
                    <td class="pe-4 text-end"><a class="btn-view text-decoration-none" href="{{ route('admin.orders.show', $o->id) }}">View</a></td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="pr-empty">No pickups yet.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============ Payments + Vehicles ============ --}}
<div class="pr-grid-eq">
    <div class="pr-card">
        <div class="pr-card-head"><h3>Awaiting payment</h3><a href="{{ route('admin.payments.index', ['status' => 'pending']) }}">All &rarr;</a></div>
        @forelse($awaitingPayment as $o)
            <div class="pr-mini">
                <div class="left"><div class="pr-avatar">{{ $initial($o->user->full_name ?? '') }}</div><div><b>{{ $o->user->full_name ?? 'Customer' }}</b><small>#{{ str_pad($o->id, 4, '0', STR_PAD_LEFT) }} &middot; {{ $o->updated_at->diffForHumans() }}</small></div></div>
                <span class="pr-money">₹{{ number_format($o->total_amount, 0) }}</span>
            </div>
        @empty
            <div class="pr-empty">All caught up - nothing waiting for payment.</div>
        @endforelse

        <div class="pr-card-head mt-4"><h3>New payments</h3><a href="{{ route('admin.payments.index', ['status' => 'paid']) }}">All &rarr;</a></div>
        @forelse($recentPayments as $o)
            <div class="pr-mini">
                <div class="left"><div class="pr-avatar" style="background:#E4F4E8;color:#2B7A3D;"><i class="fa-solid fa-check"></i></div><div><b>{{ $o->user->full_name ?? 'Customer' }}</b><small>#{{ str_pad($o->id, 4, '0', STR_PAD_LEFT) }} &middot; {{ $o->updated_at->diffForHumans() }}</small></div></div>
                <span class="pr-money">₹{{ number_format($o->total_amount - ($o->discount_applied ?? 0), 0) }}</span>
            </div>
        @empty
            <div class="pr-empty">No payments received yet.</div>
        @endforelse
    </div>

    <div class="pr-card">
        <div class="pr-card-head"><h3>Scrap vehicles</h3><a href="{{ route('admin.scrap-vehicles.index') }}">All &rarr;</a></div>
        @forelse($recentVehicles as $v)
            <div class="pr-mini">
                <div class="left">
                    <img class="pr-thumb" src="{{ asset('img/vehicles/'.(['Bike' => 'bike', 'Auto' => 'auto', 'Car' => 'car', 'Heavy Load' => 'heavy'][$v->vehicle_type] ?? 'car').'.png') }}" alt="">
                    <div><b>{{ $v->vehicle_number }}</b><small>{{ $v->vehicle_type }} &middot; {{ $v->user->full_name ?? 'Customer' }}</small></div>
                </div>
                {!! $pill($v->status) !!}
            </div>
        @empty
            <div class="pr-empty">No vehicle requests yet.</div>
        @endforelse

        <div class="pr-card-head mt-4"><h3>Support tickets</h3><a href="{{ route('admin.tickets.index') }}">All &rarr;</a></div>
        @forelse($openTickets as $t)
            <div class="pr-mini">
                <div class="left"><div class="pr-avatar" style="background:#E8F1FE;color:#2F6FE4;"><i class="fa-solid fa-headset"></i></div><div><b>{{ $t->subject }}</b><small>{{ $t->user->full_name ?? 'Customer' }} &middot; {{ $t->created_at->diffForHumans() }}</small></div></div>
                {!! $pill($t->status) !!}
            </div>
        @empty
            <div class="pr-empty">No open tickets.</div>
        @endforelse
    </div>
</div>

<div class="pr-grid-2">
    <div class="pr-card">
        <div class="pr-card-head"><h3>Pickups - last 7 days</h3></div>
        <div class="pr-chart-box" style="height:220px;"><canvas id="chDays"></canvas></div>
    </div>
    <div class="pr-card">
        <div class="pr-card-head"><h3>Area requests</h3><a href="{{ route('admin.service-requests.index') }}">All &rarr;</a></div>
        @forelse($newRequests as $r)
            <div class="pr-mini">
                <div class="left"><div class="pr-avatar" style="background:#FEF3DC;color:#B7770A;"><i class="fa-solid fa-location-dot"></i></div><div><b>{{ $r->pin_code }} {{ $r->area }}</b><small>{{ $r->name }} &middot; {{ $r->created_at->diffForHumans() }}</small></div></div>
            </div>
        @empty
            <div class="pr-empty">No new area requests.</div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const C = @json($chart);
        Chart.defaults.font.family = 'Inter, sans-serif';
        Chart.defaults.color = '#64748B';
        const grid = { color: 'rgba(148,163,184,.18)' };

        new Chart(document.getElementById('chRevenue'), {
            type: 'bar',
            data: { labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                datasets: [{ data: C.monthlyRevenue, backgroundColor: '#3D8B4D', borderRadius: 8, maxBarThickness: 28 }] },
            options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid }, x: { grid: { display: false } } } }
        });
        new Chart(document.getElementById('chStatus'), {
            type: 'doughnut',
            data: { labels: C.statusLabels, datasets: [{ data: C.statusData, backgroundColor: ['#F5B73B', '#4C8DF6', '#3D8B4D', '#F4627A', '#8B5CF6'], borderWidth: 0 }] },
            options: { maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'bottom' } } }
        });
        new Chart(document.getElementById('chDays'), {
            type: 'line',
            data: { labels: C.dayLabels, datasets: [{ data: C.dayData, borderColor: '#3D8B4D', backgroundColor: 'rgba(61,139,77,.12)', fill: true, tension: .35, pointRadius: 3 }] },
            options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid }, x: { grid: { display: false } } } }
        });
    });
</script>
@endpush
