@extends('layouts.admin')

@section('title', 'Home Content')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0">Home Content</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<p class="text-muted">These texts appear on the Home screen of the website and the mobile app, in this order. Changes show up straight away.</p>

@php
    $labels = [
        'trending' => ['Trending scrap', 'Small cards of popular items with a quantity + button. Pick the items below.'],
        'bulk_enquiry' => ['Bulk enquiry', 'Banner with a button that opens the enquiry form. Manage the form under Bulk Enquiries.'],
        'sell_bulk' => ['Sell bulk scrap', 'Title, description and hashtags above the bulk scrap banners.'],
        'scrap_vehicle' => ['Scrap your vehicle', 'Title, description and hashtags above the vehicle banners.'],
        'offers' => ['Offers', 'Title above the offer banners.'],
        'know_scrap' => ['Know your scrap', 'Title and description above the articles.'],
        'how_it_works' => ['How it works', 'Heading above the steps. Edit the steps themselves with the button.'],
    ];
@endphp

<div class="row g-3 mb-4">
    @foreach($sections as $section)
        <div class="col-lg-6">
            <form method="POST" action="{{ route('admin.home-content.section', $section->key) }}" class="card shadow-sm border-0 rounded-3 h-100">
                @csrf
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="fw-bold mb-0">{{ $labels[$section->key][0] ?? $section->key }}</h5>
                            <small class="text-muted">{{ $labels[$section->key][1] ?? '' }}</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="act_{{ $section->key }}" {{ $section->is_active ? 'checked' : '' }}>
                            <label class="form-check-label small" for="act_{{ $section->key }}">Show</label>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small mb-1">Title</label>
                        <input type="text" name="title" class="form-control" maxlength="120" value="{{ $section->title }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small mb-1">Description</label>
                        <textarea name="description" rows="2" class="form-control" maxlength="1000">{{ $section->description }}</textarea>
                    </div>
                    @if(in_array($section->key, ['trending', 'bulk_enquiry', 'sell_bulk', 'scrap_vehicle', 'know_scrap']))
                    <div class="mb-2">
                        <label class="form-label fw-semibold small mb-1">Button text</label>
                        <input type="text" name="button_text" class="form-control" maxlength="60" value="{{ $section->button_text }}">
                    </div>
                    @endif
                    @if(in_array($section->key, ['sell_bulk', 'scrap_vehicle']))
                    <div class="mb-2">
                        <label class="form-label fw-semibold small mb-1">Hashtags</label>
                        <input type="text" name="tags" class="form-control" value="{{ implode(', ', $section->tags ?? []) }}" placeholder="Best rates, Compliance, Transparency">
                        <div class="form-text">Separate with commas.</div>
                    </div>
                    @endif
                    <button class="btn btn-success fw-bold btn-sm mt-1"><i class="fa-solid fa-floppy-disk me-1"></i> Save</button>
                    @if(in_array($section->key, ['sell_bulk', 'scrap_vehicle', 'offers']))
                        <a href="{{ route('admin.home-banners.index', $section->key) }}" class="btn btn-outline-success fw-bold btn-sm mt-1 ms-1"><i class="fa-solid fa-images me-1"></i> Manage banners</a>
                    @elseif($section->key === 'how_it_works')
                        <a href="{{ route('admin.how-steps.index') }}" class="btn btn-outline-success fw-bold btn-sm mt-1 ms-1"><i class="fa-solid fa-list-ol me-1"></i> Manage steps</a>
                    @elseif($section->key === 'know_scrap')
                        <a href="{{ route('admin.scrap-articles.index') }}" class="btn btn-outline-success fw-bold btn-sm mt-1 ms-1"><i class="fa-solid fa-book-open me-1"></i> Manage articles</a>
                    @endif
                </div>
            </form>
        </div>
    @endforeach
</div>

<form method="POST" action="{{ route('admin.home-content.trending') }}" class="card shadow-sm border-0 rounded-3 mb-4">
    @csrf
    <div class="card-body">
        <h5 class="fw-bold mb-1">Trending items</h5>
        <p class="text-muted small mb-3">Tick the items to show in the Trending section and give each a position (1 = first). Keep it to about 6 to 12 items.</p>
        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
            <table class="table align-middle mb-0">
                <thead><tr><th style="width:60px;">Show</th><th>Item</th><th>Category</th><th style="width:110px;">Position</th></tr></thead>
                <tbody>
                @forelse($subcategories as $sub)
                    <tr>
                        <td><input type="checkbox" class="form-check-input" name="trending[]" value="{{ $sub->uuid }}" {{ $sub->is_trending ? 'checked' : '' }}></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($sub->image)<img src="{{ asset($sub->image) }}" alt="" style="width:36px;height:36px;object-fit:contain;border-radius:8px;border:1px solid #eee;">@endif
                                <span class="fw-semibold">{{ $sub->name }}</span>
                            </div>
                        </td>
                        <td class="text-muted">{{ $sub->category->title ?? '-' }}</td>
                        <td><input type="number" min="0" max="999" class="form-control form-control-sm" name="order[{{ $sub->uuid }}]" value="{{ $sub->trending_order ?: '' }}"></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted text-center py-4">No active sub-categories yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <button class="btn btn-success fw-bold mt-3"><i class="fa-solid fa-floppy-disk me-1"></i> Save trending items</button>
    </div>
</form>
@endsection
