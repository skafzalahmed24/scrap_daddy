<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\OrderPaymentService;
use App\Services\ReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportReferralTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $phone, array $extra = []): User
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

    public function test_support_info_is_public_and_dynamic(): void
    {
        $this->postJson('/api/customer/support-info')
            ->assertJson(['status' => 1])
            ->assertJsonStructure(['data' => ['phone', 'call_number', 'email', 'whatsapp_link', 'address']]);
    }

    public function test_ticket_validation_and_creation(): void
    {
        $user = $this->makeUser('9000000011');
        $token = $user->createToken('t')->plainTextToken;

        $this->withToken($token)->postJson('/api/customer/tickets/create', ['subject' => 'x', 'message' => 'short'])
            ->assertJson(['status' => 0]);

        $this->withToken($token)->postJson('/api/customer/tickets/create', ['subject' => 'Pickup late', 'message' => 'The pickup did not arrive on time.'])
            ->assertStatus(201)->assertJson(['status' => 1]);

        $this->withToken($token)->postJson('/api/customer/tickets')->assertJson(['status' => 1, 'data' => ['count' => 1]]);
    }

    public function test_referral_code_registration_and_reward_on_first_paid_order(): void
    {
        $referrer = $this->makeUser('9000000012');
        $code = ReferralService::codeFor($referrer);

        $this->postJson('/api/customer/register', [
            'full_name' => 'Friend', 'phone_number' => '9000000013', 'password' => 'Secret#123',
            'platform_type' => 1, 'referral_code' => 'NOPE1234',
        ])->assertJson(['status' => 0]);

        $this->postJson('/api/customer/register', [
            'full_name' => 'Friend', 'phone_number' => '9000000013', 'password' => 'Secret#123',
            'platform_type' => 1, 'referral_code' => $code,
        ])->assertJson(['status' => 1]);

        $friend = User::where('phone_number', '9000000013')->first();
        $this->assertSame($referrer->uuid, $friend->referred_by);

        $order = Order::create([
            'user_uuid' => $friend->uuid, 'status' => 'completed', 'pickup_location' => 'x',
            'pickup_date' => now()->toDateString(), 'pickup_time' => '09:00 - 09:30', 'items' => [], 'total_amount' => 100,
        ]);
        OrderPaymentService::markPaid($order, 'pay_1');
        // paying the same order again must not pay the bonus twice
        OrderPaymentService::markPaid($order->fresh(), 'pay_1');

        $this->assertSame(ReferralService::referrerBonus(), (int) $referrer->fresh()->reward_coins);
        $this->assertSame(ReferralService::friendBonus(), (int) $friend->fresh()->reward_coins);
        $this->assertTrue((bool) $friend->fresh()->referral_rewarded);
    }

    public function test_referral_endpoint_returns_code_and_stats(): void
    {
        $user = $this->makeUser('9000000014');
        $token = $user->createToken('t')->plainTextToken;

        $this->withToken($token)->postJson('/api/customer/referral')
            ->assertJson(['status' => 1])
            ->assertJsonStructure(['data' => ['code', 'link', 'share_message', 'friends_joined', 'coins_earned']]);
    }

    public function test_order_api_sends_same_title_image_and_item_pictures_as_website(): void
    {
        $cat = \App\Models\Category::create(["title" => "Paper", "image" => "categories/paper.png", "status" => 1]);
        $sub = \App\Models\Subcategory::create(["category_id" => $cat->uuid, "name" => "Newspaper", "image" => "subcategories/news.png", "status" => 1]);
        $user = $this->makeUser("9000000015");
        $token = $user->createToken("t")->plainTextToken;
        $order = Order::create([
            "user_uuid" => $user->uuid, "category_uuid" => $cat->uuid, "status" => "pending", "pickup_location" => "x",
            "pickup_date" => now()->toDateString(), "pickup_time" => "09:00 - 09:30",
            "items" => [["subcategory_uuid" => $sub->uuid, "name" => "Newspaper", "quantity" => 2]],
        ]);

        $this->withToken($token)->postJson("/api/customer/order-details", ["order_id" => $order->id])
            ->assertJson(["status" => 1, "data" => ["display_title" => "Paper", "display_image" => "subcategories/news.png"]])
            ->assertJsonPath("data.items_with_images.0.image", "subcategories/news.png");
    }
}
