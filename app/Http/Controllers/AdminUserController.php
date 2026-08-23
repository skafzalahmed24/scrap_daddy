<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\ScrapVehicle;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('orders', 'scrapVehicles')->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('uuid', 'like', "%{$search}%");
        }

        $users = $query->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }

    public function show(Request $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        // Paginate Orders for this user
        $ordersQuery = Order::with('category')->where('user_uuid', $uuid)->latest();
        if ($request->has('order_search') && $request->order_search != '') {
            $ordersQuery->where('id', 'like', "%{$request->order_search}%");
        }
        $orders = $ordersQuery->paginate(10, ['*'], 'orders_page');

        // Paginate Scrap Vehicles for this user
        $vehiclesQuery = ScrapVehicle::where('user_uuid', $uuid)->latest();
        if ($request->has('vehicle_search') && $request->vehicle_search != '') {
            $vehiclesQuery->where(function ($q) use ($request) {
                $q->where('vehicle_number', 'like', "%{$request->vehicle_search}%")
                  ->orWhere('vehicle_type', 'like', "%{$request->vehicle_search}%");
            });
        }
        $vehicles = $vehiclesQuery->paginate(10, ['*'], 'vehicles_page');

        return view('admin.users.show', compact('user', 'orders', 'vehicles'));
    }
}
