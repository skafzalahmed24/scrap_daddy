<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\SiteSetting;
use App\Models\RewardConfiguration;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;

/**
 * Single place for payment/reward rules so the website and the mobile API
 * behave identically.
 */
class OrderPaymentService
{
    /** Amount (in rupees) the customer still has to pay for the order. */
    public static function payableAmount(Order $order): float
    {
        $amount = (float) ($order->total_amount ?? 0) - (float) ($order->discount_applied ?? 0);

        return max(0, round($amount, 2));
    }

    public static function razorpay(): Api
    {
        return new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    /**
     * Confirm with Razorpay that the payment really exists, belongs to this
     * order's amount and is paid. Returns null when OK, otherwise an error message.
     */
    public static function verifyRazorpayPayment(
        Order $order,
        string $paymentId,
        ?string $razorpayOrderId = null,
        ?string $signature = null,
        ?float $expectedAmount = null
    ): ?string {
        try {
            $api = self::razorpay();

            if ($razorpayOrderId && $signature) {
                $api->utility->verifyPaymentSignature([
                    'razorpay_order_id' => $razorpayOrderId,
                    'razorpay_payment_id' => $paymentId,
                    'razorpay_signature' => $signature,
                ]);
            }

            $payment = $api->payment->fetch($paymentId);

            if ($payment['status'] === 'authorized') {
                $payment = $payment->capture([
                    'amount' => $payment['amount'],
                    'currency' => $payment['currency'],
                ]);
            }

            if ($payment['status'] !== 'captured') {
                return 'Payment has not been completed.';
            }

            $expectedPaise = (int) round(($expectedAmount ?? self::dueNow($order)['amount']) * 100);
            if ((int) $payment['amount'] !== $expectedPaise) {
                return 'Payment amount does not match the order amount.';
            }

            if ($razorpayOrderId && !empty($payment['order_id']) && $payment['order_id'] !== $razorpayOrderId) {
                return 'Payment does not belong to this order.';
            }
        } catch (\Throwable $e) {
            report($e);

            return 'Unable to verify payment.';
        }

        return null;
    }

    // ------------------------------------------------------------------
    // Advance payment + payment methods
    // ------------------------------------------------------------------

    /** Admin > Payment Settings. */
    public static function advanceConfig(): array
    {
        $percent = (float) SiteSetting::get('advance_percent', 20);

        return [
            'enabled' => SiteSetting::get('advance_enabled', '0') === '1' && $percent > 0,
            'percent' => max(1, min(90, $percent)),
        ];
    }

    /** Enabled payment methods with the details the customer needs to use them. */
    public static function methods(): array
    {
        $s = SiteSetting::values();
        $out = [];

        if (($s['pay_razorpay'] ?? '1') === '1' && config('services.razorpay.key')) {
            $out[] = ['key' => 'razorpay', 'label' => 'Pay online', 'hint' => 'Card, UPI or Netbanking - instant confirmation'];
        }
        if (($s['pay_upi'] ?? '0') === '1' && !empty($s['upi_id'])) {
            $out[] = ['key' => 'upi', 'label' => 'UPI', 'hint' => 'Pay to our UPI ID, then enter the UTR / reference',
                'upi_id' => $s['upi_id'], 'upi_name' => $s['upi_name'] ?: $s['company_name']];
        }
        if (($s['pay_bank'] ?? '0') === '1' && !empty($s['bank_account'])) {
            $out[] = ['key' => 'bank', 'label' => 'Bank transfer', 'hint' => 'NEFT / IMPS, then enter the UTR / reference',
                'bank_holder' => $s['bank_holder'], 'bank_name' => $s['bank_name'], 'bank_account' => $s['bank_account'], 'bank_ifsc' => $s['bank_ifsc']];
        }
        if (($s['pay_cash'] ?? '0') === '1') {
            $out[] = ['key' => 'cash', 'label' => 'Cash', 'hint' => 'Pay in cash to our executive - confirmed by our team'];
        }

        return $out;
    }

    /** Money received so far. A completed payment always counts as the full payable amount (also for older orders). */
    public static function paidTotal(Order $order): float
    {
        $paid = (float) ($order->paid_amount ?? 0);

        return $order->payment_status === 'completed' ? max($paid, self::payableAmount($order)) : $paid;
    }

    public static function remaining(Order $order): float
    {
        if ($order->payment_status === 'completed') {
            return 0.0;
        }

        return max(0, round(self::payableAmount($order) - (float) ($order->paid_amount ?? 0), 2));
    }

    /** Gateway (Razorpay) fee for an online amount, using the % set in Admin > Payment Settings. */
    public static function gatewayFee(float $amount): float
    {
        return round($amount * max(0, (float) SiteSetting::get('gateway_fee_percent', 2)) / 100, 2);
    }

    /**
     * What the customer must pay right now:
     * advance (a % of the bill, when enabled and nothing is paid yet), the balance, or the full amount.
     */
    public static function dueNow(Order $order): array
    {
        $total = self::payableAmount($order);
        $paid = self::paidTotal($order);
        $remaining = self::remaining($order);

        $info = ['kind' => 'none', 'amount' => 0.0, 'total' => $total, 'paid' => $paid, 'remaining' => $remaining, 'percent' => null, 'balance_after' => 0.0];
        if ($remaining <= 0 || $total <= 0) {
            return $info;
        }

        $adv = self::advanceConfig();
        if ($adv['enabled'] && $paid <= 0 && $order->status !== 'completed') {
            $amount = min($remaining, max(1, round($total * $adv['percent'] / 100, 2)));

            return ['kind' => 'advance', 'amount' => $amount, 'percent' => $adv['percent'], 'balance_after' => round($remaining - $amount, 2)] + $info;
        }

        return ['kind' => $paid > 0 ? 'balance' : 'full', 'amount' => $remaining] + $info;
    }

    /** Create a Razorpay order for the amount due now; the app / website opens checkout with it. */
    public static function createGatewayOrder(Order $order): array
    {
        $due = self::dueNow($order);
        if ($due['amount'] <= 0) {
            throw new \RuntimeException('Nothing is due for this order.');
        }

        $rzp = self::razorpay()->order->create([
            'receipt' => 'order_' . $order->id . '_' . now()->timestamp,
            'amount' => (int) round($due['amount'] * 100),
            'currency' => 'INR',
            'payment_capture' => 1,
            'notes' => ['order_id' => (string) $order->id, 'kind' => $due['kind']],
        ]);

        OrderPayment::create([
            'order_id' => $order->id, 'user_uuid' => $order->user_uuid, 'kind' => $due['kind'], 'amount' => $due['amount'],
            'method' => 'razorpay', 'gateway_order_id' => $rzp['id'], 'status' => 'pending',
        ]);

        return [
            'key_id' => config('services.razorpay.key'),
            'gateway_order_id' => $rzp['id'],
            'amount_paise' => (int) round($due['amount'] * 100),
            'currency' => 'INR',
            'kind' => $due['kind'],
            'amount' => $due['amount'],
        ];
    }

    /**
     * Verify a Razorpay payment and record it. Returns an error message, or null on success.
     * Works with the server-created gateway order (preferred) and, for older app builds, without one.
     */
    public static function verifyAndRecordRazorpay(Order $order, string $paymentId, ?string $gatewayOrderId, ?string $signature): ?string
    {
        if (OrderPayment::where('order_id', $order->id)->where('reference', $paymentId)->where('status', 'paid')->exists()) {
            return null; // already recorded (double callback)
        }

        $pending = $gatewayOrderId
            ? OrderPayment::where('order_id', $order->id)->where('gateway_order_id', $gatewayOrderId)->where('status', 'pending')->first()
            : null;

        $expected = $pending ? (float) $pending->amount : self::dueNow($order)['amount'];
        if ($expected <= 0) {
            return 'Nothing is due for this order.';
        }

        $error = self::verifyRazorpayPayment($order, $paymentId, $gatewayOrderId, $signature, $expected);
        if ($error) {
            return $error;
        }

        $kind = $pending->kind ?? self::dueNow($order)['kind'];
        self::recordPayment($order, $kind, $expected, 'razorpay', $paymentId, 'paid', $gatewayOrderId, null, $pending);

        return null;
    }

    /**
     * Record a payment. status=paid counts immediately (online / admin-confirmed cash);
     * status=pending waits for the admin to verify (UPI / bank / cash promised by the customer).
     */
    public static function recordPayment(
        Order $order, string $kind, float $amount, string $method, ?string $reference, string $status = 'paid',
        ?string $gatewayOrderId = null, ?string $note = null, ?OrderPayment $existing = null
    ): OrderPayment {
        return DB::transaction(function () use ($order, $kind, $amount, $method, $reference, $status, $gatewayOrderId, $note, $existing) {
            $payment = $existing ?? new OrderPayment(['order_id' => $order->id, 'user_uuid' => $order->user_uuid]);
            $payment->fill([
                'kind' => $kind, 'amount' => $amount, 'method' => $method, 'reference' => $reference,
                'gateway_order_id' => $gatewayOrderId ?? $payment->gateway_order_id, 'status' => $status, 'note' => $note ?? $payment->note,
            ]);
            if ($status !== 'pending') {
                $payment->reviewed_at = now();
            }
            $payment->save();

            if ($status === 'paid') {
                self::applyPaid($payment);
            }

            return $payment;
        });
    }

    /** Admin approves a UPI / bank / cash payment. */
    public static function approve(OrderPayment $payment, ?string $note = null): OrderPayment
    {
        return DB::transaction(function () use ($payment, $note) {
            $payment = OrderPayment::whereKey($payment->id)->lockForUpdate()->first();
            if ($payment->status === 'paid') {
                return $payment;
            }
            $payment->fill(['status' => 'paid', 'reviewed_at' => now(), 'note' => $note ?? $payment->note])->save();
            self::applyPaid($payment);

            return $payment;
        });
    }

    public static function reject(OrderPayment $payment, ?string $note = null): OrderPayment
    {
        if ($payment->status === 'pending') {
            $payment->fill(['status' => 'rejected', 'reviewed_at' => now(), 'note' => $note ?? $payment->note])->save();
        }

        return $payment;
    }

    /** After the amount/discount changes: complete the order when what was paid already covers it. */
    public static function settleIfCovered(Order $order): Order
    {
        if ($order->payment_status !== 'completed' && (float) $order->paid_amount > 0 && $order->paid_amount + 0.009 >= self::payableAmount($order)) {
            return self::markPaid($order);
        }

        return $order;
    }

    /** Everything the app / website needs to show "how to pay" and the status. */
    public static function summary(Order $order): array
    {
        $due = self::dueNow($order);
        $pending = $order->payments()->where('status', 'pending')->where('method', '!=', 'razorpay')->exists();

        return [
            'order_id' => $order->id,
            'order_amount' => (float) $order->total_amount,
            'discount' => (float) ($order->discount_applied ?? 0),
            'total' => $due['total'],
            'paid' => $due['paid'],
            'remaining' => $due['remaining'],
            'due_now' => $due,
            'advance' => self::advanceConfig(),
            'status' => self::statusKey($order, $pending),
            'status_label' => self::statusLabel(self::statusKey($order, $pending)),
            'methods' => self::methods(),
            'payments' => $order->payments()->get(['id', 'kind', 'amount', 'method', 'reference', 'status', 'note', 'created_at'])
                ->map(fn ($p) => $p->toArray() + ['method_label' => $p->method_label])->all(),
        ];
    }

    public static function statusKey(Order $order, bool $verifying = false): string
    {
        if ($order->payment_status === 'completed') {
            return 'paid';
        }
        if ($verifying) {
            return 'verifying';
        }
        if ((float) $order->paid_amount > 0) {
            return 'partial';
        }
        if ($order->status === 'cancelled') {
            return 'cancelled';
        }

        return (float) $order->total_amount > 0 ? 'due' : 'none';
    }

    public static function statusLabel(string $key): string
    {
        return [
            'paid' => 'Paid', 'verifying' => 'Verification pending', 'partial' => 'Advance paid - balance due',
            'due' => 'Payment due', 'cancelled' => 'Cancelled', 'none' => 'Amount not set yet',
        ][$key] ?? ucfirst($key);
    }

    /** Add a confirmed payment to the order; complete the order (and credit coins) once everything is paid. */
    private static function applyPaid(OrderPayment $payment): void
    {
        $order = Order::whereKey($payment->order_id)->lockForUpdate()->first();

        $order->paid_amount = round((float) $order->paid_amount + (float) $payment->amount, 2);
        $order->payment_method = $payment->method;
        if ($payment->kind === 'advance') {
            $order->advance_amount = $payment->amount;
            $order->advance_percent = self::advanceConfig()['percent'];
        }

        if ($order->paid_amount + 0.009 >= self::payableAmount($order)) {
            $order->save();
            self::markPaid($order, $payment->method === 'razorpay' ? $payment->reference : $order->payment_id);
        } else {
            $order->payment_status = 'partial';
            $order->save();
        }
    }
    /**
     * Mark an order as paid and credit the earned reward coins exactly once.
     */
    public static function markPaid(Order $order, ?string $paymentId = null): Order
    {
        return DB::transaction(function () use ($order, $paymentId) {
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            if ($order->payment_status === 'completed') {
                return $order;
            }

            $order->payment_status = 'completed';
            $order->paid_amount = max((float) $order->paid_amount, self::payableAmount($order));
            if ($paymentId) {
                $order->payment_id = $paymentId;
            }

            $earned = (int) ($order->coins_earned ?? 0);
            if ($earned <= 0) {
                $config = self::rewardConfigFor($order);
                $earned = $config ? (int) $config->reward_coins : 0;
                $order->coins_earned = $earned;
            }

            if ($earned > 0) {
                $order->available_coins = $earned;
                $config = self::rewardConfigFor($order);
                if ($config && $config->validity_days) {
                    $order->coins_expires_at = now()->addDays($config->validity_days);
                }

                $user = User::where('uuid', $order->user_uuid)->lockForUpdate()->first();
                if ($user) {
                    $user->reward_coins = (int) ($user->reward_coins ?? 0) + $earned;
                    $user->save();
                }
            }

            // refer-a-friend bonus on the friend's first paid pickup
            $payer = User::where('uuid', $order->user_uuid)->lockForUpdate()->first();
            if ($payer) {
                ReferralService::rewardOnFirstPaidOrder($payer);
            }

            $order->save();

            return $order;
        });
    }

    public static function rewardConfigFor(Order $order): ?RewardConfiguration
    {
        $amount = (float) ($order->total_amount ?? 0);

        return RewardConfiguration::where('status', 1)
            ->where('min_amount', '<=', $amount)
            ->where('max_amount', '>=', $amount)
            ->first();
    }

    public static function coinValue(): float
    {
        $setting = \App\Models\RewardSetting::first();

        return $setting ? (float) $setting->coin_value_in_rupees : 0.10;
    }

    /**
     * Spend up to $coins of the user's coins on the order (FIFO by expiry).
     * Discount can never exceed the order total. Returns [coinsUsed, discount].
     */
    public static function redeemCoins(User $user, Order $order, ?int $requestedCoins = null): array
    {
        return DB::transaction(function () use ($user, $order, $requestedCoins) {
            $user = User::where('uuid', $user->uuid)->lockForUpdate()->first();
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            $coinValue = self::coinValue();
            $remainingValue = max(0, (float) $order->total_amount - (float) $order->discount_applied);
            $maxCoinsForOrder = $coinValue > 0 ? (int) floor($remainingValue / $coinValue) : 0;

            $balance = max((int) ($user->reward_coins ?? 0), 0);
            $coins = min($requestedCoins ?? $balance, $balance, $maxCoinsForOrder);

            if ($coins <= 0) {
                return [0, 0.0];
            }

            $discount = round($coins * $coinValue, 2);

            $order->coins_redeemed = (int) $order->coins_redeemed + $coins;
            $order->discount_applied = round((float) $order->discount_applied + $discount, 2);
            $order->save();

            $remaining = $coins;
            $ledger = Order::where('user_uuid', $user->uuid)
                ->where('available_coins', '>', 0)
                ->where(function ($q) {
                    $q->whereNull('coins_expires_at')->orWhere('coins_expires_at', '>', now());
                })
                ->orderByRaw('coins_expires_at IS NULL, coins_expires_at ASC')
                ->lockForUpdate()
                ->get();

            foreach ($ledger as $row) {
                if ($remaining <= 0) {
                    break;
                }
                $take = min($row->available_coins, $remaining);
                $row->available_coins -= $take;
                $row->save();
                $remaining -= $take;
            }

            $user->reward_coins = $balance - $coins;
            $user->save();

            return [$coins, $discount];
        });
    }
}
