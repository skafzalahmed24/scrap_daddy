@extends('layouts.admin')

@section('title', 'Bulk Enquiries')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0">Bulk Enquiries</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

{{-- ===== Inbox ===== --}}
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 mb-3">
            @php $cur = request('status'); @endphp
            <a href="{{ route('admin.bulk-enquiries.index') }}" class="btn btn-sm {{ $cur ? 'btn-outline-secondary' : 'btn-dark' }}">All <span class="badge bg-light text-dark">{{ $counts->sum() }}</span></a>
            @foreach(['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'] as $k => $label)
                <a href="{{ route('admin.bulk-enquiries.index', ['status' => $k]) }}" class="btn btn-sm {{ $cur === $k ? 'btn-dark' : 'btn-outline-secondary' }}">{{ $label }} <span class="badge bg-light text-dark">{{ $counts[$k] ?? 0 }}</span></a>
            @endforeach
        </div>

        @forelse($enquiries as $e)
            <div class="border rounded-3 p-3 mb-3">
                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <div>
                        <div class="fw-bold">{{ $e->name }}
                            <span class="badge ms-1 {{ $e->status === 'new' ? 'bg-warning text-dark' : ($e->status === 'contacted' ? 'bg-primary' : 'bg-secondary') }}">{{ ucfirst($e->status) }}</span>
                        </div>
                        <a href="tel:{{ $e->phone }}" class="text-decoration-none"><i class="fa-solid fa-phone me-1"></i>{{ $e->phone }}</a>
                        <div class="text-muted small">{{ $e->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <form method="POST" action="{{ route('admin.bulk-enquiries.update', $e->id) }}" class="d-flex flex-wrap gap-2 align-items-start">
                        @csrf
                        <select name="status" class="form-select form-select-sm" style="width:auto;">
                            @foreach(['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'] as $k => $label)
                                <option value="{{ $k }}" {{ $e->status === $k ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="admin_note" class="form-control form-control-sm" style="width:220px;" maxlength="1000" placeholder="Internal note" value="{{ $e->admin_note }}">
                        <button class="btn btn-sm btn-success">Save</button>
                    </form>
                </div>
                @if(!empty($e->answers))
                    <div class="row g-2 mt-2">
                        @foreach($e->answers as $a)
                            <div class="col-md-4"><div class="small text-muted">{{ $a['label'] }}</div><div class="fw-semibold">{{ $a['value'] }}</div></div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center text-muted py-4"><i class="fa-solid fa-inbox fa-2x mb-2 opacity-50"></i><div>No enquiries yet.</div></div>
        @endforelse

        {{ $enquiries->links() }}
    </div>
</div>

{{-- ===== Form builder ===== --}}
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-1">Enquiry form fields</h5>
        <p class="text-muted small">Name and phone number are always asked. Add the extra questions you need, including dropdowns. Changes show in the website and the app straight away.</p>

        @foreach($fields as $f)
            <form method="POST" action="{{ route('admin.bulk-enquiries.fields.update', $f->id) }}" class="row g-2 align-items-end border-bottom pb-3 mb-3">
                @csrf
                <div class="col-md-3"><label class="form-label small mb-1">Question</label><input type="text" name="label" class="form-control form-control-sm" value="{{ $f->label }}" maxlength="120" required></div>
                <div class="col-md-2"><label class="form-label small mb-1">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        @foreach(\App\Models\BulkEnquiryField::TYPES as $k => $label)<option value="{{ $k }}" {{ $f->type === $k ? 'selected' : '' }}>{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-3"><label class="form-label small mb-1">Dropdown choices</label><input type="text" name="options" class="form-control form-control-sm" value="{{ implode(', ', $f->options ?? []) }}" placeholder="Paper, Plastic, Metal"></div>
                <div class="col-md-1"><label class="form-label small mb-1">Order</label><input type="number" name="sort" min="0" max="999" class="form-control form-control-sm" value="{{ $f->sort }}"></div>
                <div class="col-md-3 d-flex flex-wrap gap-2 align-items-center">
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="required" value="1" id="req{{ $f->id }}" {{ $f->required ? 'checked' : '' }}><label class="form-check-label small" for="req{{ $f->id }}">Required</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="act{{ $f->id }}" {{ $f->is_active ? 'checked' : '' }}><label class="form-check-label small" for="act{{ $f->id }}">Show</label></div>
                    <button class="btn btn-sm btn-success">Save</button>
                    <button type="submit" form="del{{ $f->id }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this field?')"><i class="fa-solid fa-trash"></i></button>
                </div>
            </form>
            <form id="del{{ $f->id }}" method="POST" action="{{ route('admin.bulk-enquiries.fields.destroy', $f->id) }}" class="d-none">@csrf @method('DELETE')</form>
        @endforeach

        <form method="POST" action="{{ route('admin.bulk-enquiries.fields.store') }}" class="row g-2 align-items-end bg-light rounded-3 p-3">
            @csrf
            <div class="col-12"><b class="small">Add a question</b></div>
            <div class="col-md-3"><input type="text" name="label" class="form-control form-control-sm" placeholder="e.g. Preferred pickup date" maxlength="120" required></div>
            <div class="col-md-2">
                <select name="type" class="form-select form-select-sm">
                    @foreach(\App\Models\BulkEnquiryField::TYPES as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3"><input type="text" name="options" class="form-control form-control-sm" placeholder="Dropdown choices, comma separated"></div>
            <div class="col-md-1"><input type="number" name="sort" min="0" max="999" class="form-control form-control-sm" placeholder="Order"></div>
            <div class="col-md-3 d-flex gap-2 align-items-center">
                <div class="form-check"><input class="form-check-input" type="checkbox" name="required" value="1" id="reqNew"><label class="form-check-label small" for="reqNew">Required</label></div>
                <button class="btn btn-sm btn-success fw-bold"><i class="fa-solid fa-plus me-1"></i> Add</button>
            </div>
        </form>
    </div>
</div>
@endsection
