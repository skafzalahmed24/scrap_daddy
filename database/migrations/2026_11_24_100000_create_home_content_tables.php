<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Editable text blocks of the Home screen (website + app): title, description, tags, button
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key', 40)->unique();      // trending | bulk_enquiry | sell_bulk | scrap_vehicle | offers | know_scrap
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('button_text', 60)->nullable();
            $table->json('tags')->nullable();          // hashtags / chips
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // Which sub-categories appear in "Trending" and in which order
        Schema::table('subcategories', function (Blueprint $table) {
            $table->boolean('is_trending')->default(false)->after('status');
            $table->unsignedInteger('trending_order')->default(0)->after('is_trending');
        });

        // Bulk enquiry form: the admin decides the extra fields (dropdowns etc.)
        Schema::create('bulk_enquiry_fields', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('type', 20)->default('text');   // text | textarea | number | phone | email | dropdown
            $table->json('options')->nullable();            // dropdown choices
            $table->boolean('required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('bulk_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('user_uuid')->nullable();
            $table->string('name');
            $table->string('phone', 20);
            $table->json('answers')->nullable();           // [{label, value}] snapshot of the dynamic fields
            $table->string('status', 20)->default('new');  // new | contacted | closed
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('home_sections')->insert([
            ['key' => 'trending', 'title' => 'Trending scrap', 'description' => 'Popular items people sell most. Add quantity and book a pickup in a tap.', 'button_text' => 'Continue', 'tags' => null, 'is_active' => true, 'sort' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'bulk_enquiry', 'title' => 'Got scrap in bulk?', 'description' => 'Selling 500+ kg? Tell us what you have and our team will get back with the best rate.', 'button_text' => 'Start enquiry', 'tags' => null, 'is_active' => true, 'sort' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'sell_bulk', 'title' => 'Sell bulk scrap', 'description' => 'Got 500+ kg of scrap? List it and get better rates.', 'button_text' => 'Sell now', 'tags' => json_encode(['Best rates', 'Compliance', 'Transparency']), 'is_active' => true, 'sort' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'scrap_vehicle', 'title' => 'Want to scrap your vehicle?', 'description' => 'Scrap any vehicle, from 2 wheelers to heavy vehicles, quickly and with full paperwork support.', 'button_text' => 'Scrap your vehicle', 'tags' => json_encode(['Best rates', 'Compliance', 'Transparency']), 'is_active' => true, 'sort' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'offers', 'title' => 'Offers for you', 'description' => null, 'button_text' => null, 'tags' => null, 'is_active' => true, 'sort' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'know_scrap', 'title' => 'Know your scrap', 'description' => 'Learn what each type of scrap is and how it gets recycled.', 'button_text' => 'Read more', 'tags' => null, 'is_active' => true, 'sort' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('bulk_enquiry_fields')->insert([
            ['label' => 'Type of scrap', 'type' => 'dropdown', 'options' => json_encode(['Paper', 'Plastic', 'Metal', 'E-waste', 'Other']), 'required' => true, 'is_active' => true, 'sort' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Approximate quantity (kg)', 'type' => 'number', 'options' => null, 'required' => true, 'is_active' => true, 'sort' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'City / area', 'type' => 'text', 'options' => null, 'required' => false, 'is_active' => true, 'sort' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_enquiries');
        Schema::dropIfExists('bulk_enquiry_fields');
        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropColumn(['is_trending', 'trending_order']);
        });
        Schema::dropIfExists('home_sections');
    }
};
