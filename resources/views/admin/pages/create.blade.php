@extends('layouts.admin')

@section('title', 'Create Static Page')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Create Static Page</h1>
    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary px-4">
        <i class="fa-solid fa-arrow-left me-2"></i> Back
    </a>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">
        <form action="{{ route('admin.pages.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="slug" class="form-label fw-bold">Slug / URL Path <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" placeholder="e.g., help-and-support" required>
                <div class="form-text">This will be the URL for the page: /page/your-slug</div>
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="title" class="form-label fw-bold">Page Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="e.g., Help & Support" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="content" class="form-label fw-bold">Page Content <span class="text-danger">*</span></label>
                <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10" placeholder="Enter HTML or plain text content here..." required>{{ old('content') }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success px-4 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-save me-2"></i> Create Page
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
