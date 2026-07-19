@extends('layouts.app')

@section('title', $page->title . ' - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
@endpush

@section('content')

<div class="dashboard-container">
    <div class="row g-4">
        
        @include('partials.customer.sidebar')

        <!-- Main Content -->
        <div class="col-xl-9 col-lg-9">
            <!-- Hero Banner -->
            @include('partials.customer.hero-banner', [
                'title' => $page->title,
                'subtitle' => 'Information regarding ' . strtolower($page->title) . ' and policies.'
            ])

            <div class="middle-card">
                <div class="page-content-wrapper">
                    {!! $page->content !!}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
