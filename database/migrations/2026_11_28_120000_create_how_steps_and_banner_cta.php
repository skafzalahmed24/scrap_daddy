<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // "How it works" steps: edited in the admin panel, shown on the website and in the app
        Schema::create('how_steps', function (Blueprint $table) {
            $table->id();
            $table->string('title', 80);
            $table->string('description', 300)->nullable();
            $table->string('icon', 40)->default('box-open');   // a Font Awesome name from the admin list
            $table->string('color', 9)->default('#4A9A58');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        $now = now();
        DB::table('how_steps')->insert([
            ['title' => 'Add your scrap', 'description' => 'Pick the items you want to sell and tell us roughly how much you have.', 'icon' => 'sack-dollar', 'color' => '#4A9A58', 'is_active' => true, 'sort' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Schedule a pickup', 'description' => 'Choose a date and a 1-hour time slot that suits you. Our team comes to your door.', 'icon' => 'calendar-check', 'color' => '#0d2b4d', 'is_active' => true, 'sort' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Accurate weighing', 'description' => 'Certified digital scales weigh your scrap in front of you.', 'icon' => 'scale-balanced', 'color' => '#f57c00', 'is_active' => true, 'sort' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Instant payment', 'description' => 'Get paid on the spot through UPI, bank transfer or cash, and earn reward coins.', 'icon' => 'money-bill-transfer', 'color' => '#7b3fe4', 'is_active' => true, 'sort' => 4, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // heading above the steps (editable in Admin > Home Content like the other sections)
        if (!DB::table('home_sections')->where('key', 'how_it_works')->exists()) {
            DB::table('home_sections')->insert([
                'key' => 'how_it_works', 'title' => 'How it works', 'description' => 'Selling your scrap is simple: book a pickup, we weigh it in front of you and pay you instantly.',
                'button_text' => 'Start selling', 'tags' => null, 'is_active' => true, 'sort' => 7, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // main banners can carry a call-to-action button
        Schema::table('banners', function (Blueprint $table) {
            $table->string('cta_text', 40)->nullable()->after('short_description');
            $table->string('cta_link', 500)->nullable()->after('cta_text');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['cta_text', 'cta_link']);
        });
        DB::table('home_sections')->where('key', 'how_it_works')->delete();
        Schema::dropIfExists('how_steps');
    }
};
