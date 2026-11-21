<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('paid_amount', 10, 2)->default(0)->after('discount_applied');      // everything received so far
            $table->decimal('advance_amount', 10, 2)->default(0)->after('paid_amount');        // advance that was required
            $table->decimal('advance_percent', 5, 2)->nullable()->after('advance_amount');
            $table->string('payment_method', 20)->nullable()->after('advance_percent');       // last method used
        });

        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->uuid('user_uuid')->index();
            $table->string('kind', 10);                      // advance | balance | full
            $table->decimal('amount', 10, 2);
            $table->string('method', 20);                    // razorpay | upi | bank | cash
            $table->string('reference', 120)->nullable();    // Razorpay payment id / UTR / receipt no.
            $table->string('gateway_order_id', 100)->nullable()->index();
            $table->string('status', 12)->default('pending'); // pending | paid | rejected
            $table->string('note', 255)->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'advance_amount', 'advance_percent', 'payment_method']);
        });
    }
};
