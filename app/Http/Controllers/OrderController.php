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
        $user = auth()->user();
        $rewardSetting = \App\Models\RewardSetting::first();
        $coinValue = $rewardSetting ? $rewardSetting->coin_value_in_rupees : 0.10;
        return view('customer.request-pickup', compact('subcategories', 'user', 'coinValue'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_uuid' => 'required|exists:users,uuid',
            'items' => 'required|array',
            'items.*.subcategory_uuid' => 'required|exists:subcategories,uuid',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.name' => 'required|string',
            'pickup_location' => 'required|string',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required|string',
            'images' => 'nullable|array',
            'notes' => 'nullable|string',
            'apply_rewards' => 'nullable|boolean',
        ]);

        $firstSubcategory = \App\Models\Subcategory::where('uuid', $request->items[0]['subcategory_uuid'])->first();

        $coinsToRedeem = 0;
        $discountApplied = 0;

        if ($request->has('apply_rewards') && $request->apply_rewards) {
            $user = \App\Models\User::where('uuid', $request->user_uuid)->first();
            if ($user && $user->reward_coins > 0) {
                $coinsToRedeem = $user->reward_coins;
                
                $setting = \App\Models\RewardSetting::first();
                $coinValue = $setting ? $setting->coin_value_in_rupees : 0.10;
                
                $discountApplied = $coinsToRedeem * $coinValue;
                
                // Deduct coins
                $user->reward_coins = 0;
                $user->save();
            }
        }

        Order::create([
            'user_uuid' => $request->user_uuid,
            'category_uuid' => $firstSubcategory ? $firstSubcategory->category_id : null,
            'items' => $request->items,
            'pickup_location' => $request->pickup_location,
            'pickup_date' => $request->pickup_date,
            'pickup_time' => $request->pickup_time,
            'images' => $request->images,
            'notes' => $request->notes,
            'status' => 'pending',
            'coins_redeemed' => $coinsToRedeem,
            'discount_applied' => $discountApplied,
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
    public function adminIndex(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                  });
        }

        $orders = $query->paginate(15);
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

            // Calculate Rewards if completing for the first time
            if ($order->isDirty('status') && $order->coins_earned == 0) {
                $rewardConfig = \App\Models\RewardConfiguration::where('status', 1)
                    ->where('min_amount', '<=', $order->total_amount)
                    ->where('max_amount', '>=', $order->total_amount)
                    ->first();
                    
                if ($rewardConfig) {
                    $order->coins_earned = $rewardConfig->reward_coins;
                    // Coins will be credited to the user's account only after successful payment
                }
            }
        }

        $order->save();

        return redirect()->back()->with('success', 'Order status updated.');
    }
}
