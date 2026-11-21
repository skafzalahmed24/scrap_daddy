<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ScrapVehicle;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    private function seedData(): void
    {
        $user = User::create([
            'full_name' => 'Asha Rao', 'phone_number' => '9000000099', 'password' => 'secret123',
            'platform_type' => 1, 'status' => 1, 'is_verified' => true,
        ]);
        $paid = Order::create([
            'user_uuid' => $user->uuid, 'status' => 'completed', 'pickup_location' => 'Kakinada, 533001', 'pin_code' => '533001',
            'pickup_date' => now()->toDateString(), 'pickup_time' => '09:00 - 09:30',
            'items' => [['subcategory_uuid' => 'x', 'name' => 'Newspaper', 'quantity' => 2]],
            'total_amount' => 250, 'payment_status' => 'completed', 'discount_applied' => 20,
        ]);
        Order::create([
            'user_uuid' => $user->uuid, 'status' => 'completed', 'pickup_location' => 'Kakinada', 'pickup_date' => now()->toDateString(),
            'pickup_time' => '10:00 - 10:30', 'items' => [], 'total_amount' => 100, 'payment_status' => 'pending',
        ]);
        Order::create([
            'user_uuid' => $user->uuid, 'status' => 'pending', 'pickup_location' => 'Kakinada', 'pickup_date' => now()->toDateString(),
            'pickup_time' => '11:00 - 11:30', 'items' => [],
        ]);
        ScrapVehicle::create(['user_uuid' => $user->uuid, 'vehicle_type' => 'Bike', 'vehicle_number' => 'MP04 DK 9834', 'vehicle_model' => 'Splendor', 'status' => 'pending']);
        SupportTicket::create(['user_uuid' => $user->uuid, 'subject' => 'Late pickup', 'message' => 'Pickup did not arrive on time.']);
    }

    public function test_admin_pages_render_with_data(): void
    {
        $this->seedData();

        foreach (['/dashboard', '/admin/orders', '/admin/orders?status=pending&search=Asha', '/admin/scrap-vehicles', '/admin/scrap-vehicles?status=pending',
                     '/admin/payments', '/admin/payments?status=paid', '/admin/payments?status=pending', '/admin/tickets'] as $url) {
            $this->withSession(['is_admin' => true])->get($url)->assertOk();
        }
    }

    public function test_admin_pages_render_when_empty(): void
    {
        foreach (['/dashboard', '/admin/orders', '/admin/scrap-vehicles', '/admin/payments'] as $url) {
            $this->withSession(['is_admin' => true])->get($url)->assertOk();
        }
    }

    public function test_admin_pages_require_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/admin/login');
        $this->get('/admin/orders')->assertRedirect('/admin/login');
    }
}
