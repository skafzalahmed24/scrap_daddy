<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Feedback;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ScrapVehicle;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Key Metrics
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $newEnquiries = Feedback::count(); // Assuming feedback acts as enquiries

        // Revenue generation (Sum of total_amount - discount_applied for completed payments)
        $totalRevenue = Order::where('payment_status', 'completed')
            ->selectRaw('SUM(total_amount - discount_applied) as net_revenue')
            ->value('net_revenue') ?? 0;
        
        // Total Rewards Applied
        $totalRewardsApplied = Order::where('payment_status', 'completed')
            ->sum('discount_applied');

        // Extra Entity Counts
        $totalCategories = Category::count();
        $totalSubcategories = Subcategory::count();
        $totalScrapVehicles = ScrapVehicle::count();

        // 2. Pending List (Top 10 recent pending orders)
        $pendingOrders = Order::with(['user', 'category'])
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        // 3. Recent Revenue / Activity (Top 5 recently completed orders)
        $recentCompletedOrders = Order::with(['user'])
            ->where('payment_status', 'completed')
            ->latest('updated_at')
            ->take(5)
            ->get();

        // 4. Chart Data
        // Monthly Revenue (Current Year)
        $monthlyRevenueRaw = Order::selectRaw('MONTH(updated_at) as month, SUM(total_amount - discount_applied) as total')
            ->where('payment_status', 'completed')
            ->whereYear('updated_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        $monthlyRevenue = array_fill(1, 12, 0);
        foreach ($monthlyRevenueRaw as $data) {
            $monthlyRevenue[$data->month] = (float) $data->total;
        }
        $chartMonthlyRevenue = array_values($monthlyRevenue);

        // Order Status Distribution
        $orderStatusRaw = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
            
        $chartOrderStatusLabels = $orderStatusRaw->pluck('status')->map('ucfirst');
        $chartOrderStatusData = $orderStatusRaw->pluck('count');

        // Top Categories
        $categoriesRaw = Order::selectRaw('category_uuid, COUNT(*) as count')
            ->with('category')
            ->groupBy('category_uuid')
            ->orderByDesc('count')
            ->take(5)
            ->get();
            
        $chartCategoryLabels = $categoriesRaw->map(function($order) {
            return $order->category->title ?? 'Unknown';
        });
        $chartCategoryData = $categoriesRaw->pluck('count');

        return view('dashboard', compact(
            'totalUsers',
            'totalOrders',
            'newEnquiries',
            'totalRevenue',
            'totalRewardsApplied',
            'pendingOrders',
            'recentCompletedOrders',
            'chartMonthlyRevenue',
            'chartOrderStatusLabels',
            'chartOrderStatusData',
            'chartCategoryLabels',
            'chartCategoryData',
            'totalCategories',
            'totalSubcategories',
            'totalScrapVehicles'
        ));
    }
}
