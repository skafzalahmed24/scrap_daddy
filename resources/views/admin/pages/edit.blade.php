@extends('layouts.admin')

@section('title', 'Edit Page: ' . $page->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Edit Page: <span class="text-primary">{{ $page->title }}</span></h1>
    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i> Back to Pages
    </a>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">
        <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="form-label fw-bold">Page Title</label>
                <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $page->title) }}" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold">Page Content (HTML allowed)</label>
                <textarea name="content" class="form-control" rows="15" required style="font-family: monospace;">{{ old('content', $page->content) }}</textarea>
            </div>
            
            <div class="text-end">
                <a href="{{ route('admin.pages.index') }}" class="btn btn-light me-2">Cancel</a>
                <button type="submit" class="btn btn-success px-4">
                    <i class="fa-solid fa-save me-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
