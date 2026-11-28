@extends('layouts.admin')

@section('title', 'How it works')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0">How it works &middot; Steps</h1>
    <a href="{{ route('admin.home-content.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Home Content</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<p class="text-muted">These steps are shown in <b>How it works</b> on the website (Home, Services, About) and in the app. The heading above them is edited under Home Content.</p>

@foreach($steps as $st)
    <form method="POST" action="{{ route('admin.how-steps.update', $st->id) }}" class="card shadow-sm border-0 rounded-3 mb-3">
        @csrf
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-3"><label class="form-label small mb-1">Title</label><input type="text" name="title" class="form-control form-control-sm" maxlength="80" value="{{ $st->title }}" required></div>
                <div class="col-md-4"><label class="form-label small mb-1">Description</label><input type="text" name="description" class="form-control form-control-sm" maxlength="300" value="{{ $st->description }}"></div>
                <div class="col-md-2"><label class="form-label small mb-1">Icon</label>
                    <select name="icon" class="form-select form-select-sm">
                        @foreach(\App\Models\HowStep::ICONS as $k => $label)<option value="{{ $k }}" {{ $st->icon === $k ? 'selected' : '' }}>{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="col-6 col-md-1"><label class="form-label small mb-1">Colour</label><input type="color" name="color" class="form-control form-control-sm form-control-color w-100" value="{{ $st->color }}"></div>
                <div class="col-6 col-md-1"><label class="form-label small mb-1">Order</label><input type="number" name="sort" min="0" max="999" class="form-control form-control-sm" value="{{ $st->sort }}"></div>
                <div class="col-md-1 d-flex align-items-center gap-2">
                    <div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="hs{{ $st->id }}" {{ $st->is_active ? 'checked' : '' }}><label class="form-check-label small" for="hs{{ $st->id }}">Show</label></div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-success btn-sm fw-bold">Save</button>
                <button type="submit" form="delhs{{ $st->id }}" class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove this step?')"><i class="fa-solid fa-trash"></i></button>
                <span class="ms-auto d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;border-radius:12px;background:{{ $st->color }};color:#fff;"><i class="fa-solid fa-{{ $st->icon }}"></i></span>
            </div>
        </div>
    </form>
    <form id="delhs{{ $st->id }}" method="POST" action="{{ route('admin.how-steps.destroy', $st->id) }}" class="d-none">@csrf @method('DELETE')</form>
@endforeach

<form method="POST" action="{{ route('admin.how-steps.store') }}" class="card shadow-sm border-0 rounded-3">
    @csrf
    <div class="card-body">
        <h5 class="fw-bold mb-3">Add a step</h5>
        <div class="row g-2 align-items-end">
            <div class="col-md-3"><input type="text" name="title" class="form-control form-control-sm" maxlength="80" placeholder="Title" required></div>
            <div class="col-md-4"><input type="text" name="description" class="form-control form-control-sm" maxlength="300" placeholder="Short description"></div>
            <div class="col-md-2">
                <select name="icon" class="form-select form-select-sm">@foreach(\App\Models\HowStep::ICONS as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach</select>
            </div>
            <div class="col-6 col-md-1"><input type="color" name="color" class="form-control form-control-sm form-control-color w-100" value="#4A9A58"></div>
            <div class="col-6 col-md-1"><input type="number" name="sort" min="0" max="999" class="form-control form-control-sm" placeholder="Order"></div>
            <div class="col-md-1"><button class="btn btn-success btn-sm fw-bold w-100"><i class="fa-solid fa-plus"></i> Add</button></div>
        </div>
    </div>
</form>
@endsection
