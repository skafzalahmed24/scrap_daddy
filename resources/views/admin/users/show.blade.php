@extends('layouts.admin')

@section('title', 'User Profile - Scrap Daddy')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-circle" style="width: 40px; height: 40px; padding: 0; line-height: 40px;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="page-title mb-0">User Profile</h1>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- User Info Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white;">
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                <div class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3 fw-bold fs-3 shadow-sm" style="width: 80px; height: 80px;">
                    {{ strtoupper(substr($user->full_name, 0, 1)) }}
                </div>
                <h3 class="fw-bold mb-1">{{ $user->full_name }}</h3>
                <p class="text-white-50 mb-3">{{ $user->phone_number }}</p>
                <div class="d-flex justify-content-center text-center bg-white bg-opacity-10 p-3 rounded mt-3">
                    <div>
                        <small class="text-white-50 d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Joined</small>
                        <span class="fw-medium">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- User Stats -->
    <div class="col-lg-8">
        <div class="row g-4 h-100">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-body p-4 d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-cart-shopping fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.85rem; letter-spacing: 1px;">Total Orders</h6>
                            <h2 class="fw-bold mb-0">{{ $orders->total() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-body p-4 d-flex align-items-center gap-3">
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-truck-monster fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.85rem; letter-spacing: 1px;">Scrap Vehicles</h6>
                            <h2 class="fw-bold mb-0">{{ $vehicles->total() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Tables Tab Interface -->
<ul class="nav nav-tabs mb-4 border-bottom-0 gap-2" id="userProfileTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active rounded-top px-4 py-2 bg-white text-dark shadow-sm fw-medium border-0" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="true">
            <i class="fa-solid fa-cart-shopping me-2"></i>Order History
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-top px-4 py-2 bg-light text-muted fw-medium border-0" id="vehicles-tab" data-bs-toggle="tab" data-bs-target="#vehicles" type="button" role="tab" aria-controls="vehicles" aria-selected="false">
            <i class="fa-solid fa-truck-monster me-2"></i>Scrap Vehicles
        </button>
    </li>
</ul>

<div class="tab-content" id="userProfileTabsContent">
    <!-- Orders Tab -->
    <div class="tab-pane fade show active" id="orders" role="tabpanel" aria-labelledby="orders-tab">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-body bg-light border-bottom" style="border-radius: 12px 12px 0 0;">
                <form action="{{ route('admin.users.show', $user->uuid) }}" method="GET" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="order_search" class="form-control border-start-0" placeholder="Search by Order ID..." value="{{ request('order_search') }}">
                    </div>
                    <button type="submit" class="btn btn-primary px-4">Search</button>
                    @if(request('order_search'))
                        <a href="{{ route('admin.users.show', $user->uuid) }}" class="btn btn-light px-3">Clear</a>
                    @endif
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Order ID</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Base Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#{{ $order->id }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                        {{ $order->category->title ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->status == 'completed')
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">Completed</span>
                                    @elseif($order->status == 'cancelled')
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">Cancelled</span>
                                    @elseif($order->status == 'accepted')
                                        <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">Accepted</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 fw-medium text-dark">
                                    {{ $order->total_amount > 0 ? '₹'.number_format($order->total_amount, 2) : 'TBD' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No orders found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted small fw-medium">
                        Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} entries
                    </div>
                    <div class="m-0">
                        {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scrap Vehicles Tab -->
    <div class="tab-pane fade" id="vehicles" role="tabpanel" aria-labelledby="vehicles-tab">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-body bg-light border-bottom" style="border-radius: 12px 12px 0 0;">
                <form action="{{ route('admin.users.show', $user->uuid) }}" method="GET" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="vehicle_search" class="form-control border-start-0" placeholder="Search by Vehicle Number or Type..." value="{{ request('vehicle_search') }}">
                    </div>
                    <button type="submit" class="btn btn-primary px-4">Search</button>
                    @if(request('vehicle_search'))
                        <a href="{{ route('admin.users.show', $user->uuid) }}" class="btn btn-light px-3">Clear</a>
                    @endif
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Vehicle Number</th>
                                <th>Type / Brand</th>
                                <th>Status</th>
                                <th class="pe-4">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicles as $vehicle)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $vehicle->vehicle_number }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle align-self-start mb-1">
                                            {{ $vehicle->vehicle_type }}
                                        </span>
                                        @if($vehicle->vehicle_brand || $vehicle->vehicle_model)
                                            <small class="text-muted">{{ $vehicle->vehicle_brand }} {{ $vehicle->vehicle_model }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($vehicle->status == 'completed')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Completed</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">Pending</span>
                                    @endif
                                </td>
                                <td class="pe-4">
                                    <div>{{ $vehicle->created_at->format('d M, Y') }}</div>
                                    <small class="text-muted">{{ $vehicle->created_at->format('h:i A') }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No scrap vehicles found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted small fw-medium">
                        Showing {{ $vehicles->firstItem() ?? 0 }} to {{ $vehicles->lastItem() ?? 0 }} of {{ $vehicles->total() }} entries
                    </div>
                    <div class="m-0">
                        {{ $vehicles->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Simple script to toggle active classes on tabs for styling
    document.querySelectorAll('.nav-link').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            document.querySelectorAll('.nav-link').forEach(t => {
                t.classList.remove('bg-white', 'text-dark', 'shadow-sm');
                t.classList.add('bg-light', 'text-muted');
            });
            event.target.classList.remove('bg-light', 'text-muted');
            event.target.classList.add('bg-white', 'text-dark', 'shadow-sm');
        });
    });
    
    // Automatically switch to correct tab if searching
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.has('vehicle_search')) {
        const triggerEl = document.querySelector('#vehicles-tab');
        bootstrap.Tab.getOrCreateInstance(triggerEl).show();
    }
</script>
@endpush
@endsection
