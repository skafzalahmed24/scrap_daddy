@extends('layouts.admin')

@section('title', 'Manage Feedbacks')

@section('content')
<h1 class="page-title mb-4">Manage Feedbacks</h1>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>User</th>
                        <th>Rating</th>
                        <th style="width: 40%;">Comment</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbacks as $feedback)
                    <tr>
                        <td class="ps-4 fw-semibold">#{{ $feedback->id }}</td>
                        <td>{{ $feedback->user->full_name ?? 'N/A' }}</td>
                        <td>
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $feedback->star_rating)
                                        <i class="fa-solid fa-star"></i>
                                    @else
                                        <i class="fa-regular fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </td>
                        <td>
                            <p class="mb-0 text-wrap">{{ $feedback->comment }}</p>
                        </td>
                        <td>
                            @if($feedback->is_approved)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">Approved</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">Hidden</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <form action="{{ route('admin.feedbacks.toggle', $feedback->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $feedback->is_approved ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                    {{ $feedback->is_approved ? 'Hide' : 'Approve' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No feedbacks found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
