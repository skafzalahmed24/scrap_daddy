<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Payment endpoints shared by the mobile app (and used by the website through the same service):
 * what is due (advance / balance), which methods the admin enabled, online checkout order, direct payments.
 */
class PaymentApiController extends Controller
{
    private function fail(string $message, array $extra = [], int $code = 200)
    {
        return response()->json(['status' => 0, 'message' => $message] + $extra, $code);
    }

    private function ownedOrder(Request $request): Order|\Illuminate\Http\JsonResponse
    {
        $v = Validator::make($request->all(), ['order_id' => 'required|integer']);
        if ($v->fails()) {
            return $this->fail('Validation error', ['data' => ['errors' => $v->errors()]]);
        }

        $order = Order::where('user_uuid', $request->user()->uuid)->where('id', $request->order_id)->first();

        return $order ?: $this->fail('Order not found', [], 404);
    }

    public function options(Request $request)
    {
        $order = $this->ownedOrder($request);
        if (!($order instanceof Order)) {
            return $order;
        }

        return response()->json(['status' => 1, 'message' => 'Payment options', 'data' => OrderPaymentService::summary($order)]);
    }

    /** Server-created Razorpay order: key, order id and amount all come from the server (no key mismatch / wrong amount). */
    public function gatewayOrder(Request $request)
    {
        $order = $this->ownedOrder($request);
        if (!($order instanceof Order)) {
            return $order;
        }

        if (!collect(OrderPaymentService::methods())->contains('key', 'razorpay')) {
            return $this->fail('Online payment is not available right now. Please choose another method.');
        }
        if (OrderPaymentService::dueNow($order)['amount'] <= 0) {
            return $this->fail('Nothing is due for this order.');
        }

        try {
            $data = OrderPaymentService::createGatewayOrder($order);
        } catch (\Throwable $e) {
            report($e);

            return $this->fail('Unable to start the online payment. Please try again or use another method.');
        }

        return response()->json(['status' => 1, 'message' => 'Gateway order created', 'data' => $data]);
    }

    /** UPI / bank (customer enters the UTR) or cash (customer promises cash): recorded as pending until the admin confirms. */
    public function payDirect(Request $request)
    {
        $order = $this->ownedOrder($request);
        if (!($order instanceof Order)) {
            return $order;
        }

        $v = Validator::make($request->all(), [
            'method' => 'required|in:upi,bank,cash',
            'reference' => ['nullable', 'string', 'max:40', 'regex:/^[A-Za-z0-9\-_\/]{6,40}$/'],
            'note' => 'nullable|string|max:200',
        ], ['reference.regex' => 'Enter the transaction / UTR number (6-40 letters or digits).']);
        if ($v->fails()) {
            return $this->fail('Validation error', ['data' => ['errors' => $v->errors()]]);
        }

        $method = $request->method;
        if (!collect(OrderPaymentService::methods())->contains('key', $method)) {
            return $this->fail('This payment method is not available.');
        }
        if (in_array($method, ['upi', 'bank'], true) && !$request->filled('reference')) {
            return $this->fail('Enter the transaction / UTR number after paying.', ['data' => ['errors' => ['reference' => ['Required']]]]);
        }

        $due = OrderPaymentService::dueNow($order);
        if ($due['amount'] <= 0) {
            return $this->fail('Nothing is due for this order.');
        }
        if ($order->payments()->where('status', 'pending')->where('method', '!=', 'razorpay')->exists()) {
            return $this->fail('Your previous payment is still being verified.');
        }
        if ($request->filled('reference') && \App\Models\OrderPayment::where('reference', $request->reference)->whereIn('status', ['pending', 'paid'])->exists()) {
            return $this->fail('This transaction number was already submitted.');
        }

        OrderPaymentService::recordPayment(
            $order, $due['kind'], $due['amount'], $method, $request->reference, 'pending', null, $request->note
        );

        return response()->json([
            'status' => 1,
            'message' => $method === 'cash'
                ? 'Request received. Our executive will collect the cash and confirm.'
                : 'Payment submitted. We will verify it shortly.',
            'data' => OrderPaymentService::summary($order->fresh()),
        ], 201);
    }
}
