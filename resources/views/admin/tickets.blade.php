@extends('layouts.admin')

@section('title', 'Support Tickets')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0">Support Tickets @if($openCount)<span class="badge bg-danger ms-2">{{ $openCount }} open</span>@endif</h1>
    <form method="GET" class="d-flex gap-2">
        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All</option>
            @foreach(['open' => 'Open', 'in_progress' => 'In progress', 'closed' => 'Closed'] as $k => $label)
                <option value="{{ $k }}" @selected(request('status') === $k)>{{ $label }}</option>
            @endforeach
        </select>
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@forelse($tickets as $t)
    <div class="card shadow-sm border-0 rounded-3 mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-1">#{{ $t->id }} &middot; {{ $t->subject }}</h6>
                    <div class="text-muted small">
                        {{ $t->user->full_name ?? 'Customer' }} &middot; {{ $t->user->phone_number ?? '-' }} &middot; {{ $t->created_at->format('d M Y, h:i A') }}
                    </div>
                </div>
                <span class="badge align-self-start {{ $t->status === 'open' ? 'bg-danger' : ($t->status === 'in_progress' ? 'bg-warning text-dark' : 'bg-success') }}">{{ str_replace('_', ' ', $t->status) }}</span>
            </div>
            <p class="mt-3 mb-3" style="white-space: pre-line;">{{ $t->message }}</p>
            <form method="POST" action="{{ route('admin.tickets.update', $t->id) }}" class="row g-2">
                @csrf
                <div class="col-md-7">
                    <textarea name="admin_reply" rows="2" class="form-control" placeholder="Reply to the customer (shown in their app and website)">{{ $t->admin_reply }}</textarea>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        @foreach(['open' => 'Open', 'in_progress' => 'In progress', 'closed' => 'Closed'] as $k => $label)
                            <option value="{{ $k }}" @selected($t->status === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid"><button class="btn btn-success">Save</button></div>
            </form>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-5 bg-white rounded-3 border">No tickets yet.</div>
@endforelse

{{ $tickets->links() }}
@endsection
