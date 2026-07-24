<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    // User Methods
    public function index()
    {
        // Assuming user is authenticated via some mechanism, normally auth()->user()
        // but here the session or auth might be custom based on the uuid.
        // For now, getting all for demonstration if auth is not fully configured
        $orders = Order::with('user', 'subcategory')->latest()->get(); // Ideally: auth()->user()->orders
        return view('customer.orders', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['category', 'subcategory'])->findOrFail($id);
        return view('customer.order-details', compact('order'));
    }

    public function create()
    {
        $subcategories = \App\Models\Subcategory::where('status', true)->get();
        return view('customer.request-pickup', compact('subcategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_uuid' => 'required|exists:users,uuid',
            'subcategory_uuid' => 'required|exists:subcategories,uuid',
            'pickup_location' => 'required|string',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required|string',
            'images' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $subcategory = \App\Models\Subcategory::where('uuid', $request->subcategory_uuid)->first();

        Order::create([
            'user_uuid' => $request->user_uuid,
            'category_uuid' => $subcategory ? $subcategory->category_id : null,
            'subcategory_uuid' => $request->subcategory_uuid,
            'pickup_location' => $request->pickup_location,
            'pickup_date' => $request->pickup_date,
            'pickup_time' => $request->pickup_time,
            'images' => $request->images,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect('/customer/home')->with('success', 'Confirmed! Our admin team will connect with you shortly within 24 to 48 hours to finalize the pickup.');
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);
        if ($order->status == 'pending' || $order->status == 'accepted') {
            $order->status = 'cancelled';
            $order->save();
        }
        return redirect()->back()->with('success', 'Pick-up request has been cancelled.');
    }

    // Admin Methods
    public function adminIndex()
    {
        $orders = Order::with('user')->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate([
            'status' => 'required|in:pending,accepted,completed,cancelled',
            'estimated_pickup_date' => 'nullable|date',
            'total_amount' => 'nullable|numeric|min:0|max:500000',
        ]);

        $order->status = $request->status;
        if ($request->has('estimated_pickup_date')) {
            $order->estimated_pickup_date = $request->estimated_pickup_date;
        }

        if ($request->status === 'completed' && $request->has('total_amount')) {
            $order->total_amount = $request->total_amount;
            if ($order->payment_status !== 'completed') {
                $order->payment_status = 'pending';
            }
        }

        $order->save();

        return redirect()->back()->with('success', 'Order status updated.');
    }
}
