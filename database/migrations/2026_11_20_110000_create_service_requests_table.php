<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('user_uuid')->nullable();
            $table->string('name', 120);
            $table->string('phone', 20);
            $table->string('email', 150)->nullable();
            $table->string('pin_code', 10);
            $table->string('area', 150)->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('new'); // new | contacted | closed
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
