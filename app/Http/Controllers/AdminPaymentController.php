<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $customerSearch = $request->input('search_customer');

        // 1. Fetch Paginated Customers who have completed payments or pending orders
        $customersQuery = User::whereHas('orders')
            ->withCount(['orders as pending_orders_count' => function ($q) {
                $q->where('payment_status', '!=', 'completed')->where('status', '!=', 'cancelled');
            }])
            ->withSum(['orders as total_net_paid' => function ($q) {
                $q->where('payment_status', 'completed');
            }], 'total_amount')
            ->withSum(['orders as total_rewards_applied' => function ($q) {
                $q->where('payment_status', 'completed');
            }], 'discount_applied')
            ->orderByDesc('total_net_paid');

        if ($customerSearch) {
            $customersQuery->where(function($q) use ($customerSearch) {
                $q->where('full_name', 'like', "%{$customerSearch}%")
                  ->orWhere('phone_number', 'like', "%{$customerSearch}%");
            });
        }

        $customers = $customersQuery->paginate(10);

        // Adjust total_net_paid in memory since DB sum doesn't allow dynamic calculation of (total_amount - discount_applied) directly without selectRaw easily in withSum
        foreach ($customers as $customer) {
            $customer->total_net_paid = $customer->total_net_paid - $customer->total_rewards_applied;
        }

        $selectedUserId = $request->input('user_uuid');
        
        $selectedUser = null;
        $pendingPayments = collect();
        $completedPayments = collect();

        if ($selectedUserId) {
            $selectedUser = User::find($selectedUserId);
            
            if ($selectedUser) {
                // Pending Payments for Selected Customer
                $pendingPayments = Order::with('category')
                    ->where('user_uuid', $selectedUserId)
                    ->where('payment_status', '!=', 'completed')
                    ->where('status', '!=', 'cancelled')
                    ->latest()
                    ->get();

                // Completed Payments for Selected Customer
                $completedPayments = Order::with('category')
                    ->where('user_uuid', $selectedUserId)
                    ->where('payment_status', 'completed')
                    ->latest('updated_at')
                    ->get();
            }
        }

        // Global Revenue and Stats for initial state
        $totalRevenue = Order::where('payment_status', 'completed')
            ->selectRaw('SUM(total_amount - discount_applied) as net_revenue')
            ->value('net_revenue') ?? 0;
            
        $globalPendingOrdersCount = Order::where('payment_status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->count();
            
        $globalPendingAmount = Order::where('payment_status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        return view('admin.payments', compact(
            'customers',
            'selectedUserId',
            'selectedUser',
            'totalRevenue',
            'globalPendingOrdersCount',
            'globalPendingAmount',
            'pendingPayments',
            'completedPayments',
            'customerSearch'
        ));
    }
}
