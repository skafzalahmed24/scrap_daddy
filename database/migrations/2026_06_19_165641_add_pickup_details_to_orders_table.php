<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignUuid('subcategory_uuid')->nullable()->constrained('subcategories', 'uuid')->onDelete('set null');
            $table->text('pickup_location')->nullable();
            $table->date('pickup_date')->nullable();
            $table->string('pickup_time')->nullable();
            $table->json('images')->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['subcategory_uuid']);
            $table->dropColumn(['subcategory_uuid', 'pickup_location', 'pickup_date', 'pickup_time', 'images', 'notes']);
        });
    }
};
