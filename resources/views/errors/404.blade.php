@extends('layouts.app')

@section('title', 'Page not found - Scrap Daddy')

@push('meta')
    <meta name="robots" content="noindex">
@endpush

@push('styles')
    @include('site._styles')
@endpush

@section('content')
<div class="st-wrap" style="max-width:720px;">
    <div class="st-card text-center" style="padding:36px 22px;">
        <div style="width:84px;height:84px;border-radius:50%;background:#eaf6ea;color:#3D8B4D;display:inline-flex;align-items:center;justify-content:center;font-size:2rem;"><i class="fa-solid fa-recycle"></i></div>
        <h1 class="fw-bold mt-3" style="font-size:1.6rem;color:#1d2939;">We could not find that page</h1>
        <p>The page may have moved or no longer exists. Here is where you can go instead:</p>
        <div class="d-flex flex-wrap gap-2 justify-content-center mt-3">
            <a class="btn fw-bold text-white" style="background:#3D8B4D;border-radius:999px;padding:10px 22px;" href="{{ route('customer.orders.create') }}">Book a pickup</a>
            <a class="btn fw-bold" style="border:1.5px solid #3D8B4D;color:#3D8B4D;border-radius:999px;padding:10px 22px;" href="{{ route('site.services') }}">Our services</a>
            <a class="btn fw-bold" style="border:1.5px solid #3D8B4D;color:#3D8B4D;border-radius:999px;padding:10px 22px;" href="{{ url('/') }}">Home</a>
            <a class="btn fw-bold" style="border:1.5px solid #3D8B4D;color:#3D8B4D;border-radius:999px;padding:10px 22px;" href="{{ route('blog.index') }}">Blog</a>
        </div>
    </div>
</div>
@endsection
