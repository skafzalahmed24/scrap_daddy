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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('full_name');
            $table->string('email')->unique()->nullable();
            $table->string('phone_number')->unique();
            $table->string('password');
            $table->string('pin_code')->nullable();
            $table->string('location')->nullable();
            $table->string('device_id')->nullable();
            $table->string('device_unique_id')->nullable();
            $table->text('device_details')->nullable();
            $table->integer('platform_type')->comment('1 for web, 2 for android, 3 for ios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
