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
        Schema::table('reward_configurations', function (Blueprint $table) {
            $table->integer('validity_days')->nullable()->after('reward_coins')->comment('Number of days before earned coins expire');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reward_configurations', function (Blueprint $table) {
            $table->dropColumn('validity_days');
        });
    }
};
