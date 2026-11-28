<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Door / flat number, floor and landmark: the pickup team needs them to find the exact place
        Schema::table('orders', function (Blueprint $table) {
            $table->string('door_no', 40)->nullable()->after('pickup_location');
            $table->string('floor', 20)->nullable()->after('door_no');
            $table->string('landmark', 100)->nullable()->after('floor');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['door_no', 'floor', 'landmark']);
        });
    }
};
