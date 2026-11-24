@extends('layouts.admin')

@section('title', $sectionLabel . ' banners')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0">{{ $sectionLabel }} &middot; Banners</h1>
    <a href="{{ route('admin.home-content.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Home Content</a>
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

<p class="text-muted">These banners scroll sideways under the section title on the website and the app. Use wide images (about 1000 x 450 px, JPG, PNG, SVG or GIF, up to 2 MB).</p>

{{-- Add --}}
<form method="POST" action="{{ route('admin.home-banners.store', $section) }}" enctype="multipart/form-data" class="card shadow-sm border-0 rounded-3 mb-4">
    @csrf
    <div class="card-body">
        <h5 class="fw-bold mb-3">Add a banner</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold small mb-1">Image *</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif,.svg,image/jpeg,image/png,image/gif,image/svg+xml" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small mb-1">Title (optional)</label>
                <input type="text" name="title" class="form-control" maxlength="120" value="{{ old('title') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small mb-1">Small text (optional)</label>
                <input type="text" name="subtitle" class="form-control" maxlength="200" value="{{ old('subtitle') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small mb-1">When tapped</label>
                <select name="link_type" class="form-select js-link-type">
                    @foreach(\App\Models\HomeBanner::LINKS as $k => $label)
                        <option value="{{ $k }}" {{ old('link_type', $defaultLink) === $k ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5 js-link-url" style="display:none;">
                <label class="form-label fw-semibold small mb-1">Web link</label>
                <input type="url" name="link_value" class="form-control" maxlength="500" placeholder="https://..." value="{{ old('link_value') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small mb-1">Order</label>
                <input type="number" name="sort" min="0" max="999" class="form-control" value="{{ old('sort') }}">
            </div>
        </div>
        <button class="btn btn-success fw-bold mt-3"><i class="fa-solid fa-plus me-1"></i> Add banner</button>
    </div>
</form>

{{-- Existing --}}
<div class="row g-3">
    @forelse($banners as $b)
        <div class="col-lg-6">
            <form method="POST" action="{{ route('admin.home-banners.update', $b->id) }}" enctype="multipart/form-data" class="card shadow-sm border-0 rounded-3 h-100">
                @csrf
                <img src="{{ asset($b->image) }}" alt="" style="width:100%;height:170px;object-fit:cover;border-radius:12px 12px 0 0;">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-12"><input type="text" name="title" class="form-control form-control-sm" maxlength="120" placeholder="Title" value="{{ $b->title }}"></div>
                        <div class="col-12"><input type="text" name="subtitle" class="form-control form-control-sm" maxlength="200" placeholder="Small text" value="{{ $b->subtitle }}"></div>
                        <div class="col-md-6">
                            <select name="link_type" class="form-select form-select-sm js-link-type">
                                @foreach(\App\Models\HomeBanner::LINKS as $k => $label)
                                    <option value="{{ $k }}" {{ $b->link_type === $k ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 js-link-url" style="display:none;"><input type="url" name="link_value" class="form-control form-control-sm" placeholder="https://..." value="{{ $b->link_value }}"></div>
                        <div class="col-md-6"><input type="file" name="image" accept=".jpg,.jpeg,.png,.gif,.svg,image/jpeg,image/png,image/gif,image/svg+xml" class="form-control form-control-sm" title="Replace image"></div>
                        <div class="col-md-3"><input type="number" name="sort" min="0" max="999" class="form-control form-control-sm" placeholder="Order" value="{{ $b->sort }}"></div>
                        <div class="col-md-3 d-flex align-items-center">
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="ba{{ $b->id }}" {{ $b->is_active ? 'checked' : '' }}><label class="form-check-label small" for="ba{{ $b->id }}">Show</label></div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-success btn-sm fw-bold">Save</button>
                        <button type="submit" form="delb{{ $b->id }}" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this banner?')"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            </form>
            <form id="delb{{ $b->id }}" method="POST" action="{{ route('admin.home-banners.destroy', $b->id) }}" class="d-none">@csrf @method('DELETE')</form>
        </div>
    @empty
        <div class="col-12"><div class="text-center text-muted py-5 bg-white rounded-3 border"><i class="fa-solid fa-images fa-2x mb-2 opacity-50"></i><div>No banners yet. Add the first one above.</div></div></div>
    @endforelse
</div>

<script>
    // the web link box only matters when "Open a web link" is chosen
    function syncLinkBoxes() {
        document.querySelectorAll('.js-link-type').forEach(function (sel) {
            var box = sel.closest('.row').querySelector('.js-link-url');
            if (box) box.style.display = sel.value === 'url' ? '' : 'none';
        });
    }
    document.addEventListener('change', function (e) { if (e.target.classList.contains('js-link-type')) syncLinkBoxes(); });
    syncLinkBoxes();
</script>
@endsection
