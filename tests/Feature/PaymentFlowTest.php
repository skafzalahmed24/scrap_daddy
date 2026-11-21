<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\OrderPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    private function setting(array $values): void
    {
        foreach ($values as $k => $v) {
            SiteSetting::updateOrCreate(['key' => $k], ['value' => (string) $v]);
        }
        SiteSetting::flush();
    }

    private function customerAndOrder(float $amount = 1000, string $status = 'accepted'): array
    {
        $user = User::create(['full_name' => 'Pay User', 'phone_number' => '9000000077', 'password' => 'secret123', 'platform_type' => 1, 'status' => 1, 'is_verified' => true]);
        $order = Order::create([
            'user_uuid' => $user->uuid, 'status' => $status, 'pickup_location' => 'Kakinada', 'pickup_date' => now()->toDateString(),
            'pickup_time' => '09:00 - 09:30', 'items' => [], 'total_amount' => $amount, 'payment_status' => 'pending',
        ]);

        return [$user, $order, $user->createToken('t')->plainTextToken];
    }

    public function test_advance_percentage_flow_from_advance_to_balance(): void
    {
        $this->setting(['advance_enabled' => 1, 'advance_percent' => 20, 'pay_upi' => 1, 'upi_id' => 'scrap@okbank', 'pay_cash' => 1]);
        [$user, $order, $token] = $this->customerAndOrder(1000);

        $due = OrderPaymentService::dueNow($order);
        $this->assertSame('advance', $due['kind']);
        $this->assertEquals(200.0, $due['amount']);
        $this->assertEquals(800.0, $due['balance_after']);

        // customer pays the advance by UPI and submits the UTR -> waits for admin
        $this->withToken($token)->postJson('/api/customer/orders/pay-direct', ['order_id' => $order->id, 'method' => 'upi', 'reference' => 'UTR123456789'])
            ->assertStatus(201)->assertJson(['status' => 1, 'data' => ['status' => 'verifying']]);
        $this->assertEquals(0.0, (float) $order->fresh()->paid_amount);

        // a second submission while the first is pending is refused
        $this->withToken($token)->postJson('/api/customer/orders/pay-direct', ['order_id' => $order->id, 'method' => 'upi', 'reference' => 'UTR987654321'])
            ->assertJson(['status' => 0]);

        // admin approves -> advance counted, order is "partial", balance is now due
        $this->withSession(['is_admin' => true])->post('/admin/order-payments/' . OrderPayment::first()->id . '/review', ['action' => 'approve'])->assertRedirect();
        $order->refresh();
        $this->assertEquals(200.0, (float) $order->paid_amount);
        $this->assertSame('partial', $order->payment_status);
        $this->assertEquals(200.0, (float) $order->advance_amount);

        $due = OrderPaymentService::dueNow($order);
        $this->assertSame('balance', $due['kind']);
        $this->assertEquals(800.0, $due['amount']);

        // admin records the balance in cash -> order fully paid
        $this->withSession(['is_admin' => true])->post('/admin/orders/' . $order->id . '/record-payment', ['method' => 'cash', 'amount' => 800])->assertRedirect();
        $order->refresh();
        $this->assertSame('completed', $order->payment_status);
        $this->assertEquals(1000.0, (float) $order->paid_amount);
        $this->assertSame('paid', $order->payment_info['status']);
    }

    public function test_without_advance_the_full_amount_is_due(): void
    {
        $this->setting(['advance_enabled' => 0]);
        [, $order] = $this->customerAndOrder(500);

        $due = OrderPaymentService::dueNow($order);
        $this->assertSame('full', $due['kind']);
        $this->assertEquals(500.0, $due['amount']);
    }

    public function test_direct_payment_rules(): void
    {
        config(['services.razorpay.key' => null]); // no gateway keys configured
        $this->setting(['pay_upi' => 1, 'upi_id' => 'scrap@okbank', 'pay_bank' => 0, 'pay_razorpay' => 1]);
        [, $order, $token] = $this->customerAndOrder(300);

        // UPI needs a transaction number
        $this->withToken($token)->postJson('/api/customer/orders/pay-direct', ['order_id' => $order->id, 'method' => 'upi'])->assertJson(['status' => 0]);
        // disabled methods are refused
        $this->withToken($token)->postJson('/api/customer/orders/pay-direct', ['order_id' => $order->id, 'method' => 'bank', 'reference' => 'UTR123456'])
            ->assertJson(['status' => 0, 'message' => 'This payment method is not available.']);
        // options list only shows what the admin enabled (Razorpay has no keys in tests)
        $keys = collect($this->withToken($token)->postJson('/api/customer/orders/payment-options', ['order_id' => $order->id])->json('data.methods'))->pluck('key')->all();
        $this->assertSame(['upi'], $keys);
        // no Razorpay keys -> online payment cannot start, with a clear message
        $this->withToken($token)->postJson('/api/customer/orders/gateway-order', ['order_id' => $order->id])->assertJson(['status' => 0]);
    }

    public function test_cannot_pay_someone_elses_order(): void
    {
        $this->setting(['pay_cash' => 1]);
        [, $order] = $this->customerAndOrder(300);
        $other = User::create(['full_name' => 'Other', 'phone_number' => '9000000078', 'password' => 'secret123', 'platform_type' => 1, 'status' => 1, 'is_verified' => true]);
        $tok = $other->createToken('t')->plainTextToken;

        $this->withToken($tok)->postJson('/api/customer/orders/pay-direct', ['order_id' => $order->id, 'method' => 'cash'])->assertStatus(404);
    }

    public function test_payment_settings_page_saves_and_site_settings_do_not_wipe_them(): void
    {
        $this->withSession(['is_admin' => true])->get('/admin/payment-settings')->assertOk();

        $this->withSession(['is_admin' => true])->post('/admin/payment-settings', [
            'advance_enabled' => '1', 'advance_percent' => 25, 'pay_razorpay' => '1', 'pay_upi' => '1', 'upi_id' => 'shop@upi', 'pay_cash' => '1',
        ])->assertRedirect()->assertSessionHasNoErrors();

        SiteSetting::flush();
        $this->assertSame('1', SiteSetting::get('advance_enabled'));
        $this->assertSame('25', SiteSetting::get('advance_percent'));
        $this->assertSame('shop@upi', SiteSetting::get('upi_id'));

        // UPI switched on without an ID is rejected
        $this->withSession(['is_admin' => true])->post('/admin/payment-settings', ['advance_percent' => 20, 'pay_upi' => '1', 'upi_id' => ''])->assertSessionHasErrors('upi_id');

        // saving the general Site Settings form must not erase the payment settings
        $this->withSession(['is_admin' => true])->post('/admin/settings', [
            'company_name' => 'Scrap Daddy', 'address_line1' => 'x', 'city' => 'c', 'state' => 's', 'country' => 'India', 'pincode' => '533001',
            'email' => 'a@b.com', 'phone' => '9876543210', 'referral_bonus_coins' => 10, 'referral_friend_coins' => 5,
        ])->assertRedirect();
        SiteSetting::flush();
        $this->assertSame('shop@upi', SiteSetting::get('upi_id'));
        $this->assertSame('1', SiteSetting::get('advance_enabled'));
    }

    public function test_admin_order_and_vehicle_detail_pages_render(): void
    {
        [$user, $order] = $this->customerAndOrder(400);
        $vehicle = \App\Models\ScrapVehicle::create(['user_uuid' => $user->uuid, 'vehicle_type' => 'Car', 'vehicle_number' => 'AP05 AB 1234', 'vehicle_model' => 'i10', 'status' => 'pending']);

        $this->withSession(['is_admin' => true])->get('/admin/orders/' . $order->id)->assertOk()->assertSee('Payment');
        $this->withSession(['is_admin' => true])->get('/admin/scrap-vehicles/' . $vehicle->id)->assertOk()->assertSee('AP05 AB 1234');
    }

    public function test_paid_order_is_locked_and_shows_razorpay_with_fee(): void
    {
        [$user, $order] = $this->customerAndOrder(1000, 'completed');
        $order->update(['payment_status' => 'completed', 'payment_id' => 'pay_ABC123xyz', 'paid_amount' => 0]); // legacy: paid before itemised payments

        $this->assertSame(0.0, OrderPaymentService::remaining($order->fresh()));

        $this->withSession(['is_admin' => true])->get('/admin/orders/' . $order->id)->assertOk()
            ->assertSee('Paid via')->assertSee('Razorpay')->assertSee('pay_ABC123xyz')
            ->assertSee('Gateway fee (2%)')->assertSee('-₹20.00', false)
            ->assertSee('Completed and paid in full')
            ->assertDontSee('Record a payment received');
    }
}
