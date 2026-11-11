<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\RewardSetting;
use App\Models\Subcategory;
use App\Models\User;
use App\Services\OrderPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerFlowTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $phone = '9000000001', array $extra = []): User
    {
        return User::create(array_merge([
            'full_name' => 'Test User',
            'phone_number' => $phone,
            'password' => 'secret123',
            'platform_type' => 1,
            'status' => 1,
            'is_verified' => true,
        ], $extra));
    }

    public function test_reset_password_requires_otp_proof(): void
    {
        $user = $this->makeUser();

        $this->postJson('/api/customer/reset-password', [
            'phone_number' => $user->phone_number,
            'password' => 'NewSecret#1',
            'password_confirmation' => 'NewSecret#1',
        ])->assertJson(['status' => 0]);

        $this->assertTrue(\Hash::check('secret123', $user->fresh()->password));
    }

    public function test_reset_password_with_otp_from_web(): void
    {
        $user = $this->makeUser();
        $this->postJson('/api/customer/forgot-password', ['phone_number' => $user->phone_number])->assertJson(['status' => 1]);

        $this->postJson('/api/customer/reset-password', [
            'phone_number' => $user->phone_number,
            'otp' => '123456',
            'password' => 'NewSecret#1',
            'password_confirmation' => 'NewSecret#1',
        ])->assertJson(['status' => 1]);

        $this->assertTrue(\Hash::check('NewSecret#1', $user->fresh()->password));
    }

    public function test_reset_password_after_verify_otp_mobile_flow(): void
    {
        $user = $this->makeUser();
        $this->postJson('/api/customer/forgot-password', ['phone_number' => $user->phone_number]);
        $this->postJson('/api/customer/verify-otp', ['login' => $user->phone_number, 'otp' => '123456'])->assertJson(['status' => 1]);

        $payload = ['login' => $user->phone_number, 'password' => 'NewSecret#1', 'password_confirmation' => 'NewSecret#1'];
        $this->postJson('/api/customer/reset-password', $payload)->assertJson(['status' => 1]);
        // flag is single-use
        $this->postJson('/api/customer/reset-password', $payload)->assertJson(['status' => 0]);
    }

    public function test_otp_is_not_leaked_in_api_response(): void
    {
        $res = $this->postJson('/api/customer/register', [
            'full_name' => 'A', 'phone_number' => '9111111111', 'password' => 'Secret#123', 'platform_type' => 2,
        ])->assertStatus(201);

        $this->assertArrayNotHasKey('otp', $res->json('data.user'));
    }

    public function test_customer_cannot_see_or_cancel_other_users_order(): void
    {
        $a = $this->makeUser('9000000001');
        $b = $this->makeUser('9000000002');
        $order = Order::create(['user_uuid' => $b->uuid, 'status' => 'pending', 'items' => [], 'pickup_location' => 'x']);

        $this->actingAs($a)->get('/customer/order/' . $order->id)->assertStatus(404);
        $this->actingAs($a)->post('/customer/orders/' . $order->id . '/cancel')->assertStatus(404);
        $this->actingAs($a)->get('/customer/payment/' . $order->id)->assertStatus(404);
        $this->assertEquals('pending', $order->fresh()->status);
    }

    public function test_redeem_is_capped_and_payment_requires_gateway_verification(): void
    {
        RewardSetting::create(['coin_value_in_rupees' => 1]);
        $user = $this->makeUser('9000000003', ['reward_coins' => 500]);
        $order = Order::create([
            'user_uuid' => $user->uuid, 'status' => 'completed', 'items' => [], 'total_amount' => 100,
            'payment_status' => 'pending', 'pickup_location' => 'x',
        ]);
        $token = $user->createToken('t', ['access'])->plainTextToken;

        $res = $this->withToken($token)->postJson('/api/customer/orders/redeem-coins', ['order_id' => $order->id, 'coins' => 500])
            ->assertJson(['status' => 1]);

        $this->assertEquals(100, $order->fresh()->discount_applied);   // never more than the bill
        $this->assertEquals(400, $user->fresh()->reward_coins);        // only 100 coins used
        $this->assertEquals(0.0, OrderPaymentService::payableAmount($order->fresh()));

        // Fake payment id must not mark an order paid (gateway unreachable/invalid in tests)
        $order2 = Order::create([
            'user_uuid' => $user->uuid, 'status' => 'completed', 'items' => [], 'total_amount' => 50,
            'payment_status' => 'pending', 'pickup_location' => 'x',
        ]);
        $this->withToken($token)->postJson('/api/customer/orders/verify-payment', [
            'order_id' => $order2->id, 'razorpay_payment_id' => 'pay_fake',
        ])->assertJson(['status' => 0]);
        $this->assertEquals('pending', $order2->fresh()->payment_status);
    }

    public function test_home_does_not_expose_pickups_without_token(): void
    {
        $user = $this->makeUser('9000000004');
        Order::create(['user_uuid' => $user->uuid, 'status' => 'pending', 'items' => [], 'pickup_location' => 'x']);

        $this->postJson('/api/customer/home', ['user_id' => $user->uuid])
            ->assertJson(['data' => ['live_pickups' => []]]);
    }

    public function test_weak_password_is_rejected_on_register(): void
    {
        $this->postJson('/api/customer/register', [
            'full_name' => 'A', 'phone_number' => '9222222222', 'password' => 'password1', 'platform_type' => 2,
        ])->assertJson(['status' => 0]);
    }

    public function test_profile_update_validates_and_saves_email(): void
    {
        $user = $this->makeUser('9000000005');
        $token = $user->createToken('t', ['access'])->plainTextToken;

        $this->withToken($token)->postJson('/api/customer/update-profile', ['phone_number' => 'abc'])->assertJson(['status' => 0]);
        $this->withToken($token)->postJson('/api/customer/update-profile', ['pin_code' => '12'])->assertJson(['status' => 0]);
        $this->withToken($token)->postJson('/api/customer/update-profile', ['email' => 'a@b.com', 'pin_code' => '560001'])->assertJson(['status' => 1]);
        $this->assertEquals('a@b.com', $user->fresh()->email);
    }

    public function test_scrap_vehicle_validation(): void
    {
        $user = $this->makeUser('9000000006');
        $token = $user->createToken('t', ['access'])->plainTextToken;

        $this->withToken($token)->postJson('/api/customer/scrap-vehicles', ['vehicle_type' => 'Spaceship', 'vehicle_number' => 'MP04 DK 9834'])->assertJson(['status' => 0]);
        $this->withToken($token)->postJson('/api/customer/scrap-vehicles', ['vehicle_type' => 'Car', 'vehicle_number' => '@@'])->assertJson(['status' => 0]);
        $this->withToken($token)->postJson('/api/customer/scrap-vehicles', ['vehicle_type' => 'Car', 'vehicle_number' => 'MP04 DK 9834'])->assertJson(['status' => 1]);
    }

    private function orderPayload(string $subUuid, ?string $pin): array
    {
        return array_filter([
            'items' => [['subcategory_uuid' => $subUuid, 'quantity' => 1, 'name' => 'Paper']],
            'pickup_location' => 'Door 1, Main Street',
            'pin_code' => $pin,
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time' => '10:00 AM',
        ], fn ($v) => $v !== null);
    }

    public function test_pincode_service_area_is_enforced_on_orders(): void
    {
        $cat = Category::create(['title' => 'C', 'status' => 1]);
        $sub = Subcategory::create(['name' => 'S', 'category_id' => $cat->uuid, 'status' => 1]);
        $user = $this->makeUser('9000000007');
        $token = $user->createToken('t', ['access'])->plainTextToken;

        // Nothing configured yet -> open everywhere
        $this->postJson('/api/customer/check-pincode', ['pin_code' => '560001'])->assertJson(['data' => ['serviceable' => true]]);

        \App\Models\ServicePincode::create(['pin_code' => '560001', 'status' => true]);
        \App\Models\ServicePincode::create(['pin_code' => '560002', 'status' => false]);

        $this->postJson('/api/customer/check-pincode', ['pin_code' => '560001'])->assertJson(['data' => ['serviceable' => true]]);
        $this->postJson('/api/customer/check-pincode', ['pin_code' => '560002'])->assertJson(['data' => ['serviceable' => false]]);
        $this->postJson('/api/customer/check-pincode', ['pin_code' => '999999'])->assertJson(['data' => ['serviceable' => false]]);
        $this->postJson('/api/customer/check-pincode', ['pin_code' => '12'])->assertJson(['status' => 0]);

        $this->withToken($token)->postJson('/api/customer/orders/create', $this->orderPayload($sub->uuid, '999999'))->assertJson(['status' => 0]);
        $this->withToken($token)->postJson('/api/customer/orders/create', $this->orderPayload($sub->uuid, '560002'))->assertJson(['status' => 0]);
        $this->withToken($token)->postJson('/api/customer/orders/create', $this->orderPayload($sub->uuid, null))->assertJson(['status' => 0]);
        $this->assertEquals(0, Order::count());

        $this->withToken($token)->postJson('/api/customer/orders/create', $this->orderPayload($sub->uuid, '560001'))->assertStatus(201);
        $this->assertEquals('560001', Order::first()->pin_code);

        // Home reports availability for the detected PIN
        $this->postJson('/api/customer/home', ['pin_code' => '560001'])->assertJson(['data' => ['service_available' => true]]);
        $this->postJson('/api/customer/home', ['pin_code' => '999999'])->assertJson(['data' => ['service_available' => false]]);
    }
}
