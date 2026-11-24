@extends('layouts.admin')

@php $editing = $article->exists; @endphp
@section('title', $editing ? 'Edit article' : 'New article')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0">{{ $editing ? 'Edit article' : 'New article' }}</h1>
    <a href="{{ route('admin.scrap-articles.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> All articles</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<form method="POST" enctype="multipart/form-data"
      action="{{ $editing ? route('admin.scrap-articles.update', $article->id) : route('admin.scrap-articles.store') }}"
      class="card shadow-sm border-0 rounded-3 mb-4">
    @csrf
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold small mb-1">Title *</label>
                <input type="text" name="title" class="form-control" maxlength="160" value="{{ old('title', $article->title) }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small mb-1">Order</label>
                <input type="number" name="sort" min="0" max="999" class="form-control" value="{{ old('sort', $article->sort) }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="artActive" {{ old('is_active', $article->is_active) ? 'checked' : '' }}><label class="form-check-label" for="artActive">Show</label></div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small mb-1">Short description (shown on the card)</label>
                <input type="text" name="summary" class="form-control" maxlength="300" value="{{ old('summary', $article->summary) }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small mb-1">Full article</label>
                <textarea name="content" rows="10" class="form-control" maxlength="20000" placeholder="Write the article. Leave a blank line between paragraphs.">{{ old('content', $article->content) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small mb-1">Cover image {{ $editing ? '(leave empty to keep)' : '*' }}</label>
                <input type="file" name="cover_image" accept=".jpg,.jpeg,.png,.gif,.svg,image/jpeg,image/png,image/gif,image/svg+xml" class="form-control" {{ $editing ? '' : 'required' }}>
                @if($article->cover_image)<img src="{{ asset($article->cover_image) }}" alt="" class="mt-2 rounded-3" style="height:90px;object-fit:cover;">@endif
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small mb-1">Add gallery banners (several allowed, shown as a slider)</label>
                <input type="file" name="images[]" accept=".jpg,.jpeg,.png,.gif,.svg,image/jpeg,image/png,image/gif,image/svg+xml" multiple class="form-control">
            </div>
        </div>

        @if($editing && $article->images->count())
            <h6 class="fw-bold mt-4">Gallery</h6>
            <div class="d-flex flex-wrap gap-3">
                @foreach($article->images as $img)
                    <label class="text-center small" style="width:120px;">
                        <img src="{{ asset($img->image) }}" alt="" class="rounded-3 d-block mb-1" style="width:120px;height:80px;object-fit:cover;">
                        <input type="checkbox" name="remove_images[]" value="{{ $img->id }}" class="form-check-input"> Remove
                    </label>
                @endforeach
            </div>
        @endif

        <button class="btn btn-success fw-bold mt-4"><i class="fa-solid fa-floppy-disk me-1"></i> {{ $editing ? 'Save article' : 'Create article' }}</button>
    </div>
</form>

@if($editing)
<form method="POST" action="{{ route('admin.scrap-articles.destroy', $article->id) }}" onsubmit="return confirm('Delete this article and its images?')">
    @csrf @method('DELETE')
    <button class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash me-1"></i> Delete article</button>
</form>
@endif
@endsection
