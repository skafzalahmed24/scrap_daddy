<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_pincodes', function (Blueprint $table) {
            $table->id();
            $table->string('pin_code', 10)->unique();
            $table->string('area')->nullable();
            $table->string('city')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('pin_code', 10)->nullable()->after('pickup_location');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('pin_code');
        });
        Schema::dropIfExists('service_pincodes');
    }
};
