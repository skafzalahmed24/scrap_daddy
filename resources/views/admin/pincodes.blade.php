@extends('layouts.admin')

@section('title', 'Service PIN Codes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0">Service PIN Codes</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

@if(!$restrictionActive)
    <div class="alert alert-warning">
        <i class="fa-solid fa-triangle-exclamation me-1"></i>
        No enabled PIN codes yet, so pickups are currently accepted from <strong>every</strong> area.
        Add at least one PIN code to restrict service to those areas only.
    </div>
@else
    <div class="alert alert-info">
        <i class="fa-solid fa-circle-info me-1"></i>
        Pickups are accepted <strong>only</strong> from the enabled PIN codes below (website and mobile app).
    </div>
@endif

<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.pincodes.store') }}" class="row g-3">
            @csrf
            <div class="col-md-5">
                <label class="form-label fw-semibold">PIN code(s)</label>
                <textarea name="pin_codes" rows="2" class="form-control" placeholder="560001 or many: 560001, 560002, 560003" required>{{ old('pin_codes') }}</textarea>
                <div class="form-text">6 digits each. Separate several with commas, spaces or new lines.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Area (optional)</label>
                <input type="text" name="area" class="form-control" maxlength="255" value="{{ old('area') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">City (optional)</label>
                <input type="text" name="city" class="form-control" maxlength="255" value="{{ old('city') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-success w-100 fw-bold"><i class="fa-solid fa-plus me-1"></i> Add</button>
            </div>
        </form>
    </div>
</div>

<form method="GET" class="mb-3">
    <div class="input-group" style="max-width: 360px;">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search PIN, area or city">
        <button class="btn btn-outline-secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
    </div>
</form>

<div class="card shadow-sm border-0 rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th class="ps-4">PIN code</th><th>Area</th><th>City</th><th>Status</th><th class="pe-4 text-end">Action</th></tr>
            </thead>
            <tbody>
                @forelse($pincodes as $p)
                <tr>
                    <td class="ps-4 fw-bold">{{ $p->pin_code }}</td>
                    <td>{{ $p->area ?? '—' }}</td>
                    <td>{{ $p->city ?? '—' }}</td>
                    <td><span class="badge {{ $p->status ? 'bg-success' : 'bg-secondary' }}">{{ $p->status ? 'Serviceable' : 'Disabled' }}</span></td>
                    <td class="pe-4 text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <form method="POST" action="{{ route('admin.pincodes.toggle', $p->id) }}">@csrf
                                <button class="btn btn-sm {{ $p->status ? 'btn-outline-secondary' : 'btn-outline-success' }}">{{ $p->status ? 'Disable' : 'Enable' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.pincodes.destroy', $p->id) }}" onsubmit="return confirm('Remove PIN code {{ $p->pin_code }}?');">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No PIN codes added yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $pincodes->links() }}</div>
@endsection
