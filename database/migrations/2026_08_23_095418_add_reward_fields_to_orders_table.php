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
            $table->integer('coins_earned')->default(0)->after('payment_id');
            $table->integer('coins_redeemed')->default(0)->after('coins_earned');
            $table->decimal('discount_applied', 10, 2)->default(0)->after('coins_redeemed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['coins_earned', 'coins_redeemed', 'discount_applied']);
        });
    }
};
