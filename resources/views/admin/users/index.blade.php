@extends('layouts.admin')

@section('title', 'Users Management - Scrap Daddy')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Users Management</h1>
</div>

<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body">
        <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by Name, Phone, or UUID..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary px-4">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.users.index') }}" class="btn btn-light px-3">Clear</a>
            @endif
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Customer Details</th>
                        <th>Joined Date</th>
                        <th class="text-center">Total Orders</th>
                        <th class="text-center">Scrap Vehicles</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">{{ $user->full_name }}</span>
                                <span class="text-muted small">{{ $user->phone_number }}</span>
                            </div>
                        </td>
                        <td>
                            <div>{{ $user->created_at->format('d M, Y') }}</div>
                            <small class="text-muted">{{ $user->created_at->format('h:i A') }}</small>
                        </td>
                        <td class="text-center">
                            @if($user->orders_count > 0)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                    {{ $user->orders_count }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->scrap_vehicles_count > 0)
                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">
                                    {{ $user->scrap_vehicles_count }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('admin.users.show', $user->uuid) }}" class="btn btn-sm btn-outline-primary px-3">
                                <i class="fa-solid fa-eye me-1"></i> View Profile
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-muted small fw-medium">
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
            </div>
            <div class="m-0">
                {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
