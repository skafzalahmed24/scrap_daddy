@extends('layouts.admin')

@section('title', 'Scrap Vehicles')

@php
    $img = fn ($t) => asset('img/vehicles/'.(['Bike' => 'bike', 'Auto' => 'auto', 'Car' => 'car', 'Heavy Load' => 'heavy'][$t] ?? 'car').'.png');
    $pill = fn ($s) => '<span class="pr-pill pill-'.e($s).'">'.e(ucfirst($s)).'</span>';
    $tabs = ['' => 'All', 'pending' => 'Pending', 'completed' => 'Completed'];
    $active = request('status', '');
@endphp

@section('content')
<div class="mb-3">
    <h1 class="page-title mb-1">Scrap Vehicles</h1>
    <div class="pr-sub">Vehicle scrap requests from the app and website</div>
</div>

{{-- counts --}}
<div class="pr-kpis" style="grid-template-columns:repeat(auto-fit,minmax(170px,1fr));">
    <div class="pr-kpi"><div class="ic ic-slate"><i class="fa-solid fa-truck-monster"></i></div><div class="lbl">All requests</div><div class="val">{{ $counts->sum() }}</div></div>
    <div class="pr-kpi"><div class="ic ic-amber"><i class="fa-solid fa-clock"></i></div><div class="lbl">Pending</div><div class="val">{{ $counts['pending'] ?? 0 }}</div></div>
    <div class="pr-kpi"><div class="ic ic-green"><i class="fa-solid fa-check"></i></div><div class="lbl">Completed</div><div class="val">{{ $counts['completed'] ?? 0 }}</div></div>
    @foreach(['Bike', 'Auto', 'Car', 'Heavy Load'] as $t)
        <div class="pr-kpi"><img src="{{ $img($t) }}" alt="" style="height:42px;width:auto;margin-bottom:8px;display:block;"><div class="lbl">{{ $t }}</div><div class="val">{{ $typeCounts[$t] ?? 0 }}</div></div>
    @endforeach
</div>

<div class="pr-tabs">
    @foreach($tabs as $key => $label)
        <a class="pr-tab {{ $active === $key ? 'active' : '' }}" href="{{ route('admin.scrap-vehicles.index', array_filter(['status' => $key, 'search' => request('search')])) }}">
            {{ $label }} <b>{{ $key === '' ? $counts->sum() : ($counts[$key] ?? 0) }}</b>
        </a>
    @endforeach
</div>

<div class="pr-card mb-3" style="padding:14px 16px;">
    <form action="{{ route('admin.scrap-vehicles.index') }}" method="GET" class="d-flex gap-2">
        @if($active)<input type="hidden" name="status" value="{{ $active }}">@endif
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
            <input type="text" name="search" class="form-control border-start-0" placeholder="Search by vehicle number, brand, model, customer or phone" value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-primary px-4">Search</button>
        @if(request('search'))<a href="{{ route('admin.scrap-vehicles.index', array_filter(['status' => $active])) }}" class="btn btn-light">Clear</a>@endif
    </form>
</div>

<div class="pr-card" style="padding:0;">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th class="ps-4">Vehicle</th><th>Customer</th><th>Requested</th><th>Status</th><th class="pe-4 text-end">Action</th></tr></thead>
            <tbody>
            @forelse($vehicles as $v)
                <tr>
                    <td class="ps-4"><div class="pr-person"><img class="pr-thumb" src="{{ $img($v->vehicle_type) }}" alt=""><div><b>{{ $v->vehicle_number }}</b><small>{{ $v->vehicle_type }} &middot; {{ $v->vehicle_model ?: '-' }}</small></div></div></td>
                    <td><div class="pr-person"><div class="pr-avatar">{{ strtoupper(mb_substr($v->user->full_name ?? 'C', 0, 1)) }}</div><div><b>{{ $v->user->full_name ?? 'Customer' }}</b><small>{{ $v->user->phone_number ?? '' }}</small></div></div></td>
                    <td>{{ $v->created_at->format('d M Y') }}<div class="pr-sub">{{ $v->created_at->diffForHumans() }}</div></td>
                    <td>{!! $pill($v->status) !!}</td>
                    <td class="pe-4 text-end"><a class="btn-view text-decoration-none" href="{{ route('admin.scrap-vehicles.show', $v->id) }}">View</a></td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="pr-empty"><i class="fa-solid fa-truck-monster fs-3 d-block mb-2"></i>No vehicle requests found.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 p-3 border-top" style="border-color:var(--pr-line)!important;">
        <div class="pr-sub">Showing {{ $vehicles->firstItem() ?? 0 }} to {{ $vehicles->lastItem() ?? 0 }} of {{ $vehicles->total() }}</div>
        {{ $vehicles->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
