@extends('layouts.admin')

@section('title', 'Admin Dashboard - Scrap Daddy')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Overview</h1>

    </div>
    
    <!-- Stats Grid -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 30px;">
        <div class="stat-card" style="background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px;">
            <div class="stat-icon purple" style="width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                <i class="fa-solid fa-indian-rupee-sign"></i>
            </div>
            <div class="stat-details">
                <h3 style="margin: 0 0 4px 0; font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">Total Revenue</h3>
                <p style="margin: 0; font-size: 1.5rem; font-weight: 600; color: var(--text-dark);">₹{{ number_format($totalRevenue, 2) }}</p>
            </div>
        </div>

        <div class="stat-card" style="background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px;">
            <div class="stat-icon blue" style="width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <div class="stat-details">
                <h3 style="margin: 0 0 4px 0; font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">Total Pickups</h3>
                <p style="margin: 0; font-size: 1.5rem; font-weight: 600; color: var(--text-dark);">{{ number_format($totalOrders) }}</p>
            </div>
        </div>

        <div class="stat-card" style="background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px;">
            <div class="stat-icon orange" style="width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; background: rgba(249, 115, 22, 0.1); color: #f97316;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-details">
                <h3 style="margin: 0 0 4px 0; font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">Pending Pickups</h3>
                <p style="margin: 0; font-size: 1.5rem; font-weight: 600; color: var(--text-dark);">{{ number_format($pendingOrders->count()) }}</p>
            </div>
        </div>

        <div class="stat-card" style="background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px;">
            <div class="stat-icon green" style="width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-details">
                <h3 style="margin: 0 0 4px 0; font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">Total Users</h3>
                <p style="margin: 0; font-size: 1.5rem; font-weight: 600; color: var(--text-dark);">{{ number_format($totalUsers) }}</p>
            </div>
        </div>
    </div>
    
    <!-- Mini Stats Grid for Entities -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <div class="stat-card" style="background: white; padding: 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 16px;">
            <div class="stat-icon text-primary" style="width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; background: rgba(13, 110, 253, 0.1);">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div class="stat-details">
                <h3 style="margin: 0 0 2px 0; font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Categories</h3>
                <p style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-dark);">{{ number_format($totalCategories) }}</p>
            </div>
        </div>

        <div class="stat-card" style="background: white; padding: 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 16px;">
            <div class="stat-icon text-info" style="width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; background: rgba(13, 202, 240, 0.1);">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div class="stat-details">
                <h3 style="margin: 0 0 2px 0; font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Subcategories</h3>
                <p style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-dark);">{{ number_format($totalSubcategories) }}</p>
            </div>
        </div>

        <div class="stat-card" style="background: white; padding: 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 16px;">
            <div class="stat-icon text-warning" style="width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; background: rgba(255, 193, 7, 0.1);">
                <i class="fa-solid fa-car"></i>
            </div>
            <div class="stat-details">
                <h3 style="margin: 0 0 2px 0; font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Scrap Vehicles</h3>
                <p style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-dark);">{{ number_format($totalScrapVehicles) }}</p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3" style="border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0 fw-bold">Revenue Overview (This Year)</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3" style="border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0 fw-bold">Order Status</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <canvas id="statusChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3" style="border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0 fw-bold">Top Scrapped Categories</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <canvas id="categoryChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Pending Pickups Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0 fw-bold">Recent Pending Pickups</h5>
                    <a href="{{ url('/admin/orders') }}" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="ps-4">Order ID</th>
                                    <th>Customer</th>
                                    <th>Category</th>
                                    <th>Scheduled Date</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingOrders as $order)
                                <tr>
                                    <td class="ps-4 fw-semibold text-brand">#{{ $order->id }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $order->user->full_name ?? 'Unknown' }}</span>
                                            <small class="text-muted">{{ $order->user->phone_number ?? '' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                            {{ $order->category->title ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order->pickup_date)
                                            {{ \Carbon\Carbon::parse($order->pickup_date)->format('d M, Y') }}
                                            <br><small class="text-muted">{{ $order->pickup_time }}</small>
                                        @else
                                            <span class="text-muted">Not Set</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ url('/admin/orders') }}" class="btn btn-sm btn-primary">Process</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No pending pickups right now! 🎉</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Revenue & Activity -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3" style="border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0 fw-bold">Recent Completed Pickups</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($recentCompletedOrders as $order)
                        <li class="list-group-item p-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-success-subtle text-success rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">#{{ $order->id }}</h6>
                                    <small class="text-muted">{{ $order->user->full_name ?? 'Unknown' }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 fw-bold text-success">+₹{{ number_format($order->total_amount, 2) }}</h6>
                                <small class="text-muted">{{ $order->updated_at->diffForHumans() }}</small>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item p-4 text-center text-muted">
                            No recent completed pickups.
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-4" style="border-radius: 12px; background: linear-gradient(135deg, #10b981, #059669); color: white;">
                <div class="card-body p-4">
                    <h6 class="text-white-50 text-uppercase fw-bold mb-1" style="font-size: 0.8rem; letter-spacing: 1px;">Total Rewards Given</h6>
                    <h2 class="fw-bold mb-0">₹{{ number_format($totalRewardsApplied, 2) }}</h2>
                    <p class="mb-0 mt-2 text-white-50" style="font-size: 0.9rem;">Discounts applied to customers via coins.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Chart
            const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctxRevenue, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Revenue (₹)',
                        data: {!! json_encode($chartMonthlyRevenue) !!},
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Order Status Chart
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartOrderStatusLabels) !!},
                    datasets: [{
                        data: {!! json_encode($chartOrderStatusData) !!},
                        backgroundColor: ['#10b981', '#f97316', '#ef4444', '#3b82f6', '#6b7280'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
                    }
                }
            });

            // Category Chart
            const ctxCategory = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctxCategory, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($chartCategoryLabels) !!},
                    datasets: [{
                        data: {!! json_encode($chartCategoryData) !!},
                        backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
                    }
                }
            });
        });
    </script>
@endsection
