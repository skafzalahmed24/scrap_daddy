<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/** Payments ledger: what has been received, what is still awaited, and which payments are new. */
class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status'); // '', paid, pending

        // Only orders that reached the payment stage (completed pickup with an amount, or already paid)
        $base = Order::with(['user', 'category', 'subcategory'])
            ->where(function ($q) {
                $q->where('payment_status', 'completed')
                    ->orWhere('paid_amount', '>', 0)
                    ->orWhereHas('payments', fn ($p) => $p->where('status', 'pending'))
                    ->orWhere(function ($w) {
                        $w->where('status', 'completed')->where('total_amount', '>', 0);
                    });
            });

        $paidQ = (clone $base)->where('payment_status', 'completed');
        $pendingQ = (clone $base)->where('payment_status', '!=', 'completed');
        $verifyScope = fn ($q) => $q->whereHas('payments', fn ($p) => $p->where('status', 'pending')->where('method', '!=', 'razorpay'));

        $net = 'SUM(total_amount - COALESCE(discount_applied, 0))';
        $stats = [
            'received' => (float) ((clone $paidQ)->selectRaw("$net as t")->value('t') ?? 0),
            'received_today' => (float) ((clone $paidQ)->where('updated_at', '>=', Carbon::today())->selectRaw("$net as t")->value('t') ?? 0),
            'paid_count' => (clone $paidQ)->count(),
            'pending_amount' => (float) (clone $pendingQ)->sum('total_amount'),
            'pending_count' => (clone $pendingQ)->count(),
            'rewards_used' => (float) (clone $paidQ)->sum('discount_applied'),
            'new_count' => (clone $paidQ)->where('updated_at', '>=', Carbon::now()->subDay())->count(),
            'verify_count' => \App\Models\OrderPayment::where('status', 'pending')->where('method', '!=', 'razorpay')->count(),
            'advance_count' => (clone $base)->where('payment_status', 'partial')->count(),
        ];

        $query = clone $base;
        if ($status === 'paid') {
            $query->where('payment_status', 'completed');
        } elseif ($status === 'pending') {
            $query->where('payment_status', '!=', 'completed');
        } elseif ($status === 'verify') {
            $verifyScope($query);
        } elseif ($status === 'partial') {
            $query->where('payment_status', 'partial');
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('id', 'like', "%{$s}%")
                    ->orWhere('payment_id', 'like', "%{$s}%")
                    ->orWhereHas('user', fn ($u) => $u->where('full_name', 'like', "%{$s}%")->orWhere('phone_number', 'like', "%{$s}%"));
            });
        }

        $payments = $query->latest('updated_at')->paginate(15)->withQueryString();

        return view('admin.payments', compact('payments', 'stats', 'status'));
    }
}
