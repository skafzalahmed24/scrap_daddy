<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceRequestController extends Controller
{
    /**
     * Public endpoint (website + mobile app): a customer asks for service in a PIN code we don't cover yet.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:120',
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s]{7,15}$/'],
            'email' => 'nullable|email|max:150',
            'pin_code' => ['required', 'string', 'regex:/^[1-9][0-9]{5}$/'],
            'area' => 'nullable|string|max:150',
            'message' => 'nullable|string|max:1000',
            'user_uuid' => 'nullable|string|max:64',
        ], [
            'name.required' => 'Please enter your name.',
            'phone.required' => 'Please enter your phone number.',
            'phone.regex' => 'Please enter a valid phone number.',
            'email.email' => 'Please enter a valid email address.',
            'pin_code.regex' => 'Please enter a valid 6-digit PIN code.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'data' => ['errors' => $validator->errors()],
            ], 200);
        }

        // Stop accidental double submits: same phone + PIN within the last 10 minutes
        $duplicate = ServiceRequest::where('phone', $request->phone)
            ->where('pin_code', $request->pin_code)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->exists();

        if (!$duplicate) {
            ServiceRequest::create([
                'user_uuid' => auth('web')->user()->uuid ?? $request->user_uuid,
                'name' => trim($request->name),
                'phone' => trim($request->phone),
                'email' => $request->email ? trim($request->email) : null,
                'pin_code' => $request->pin_code,
                'area' => $request->area ? trim($request->area) : null,
                'message' => $request->message ? trim($request->message) : null,
                'status' => 'new',
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => "Thank you! We've received your request and will contact you as soon as service starts in {$request->pin_code}.",
        ]);
    }

    // ----- Admin -----

    public function adminIndex(Request $request)
    {
        $status = $request->query('status');
        $query = ServiceRequest::latest();

        if (in_array($status, ServiceRequest::STATUSES, true)) {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('pin_code', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $requests = $query->paginate(15)->withQueryString();

        // Opening the list marks what's on the page as seen, so the badge count drops
        ServiceRequest::whereIn('id', $requests->pluck('id'))->whereNull('read_at')->update(['read_at' => now()]);

        $counts = [
            'all' => ServiceRequest::count(),
            'new' => ServiceRequest::where('status', 'new')->count(),
            'contacted' => ServiceRequest::where('status', 'contacted')->count(),
            'closed' => ServiceRequest::where('status', 'closed')->count(),
        ];

        return view('admin.service-requests', compact('requests', 'counts', 'status'));
    }

    public function adminUpdateStatus(Request $request, $id)
    {
        $data = $request->validate(['status' => 'required|in:' . implode(',', ServiceRequest::STATUSES)]);

        $row = ServiceRequest::findOrFail($id);
        $row->update(['status' => $data['status'], 'read_at' => $row->read_at ?? now()]);

        return redirect()->back()->with('success', 'Request marked as ' . $data['status'] . '.');
    }

    public function adminDestroy($id)
    {
        ServiceRequest::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Request deleted.');
    }

    /** JSON used by the admin header bell + sidebar badges (polled every 30s). */
    public function adminNotifications()
    {
        $newRequests = ServiceRequest::where('status', 'new')->whereNull('read_at')->count();
        $pendingOrders = Order::where('status', 'pending')->count();

        $items = [];
        foreach (ServiceRequest::where('status', 'new')->latest()->take(5)->get() as $r) {
            $items[] = [
                'type' => 'request',
                'title' => 'Service request · ' . $r->pin_code,
                'text' => $r->name . ' (' . $r->phone . ')',
                'time' => $r->created_at->diffForHumans(),
                'url' => route('admin.service-requests.index'),
            ];
        }
        foreach (Order::where('status', 'pending')->latest()->take(5)->get() as $o) {
            $items[] = [
                'type' => 'order',
                'title' => 'New order #ORD-' . str_pad($o->id, 4, '0', STR_PAD_LEFT),
                'text' => \Illuminate\Support\Str::limit($o->pickup_location ?? '', 50),
                'time' => $o->created_at->diffForHumans(),
                'url' => route('admin.orders.index'),
            ];
        }

        return response()->json([
            'orders' => $pendingOrders,
            'requests' => $newRequests,
            'total' => $pendingOrders + $newRequests,
            'items' => array_slice($items, 0, 8),
        ]);
    }
}
