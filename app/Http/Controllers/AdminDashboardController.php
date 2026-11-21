<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\ScrapVehicle;
use App\Models\ServiceRequest;
use App\Models\Subcategory;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Carbon;

/** One-screen admin overview: every module's numbers and latest activity, no tab hopping. */
class AdminDashboardController extends Controller
{
    public function index()
    {
        $netExpr = 'SUM(total_amount - COALESCE(discount_applied, 0))';

        // ---- headline numbers
        $totalRevenue = (float) (Order::where('payment_status', 'completed')->selectRaw("$netExpr as t")->value('t') ?? 0);
        $pendingPaymentAmount = (float) (Order::where('status', 'completed')->where('payment_status', '!=', 'completed')->sum('total_amount'));
        $pendingPaymentCount = Order::where('status', 'completed')->where('payment_status', '!=', 'completed')->count();

        $orderCounts = Order::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');
        $vehicleCounts = ScrapVehicle::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');

        $kpi = [
            'revenue' => $totalRevenue,
            'revenue_month' => (float) (Order::where('payment_status', 'completed')
                ->where('updated_at', '>=', Carbon::now()->startOfMonth())->selectRaw("$netExpr as t")->value('t') ?? 0),
            'orders_total' => (int) $orderCounts->sum(),
            'orders_today' => Order::whereDate('created_at', Carbon::today())->count(),
            'orders_pending' => (int) ($orderCounts['pending'] ?? 0),
            'orders_completed' => (int) ($orderCounts['completed'] ?? 0),
            'customers' => User::where('status', 1)->count(),
            'customers_week' => User::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
            'vehicles_total' => (int) $vehicleCounts->sum(),
            'vehicles_pending' => (int) ($vehicleCounts['pending'] ?? 0),
            'payments_pending_amount' => $pendingPaymentAmount,
            'payments_pending_count' => $pendingPaymentCount,
            'tickets_open' => SupportTicket::where('status', '!=', 'closed')->count(),
            'requests_new' => ServiceRequest::where('status', 'new')->count(),
            'categories' => Category::count(),
            'subcategories' => Subcategory::count(),
        ];

        // ---- latest activity (all modules on one page)
        $recentOrders = Order::with(['user', 'category', 'subcategory'])->latest()->take(8)->get();
        $recentPayments = Order::with('user')->where('payment_status', 'completed')->latest('updated_at')->take(6)->get();
        $awaitingPayment = Order::with('user')->where('status', 'completed')->where('payment_status', '!=', 'completed')->latest('updated_at')->take(5)->get();
        $recentVehicles = ScrapVehicle::with('user')->latest()->take(5)->get();
        $openTickets = SupportTicket::with('user')->where('status', '!=', 'closed')->latest()->take(5)->get();
        $newRequests = ServiceRequest::where('status', 'new')->latest()->take(5)->get();

        // ---- charts
        // grouped in PHP (not MONTH()/DATE() in SQL) so it behaves the same on MySQL and SQLite
        $monthly = array_fill(1, 12, 0);
        Order::where('payment_status', 'completed')->whereYear('updated_at', date('Y'))
            ->get(['updated_at', 'total_amount', 'discount_applied'])
            ->each(function ($o) use (&$monthly) {
                $monthly[(int) $o->updated_at->format('n')] += (float) $o->total_amount - (float) ($o->discount_applied ?? 0);
            });

        $days = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i));
        $perDay = Order::where('created_at', '>=', Carbon::today()->subDays(6))->get(['created_at'])
            ->groupBy(fn ($o) => $o->created_at->toDateString())->map->count();
        $chart = [
            'monthlyRevenue' => array_values($monthly),
            'statusLabels' => $orderCounts->keys()->map(fn ($s) => ucfirst($s))->values(),
            'statusData' => $orderCounts->values(),
            'dayLabels' => $days->map(fn ($d) => $d->format('d M'))->values(),
            'dayData' => $days->map(fn ($d) => (int) ($perDay[$d->toDateString()] ?? 0))->values(),
        ];

        return view('dashboard', compact(
            'kpi', 'recentOrders', 'recentPayments', 'awaitingPayment', 'recentVehicles', 'openTickets', 'newRequests', 'chart'
        ));
    }
}
