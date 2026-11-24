@extends('layouts.admin')

@section('title', 'Scrap Articles')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0">Scrap Articles</h1>
    <a href="{{ route('admin.scrap-articles.create') }}" class="btn btn-success fw-bold"><i class="fa-solid fa-plus me-1"></i> New article</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<p class="text-muted">These appear in the <b>Know your scrap</b> section of the Home screen (website and app). Tapping a card opens the full article with its banner gallery. The section title and description are under Home Content.</p>

<div class="row g-3">
    @forelse($articles as $a)
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                @if($a->cover_image)<img src="{{ asset($a->cover_image) }}" alt="" style="width:100%;height:160px;object-fit:cover;border-radius:12px 12px 0 0;">@endif
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <h6 class="fw-bold mb-1">{{ $a->title }}</h6>
                        <span class="badge {{ $a->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $a->is_active ? 'Shown' : 'Hidden' }}</span>
                    </div>
                    <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($a->summary, 90) }}</p>
                    <div class="small text-muted mb-3"><i class="fa-regular fa-images me-1"></i>{{ $a->images_count }} gallery image(s) &middot; order {{ $a->sort }}</div>
                    <a href="{{ route('admin.scrap-articles.edit', $a->id) }}" class="btn btn-outline-success btn-sm fw-bold"><i class="fa-solid fa-pen me-1"></i> Edit</a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="text-center text-muted py-5 bg-white rounded-3 border"><i class="fa-solid fa-book-open fa-2x mb-2 opacity-50"></i><div>No articles yet.</div></div></div>
    @endforelse
</div>
@endsection
