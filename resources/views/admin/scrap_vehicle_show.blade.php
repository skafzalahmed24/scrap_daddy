@extends('layouts.admin')

@section('title', 'Vehicle '.$vehicle->vehicle_number)

@php
    $icon = asset('img/vehicles/'.(['Bike' => 'bike', 'Auto' => 'auto', 'Car' => 'car', 'Heavy Load' => 'heavy'][$vehicle->vehicle_type] ?? 'car').'.png');
    $photos = collect($vehicle->photos ?? [])->map(fn ($p) => preg_match('#^https?://#', $p) ? $p : asset($p));
@endphp

@section('content')
<a href="{{ route('admin.scrap-vehicles.index') }}" class="text-decoration-none pr-sub d-inline-block mb-2"><i class="fa-solid fa-arrow-left me-1"></i> All vehicles</a>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <img class="pr-thumb" style="width:64px;height:64px;" src="{{ $icon }}" alt="">
        <div>
            <h1 class="page-title mb-1">{{ $vehicle->vehicle_number }}</h1>
            <span class="pr-pill pill-{{ $vehicle->status }}">{{ ucfirst($vehicle->status) }}</span>
            <span class="pr-sub ms-2">Requested {{ $vehicle->created_at->format('d M Y, h:i A') }}</span>
        </div>
    </div>
    @if($vehicle->status !== 'completed')
        <button class="btn btn-primary" id="completeBtn"><i class="fa-solid fa-check me-1"></i> Mark as completed</button>
    @endif
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="pr-card mb-3">
            <div class="pr-card-head"><h3>Vehicle details</h3></div>
            <dl class="pr-kv">
                <dt>Type</dt><dd>{{ $vehicle->vehicle_type }}</dd>
                <dt>Number</dt><dd>{{ $vehicle->vehicle_number }}</dd>
                <dt>Brand</dt><dd>{{ $vehicle->vehicle_brand ?: '-' }}</dd>
                <dt>Model</dt><dd>{{ $vehicle->vehicle_model ?: '-' }}</dd>
                @if($vehicle->remark)<dt>Remark</dt><dd style="white-space:pre-line;">{{ $vehicle->remark }}</dd>@endif
            </dl>
        </div>
        <div class="pr-card">
            <div class="pr-card-head"><h3>Customer</h3>@if($vehicle->user)<a href="{{ route('admin.users.show', $vehicle->user->uuid) }}">Open profile &rarr;</a>@endif</div>
            <div class="pr-person"><div class="pr-avatar">{{ strtoupper(mb_substr($vehicle->user->full_name ?? 'C', 0, 1)) }}</div>
                <div><b>{{ $vehicle->user->full_name ?? 'Customer' }}</b><small>{{ $vehicle->user->phone_number ?? '' }}</small></div></div>
            @if($vehicle->user && $vehicle->user->phone_number)
                <a class="btn btn-outline-primary mt-3" href="tel:{{ preg_replace('/[^0-9+]/', '', $vehicle->user->phone_number) }}"><i class="fa-solid fa-phone me-1"></i> Call customer</a>
            @endif
        </div>
    </div>
    <div class="col-lg-6">
        <div class="pr-card">
            <div class="pr-card-head"><h3>Photos</h3><span class="pr-sub">{{ $photos->count() }}</span></div>
            @if($photos->count())
                <div class="pr-photos">@foreach($photos as $p)<a href="{{ $p }}" target="_blank" rel="noopener"><img src="{{ $p }}" alt="vehicle photo" style="width:120px;height:120px;"></a>@endforeach</div>
            @else
                <div class="pr-empty">No photos were uploaded.</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
<script>
    const btn = document.getElementById('completeBtn');
    if (btn) btn.addEventListener('click', function () {
        Swal.fire({ title: 'Mark as completed?', icon: 'question', showCancelButton: true, confirmButtonColor: '#3D8B4D', confirmButtonText: 'Yes, complete it' }).then(function (r) {
            if (!r.isConfirmed) return;
            fetch("{{ url('/api/admin/scrap-vehicles/status') }}", { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ id: {{ $vehicle->id }}, status: 'completed' }) })
                .then(function (res) { return res.json(); })
                .then(function (d) { if (d.status === 1) location.reload(); else Swal.fire('Error', d.message || 'Could not update', 'error'); })
                .catch(function () { Swal.fire('Error', 'Failed to update status', 'error'); });
        });
    });
</script>
@endpush
