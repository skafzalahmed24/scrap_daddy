@extends('layouts.admin')

@section('title', 'Service Requests')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title mb-1">Service Requests</h1>
        <p class="page-sub">Customers who asked for pickup in a PIN code we don't cover yet.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel">
    <div class="panel-toolbar">
        <div class="filter-tabs">
            @foreach(['' => ['All', $counts['all']], 'new' => ['New', $counts['new']], 'contacted' => ['Contacted', $counts['contacted']], 'closed' => ['Closed', $counts['closed']]] as $key => [$label, $count])
                <a href="{{ route('admin.service-requests.index', array_filter(['status' => $key, 'search' => request('search')])) }}"
                   class="filter-tab {{ ($status ?? '') === $key ? 'active' : '' }}">{{ $label }} <span>{{ $count }}</span></a>
            @endforeach
        </div>
        <form method="GET" class="toolbar-search">
            @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, phone, PIN code">
        </form>
    </div>

    <div class="table-responsive">
        <table class="table sd-table align-middle mb-0">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>PIN code</th>
                    <th>Message</th>
                    <th>Received</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $r)
                <tr>
                    <td data-label="Customer">
                        <div class="fw-semibold">{{ $r->name }}</div>
                        <div class="small"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $r->phone) }}" class="text-decoration-none"><i class="fa-solid fa-phone me-1 text-success"></i>{{ $r->phone }}</a></div>
                        @if($r->email)<div class="small text-muted"><i class="fa-regular fa-envelope me-1"></i>{{ $r->email }}</div>@endif
                    </td>
                    <td data-label="PIN code">
                        <span class="pin-chip">{{ $r->pin_code }}</span>
                        @if($r->area)<div class="small text-muted mt-1">{{ $r->area }}</div>@endif
                    </td>
                    <td data-label="Message" class="text-wrap-cell">{{ $r->message ?: '—' }}</td>
                    <td data-label="Received">
                        <div>{{ $r->created_at->format('d M Y') }}</div>
                        <div class="small text-muted">{{ $r->created_at->format('h:i A') }}</div>
                    </td>
                    <td data-label="Status"><span class="status-pill status-{{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
                    <td data-label="Actions" class="text-end">
                        <div class="row-actions">
                            @if($r->status !== 'contacted')
                            <form method="POST" action="{{ route('admin.service-requests.status', $r->id) }}">@csrf<input type="hidden" name="status" value="contacted">
                                <button class="btn btn-sm btn-outline-primary" title="Mark as contacted"><i class="fa-solid fa-phone-volume"></i><span>Contacted</span></button></form>
                            @endif
                            @if($r->status !== 'closed')
                            <form method="POST" action="{{ route('admin.service-requests.status', $r->id) }}">@csrf<input type="hidden" name="status" value="closed">
                                <button class="btn btn-sm btn-outline-success" title="Close"><i class="fa-solid fa-check"></i><span>Close</span></button></form>
                            @endif
                            <form method="POST" action="{{ route('admin.service-requests.destroy', $r->id) }}" onsubmit="return confirm('Delete this request?');">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-regular fa-trash-can"></i></button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row"><td colspan="6" class="text-center text-muted py-5"><i class="fa-regular fa-inbox fa-2x d-block mb-2 opacity-50"></i>No service requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div class="panel-footer">{{ $requests->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
