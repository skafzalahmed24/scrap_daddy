@extends('layouts.admin')

@section('title', 'Payments Management - Scrap Daddy')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Payments Management</h1>
    <div class="text-muted">
        <i class="fa-regular fa-clock me-1"></i> Last updated: {{ now()->format('d M, Y h:i A') }}
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Customer List -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3" style="border-radius: 12px 12px 0 0;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-users me-2 text-primary"></i>Customer Analytics</h5>
                </div>
                <form action="{{ route('admin.payments.index') }}" method="GET">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search_customer" class="form-control border-start-0 bg-light" placeholder="Search customer name or phone..." value="{{ $customerSearch }}">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </form>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4">Customer</th>
                                <th class="text-center">Pending</th>
                                <th class="text-end">Total Net Paid</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                            <tr class="{{ $selectedUserId == $customer->uuid ? 'table-primary' : '' }}">
                                <td class="ps-4">
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium text-dark">{{ $customer->full_name }}</span>
                                        <small class="text-muted">{{ $customer->phone_number }}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($customer->pending_orders_count > 0)
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">
                                            {{ $customer->pending_orders_count }} Orders
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success">
                                    ₹{{ number_format($customer->total_net_paid, 2) }}
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.payments.index', ['user_uuid' => $customer->uuid]) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    No customers found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-white border-top py-3" style="border-radius: 0 0 12px 12px;">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted small fw-medium">
                        Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} entries
                    </div>
                    <div class="m-0">
                        {{ $customers->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Details Pane -->
    <div class="col-lg-7">
        @if($selectedUser)
            <!-- Revenue Highlight for Customer -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #6366f1, #4f46e5); color: white;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 text-uppercase fw-bold mb-1" style="font-size: 0.85rem; letter-spacing: 1px;">Selected Customer</h6>
                        <h4 class="fw-bold mb-0 text-white">{{ $selectedUser->full_name }}</h4>
                    </div>
                    <div class="text-end">
                        <h6 class="text-white-50 text-uppercase fw-bold mb-1" style="font-size: 0.85rem; letter-spacing: 1px;">Total Net Revenue</h6>
                        <h3 class="fw-bold mb-0 text-white">₹{{ number_format($customers->where('uuid', $selectedUser->uuid)->first()->total_net_paid ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>

            <!-- Pending Orders Table -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0 fw-bold text-warning"><i class="fa-regular fa-clock me-2"></i>Pending Payments</h5>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill">{{ $pendingPayments->count() }} Orders</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted sticky-top">
                                <tr>
                                    <th class="ps-4">Order ID</th>
                                    <th>Category</th>
                                    <th class="text-end">Est. Amount</th>
                                    <th class="text-end pe-4">Discount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingPayments as $payment)
                                <tr>
                                    <td class="ps-4 fw-semibold text-brand">
                                        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">#{{ $payment->id }}</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                            {{ $payment->category->title ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold text-dark">
                                        {{ $payment->total_amount > 0 ? '₹'.number_format($payment->total_amount, 2) : 'TBD' }}
                                    </td>
                                    <td class="text-end pe-4 text-danger">
                                        @if($payment->discount_applied > 0)
                                            -₹{{ number_format($payment->discount_applied, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-check-circle fs-3 mb-2 text-success d-block"></i>
                                        All caught up! No pending payments.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Completed Payments Table -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0 fw-bold text-success"><i class="fa-solid fa-check-circle me-2"></i>Completed Payments</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted sticky-top">
                                <tr>
                                    <th class="ps-4">Date</th>
                                    <th>Order ID</th>
                                    <th class="text-end">Base Amount</th>
                                    <th class="text-end">Rewards</th>
                                    <th class="text-end pe-4">Net Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedPayments as $payment)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex flex-column">
                                            <span class="text-dark">{{ $payment->updated_at->format('d M, Y') }}</span>
                                            <small class="text-muted">{{ $payment->updated_at->format('h:i A') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none fw-semibold">#{{ $payment->id }}</a>
                                    </td>
                                    <td class="text-end fw-medium text-dark">
                                        ₹{{ number_format($payment->total_amount, 2) }}
                                    </td>
                                    <td class="text-end">
                                        @if($payment->discount_applied > 0)
                                            <span class="text-danger">-₹{{ number_format($payment->discount_applied, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-success">
                                        +₹{{ number_format($payment->total_amount - $payment->discount_applied, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No completed payments found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        @else
            <!-- Global Analytics / Empty State -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(135deg, #10b981, #059669); color: white;">
                        <div class="card-body p-4 text-center">
                            <h6 class="text-white-50 text-uppercase fw-bold mb-2">Total Global Revenue</h6>
                            <h2 class="fw-bold mb-0">₹{{ number_format($totalRevenue, 2) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                        <div class="card-body p-4 text-center">
                            <h6 class="text-white-50 text-uppercase fw-bold mb-2">Total Pending ({{ $globalPendingOrdersCount }} Orders)</h6>
                            <h2 class="fw-bold mb-0">₹{{ number_format($globalPendingAmount, 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm d-flex align-items-center justify-content-center bg-light" style="border-radius: 12px; min-height: 400px;">
                <div class="text-center">
                    <div class="bg-white rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-hand-pointer fs-1 text-primary"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Select a Customer</h4>
                    <p class="text-muted px-4">Search or click "View" on any customer from the list on the left to see their detailed payment history, pending orders, and total net revenue.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
