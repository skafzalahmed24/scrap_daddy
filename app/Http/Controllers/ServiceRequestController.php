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
        $counts = self::actionCounts();

        $items = [];
        foreach (\App\Models\Order::where('status', 'pending')->latest()->take(4)->get() as $o) {
            $items[] = ['type' => 'order', 'title' => 'New order #' . str_pad($o->id, 4, '0', STR_PAD_LEFT) . ' needs acceptance',
                'text' => \Illuminate\Support\Str::limit($o->pickup_location ?? '', 50), 'time' => $o->created_at->diffForHumans(), 'url' => route('admin.orders.show', $o->id)];
        }
        foreach (\App\Models\ScrapVehicle::where('status', 'pending')->latest()->take(3)->get() as $v) {
            $items[] = ['type' => 'vehicle', 'title' => 'Scrap vehicle ' . $v->vehicle_number, 'text' => $v->vehicle_type . ' request waiting',
                'time' => $v->created_at->diffForHumans(), 'url' => route('admin.scrap-vehicles.index', ['status' => 'pending'])];
        }
        foreach (\App\Models\Order::with('user')->where('status', 'completed')->where('payment_status', '!=', 'completed')->latest('updated_at')->take(3)->get() as $o) {
            $items[] = ['type' => 'payment', 'title' => 'Payment awaited #' . str_pad($o->id, 4, '0', STR_PAD_LEFT), 'text' => ($o->user->full_name ?? 'Customer') . ' · ₹' . number_format($o->total_amount, 0),
                'time' => $o->updated_at->diffForHumans(), 'url' => route('admin.payments.index', ['status' => 'pending'])];
        }
        foreach (ServiceRequest::where('status', 'new')->latest()->take(3)->get() as $r) {
            $items[] = ['type' => 'request', 'title' => 'Service request · ' . $r->pin_code, 'text' => $r->name . ' (' . $r->phone . ')',
                'time' => $r->created_at->diffForHumans(), 'url' => route('admin.service-requests.index')];
        }
        foreach (\App\Models\SupportTicket::where('status', '!=', 'closed')->latest()->take(3)->get() as $t) {
            $items[] = ['type' => 'ticket', 'title' => 'Ticket: ' . \Illuminate\Support\Str::limit($t->subject, 30), 'text' => 'Waiting for a reply',
                'time' => $t->created_at->diffForHumans(), 'url' => route('admin.tickets.index')];
        }

        return response()->json($counts + ['items' => array_slice($items, 0, 10)]);
    }

    /** Everything that is waiting for an admin action. Used by the sidebar badges, bell and dashboard. */
    public static function actionCounts(): array
    {
        $orders = \App\Models\Order::where('status', 'pending')->count();
        $vehicles = \App\Models\ScrapVehicle::where('status', 'pending')->count();
        $payments = \App\Models\Order::where('status', 'completed')->where('payment_status', '!=', 'completed')->count()
            + \App\Models\OrderPayment::where('status', 'pending')->where('method', '!=', 'razorpay')->count();
        $requests = ServiceRequest::where('status', 'new')->whereNull('read_at')->count();
        $tickets = \App\Models\SupportTicket::where('status', '!=', 'closed')->count();

        return [
            'orders' => $orders, 'vehicles' => $vehicles, 'payments' => $payments, 'requests' => $requests, 'tickets' => $tickets,
            'total' => $orders + $vehicles + $payments + $requests + $tickets,
        ];
    }
}
