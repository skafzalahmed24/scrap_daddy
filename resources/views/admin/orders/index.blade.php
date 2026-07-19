@extends('layouts.admin')

@section('title', 'Manage Orders')

@section('content')
<h1 class="page-title mb-4">Manage Orders</h1>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>User & Category</th>
                        <th>Pickup Details</th>
                        <th>Images & Notes</th>
                        <th>Status</th>
                        <th class="pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="ps-4 fw-semibold">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $order->user->full_name ?? 'N/A' }}</div>
                            <div class="text-muted small">{{ $order->user->phone_number ?? '' }}</div>
                            <span class="badge bg-secondary mt-1">{{ $order->subcategory?->name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <div style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; white-space: normal; max-width: 280px;" title="{{ $order->pickup_location }}">
                                <i class="fa-solid fa-location-dot text-danger"></i> {{ $order->pickup_location }}
                            </div>
                            <div class="mt-1"><i class="fa-regular fa-calendar text-primary"></i> {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') : '--' }}</div>
                            <div class="small"><i class="fa-regular fa-clock text-warning"></i> {{ $order->pickup_time ?? '--' }}</div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1 mb-1">
                                @if($order->images)
                                    @foreach($order->images as $img)
                                        <a href="/{{ $img }}" target="_blank"><img src="/{{ $img }}" style="width:40px; height:40px; object-fit:cover; border-radius:4px;"></a>
                                    @endforeach
                                @else
                                    <small class="text-muted">No Images</small>
                                @endif
                            </div>
                            @if($order->notes)
                                <div class="small text-muted border-top pt-1 mt-1"><em>"{{ $order->notes }}"</em></div>
                            @endif
                        </td>
                        <td>
                            @if($order->status == 'pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">Pending</span>
                            @elseif($order->status == 'accepted')
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">Accepted</span>
                            @elseif($order->status == 'completed')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">Completed</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Cancelled</span>
                            @endif
                            <div class="mt-1 small">
                                @if($order->payment_status == 'completed')
                                    <span class="text-success"><i class="fa-solid fa-check-circle"></i> Paid</span>
                                @else
                                    <span class="text-muted">Payment Pending</span>
                                @endif
                            </div>
                        </td>
                        <td class="pe-4">
                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-flex flex-column align-items-start gap-2">
                                @csrf
                                <div class="d-flex gap-2 w-100">
                                    <select name="status" class="form-select form-select-sm status-select" style="width: 140px;">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="accepted" {{ $order->status == 'accepted' ? 'selected' : '' }}>Accept</option>
                                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Mark Complete</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancel</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                </div>
                                <div class="amount-input-group w-100" style="{{ $order->status == 'completed' ? '' : 'display: none;' }}">
                                    <input type="number" step="0.01" min="0" max="500000" name="total_amount" class="form-control form-control-sm" placeholder="Payout amount (₹)" value="{{ $order->total_amount }}">
                                </div>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No orders found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelects = document.querySelectorAll('.status-select');
        statusSelects.forEach(select => {
            select.addEventListener('change', function() {
                const amountInputGroup = this.closest('form').querySelector('.amount-input-group');
                if (this.value === 'completed') {
                    amountInputGroup.style.display = 'block';
                    amountInputGroup.querySelector('input').setAttribute('required', 'required');
                } else {
                    amountInputGroup.style.display = 'none';
                    amountInputGroup.querySelector('input').removeAttribute('required');
                }
            });
        });
    });
</script>
@endpush
