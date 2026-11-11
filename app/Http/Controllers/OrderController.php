<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // User Methods
    public function index()
    {
        $orders = Order::with(['user', 'subcategory', 'category'])
            ->where('user_uuid', auth()->user()->uuid)
            ->latest()
            ->get();

        return view('customer.orders', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['category', 'subcategory'])
            ->where('user_uuid', auth()->user()->uuid)
            ->findOrFail($id);

        return view('customer.order-details', compact('order'));
    }

    public function create()
    {
        $subcategories = \App\Models\Subcategory::where('status', true)->get();
        $user = auth()->user();
        $coinValue = OrderPaymentService::coinValue();

        return view('customer.request-pickup', compact('subcategories', 'user', 'coinValue'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.subcategory_uuid' => 'required|exists:subcategories,uuid',
            'items.*.quantity' => 'required|integer|min:1|max:100000',
            'items.*.name' => 'required|string|max:255',
            'pickup_location' => 'required|string|max:500',
            'pin_code' => ['required', 'string', 'regex:/^[1-9][0-9]{5}$/'],
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required|string|max:50',
            'images' => 'nullable|array|max:10',
            'images.*' => 'string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // The order always belongs to the logged-in customer
        $user = auth()->user();

        if (!\App\Models\ServicePincode::isServiceable($request->pin_code)) {
            return back()->withInput()->with('error', \App\Models\ServicePincode::unavailableMessage($request->pin_code));
        }

        $firstSubcategory = \App\Models\Subcategory::where('uuid', $request->items[0]['subcategory_uuid'])->first();

        // Reward coins are applied later, once the admin has set the final amount
        // (same flow as the mobile app: Checkout -> redeem coins).
        Order::create([
            'user_uuid' => $user->uuid,
            'category_uuid' => $firstSubcategory ? $firstSubcategory->category_id : null,
            'items' => $request->items,
            'pickup_location' => $request->pickup_location,
            'pin_code' => $request->pin_code,
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
        $order = Order::where('user_uuid', auth()->user()->uuid)->findOrFail($id);

        if (in_array($order->status, ['pending', 'accepted'])) {
            $order->status = 'cancelled';
            $order->save();

            return redirect()->back()->with('success', 'Pick-up request has been cancelled.');
        }

        return redirect()->back()->with('error', 'This pick-up request can no longer be cancelled.');
    }

    // Admin Methods
    public function adminIndex(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            // Grouped so the search never bypasses other conditions
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('full_name', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

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

        if ($order->payment_status === 'completed' && $request->status !== 'completed') {
            return redirect()->back()->with('error', 'A paid order cannot be moved back.');
        }

        if ($request->status === 'completed' && !$request->filled('total_amount') && !($order->total_amount > 0)) {
            return redirect()->back()->with('error', 'Enter the final amount before completing the order.');
        }

        $order->status = $request->status;
        if ($request->filled('estimated_pickup_date')) {
            $order->estimated_pickup_date = $request->estimated_pickup_date;
        }

        if ($request->status === 'completed' && $request->filled('total_amount') && $order->payment_status !== 'completed') {
            $order->total_amount = $request->total_amount;
            $order->payment_status = 'pending';

            // Rewards are only credited after a successful payment (OrderPaymentService::markPaid)
            if ((int) $order->coins_earned === 0) {
                $rewardConfig = OrderPaymentService::rewardConfigFor($order);
                if ($rewardConfig) {
                    $order->coins_earned = $rewardConfig->reward_coins;
                }
            }
        }

        $order->save();

        return redirect()->back()->with('success', 'Order status updated.');
    }
}
