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
            $table->integer('available_coins')->default(0)->after('coins_redeemed')->comment('Current balance of coins from this order');
            $table->timestamp('coins_expires_at')->nullable()->after('available_coins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['available_coins', 'coins_expires_at']);
        });
    }
};
