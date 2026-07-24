@extends('layouts.app')

@section('title', $page->title . ' - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
@endpush

@section('content')
<style>
    @media (max-width: 991px) {
        header, footer {
            display: none !important;
        }
        .dashboard-container {
            padding-top: 15px !important;
        }
    }
</style>

<div class="dashboard-container">
    <div class="row g-4">
        
        @include('partials.customer.sidebar')

        <!-- Main Content -->
        <div class="col-xl-9 col-lg-9">
            <!-- Hero Banner -->
            <div class="d-none d-lg-block">
                @include('partials.customer.hero-banner', [
                    'title' => $page->title,
                    'subtitle' => 'Information regarding ' . strtolower($page->title) . ' and policies.'
                ])
            </div>

            <!-- Mobile Header with Back Button -->
            <div class="d-flex align-items-center mb-4 d-lg-none bg-white p-3 rounded-4 shadow-sm border border-light">
                <a href="{{ route('customer.profile') }}" class="text-dark text-decoration-none d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h5 class="mb-0 fw-bold ms-3" style="color: var(--primary-blue, #0d2b4d);">{{ $page->title }}</h5>
            </div>

            <div class="middle-card">
                <div class="page-content-wrapper">
                    {!! $page->content !!}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
