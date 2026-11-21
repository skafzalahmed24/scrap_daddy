<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 12)->nullable()->unique()->after('reward_coins');
            $table->uuid('referred_by')->nullable()->after('referral_code');
            $table->boolean('referral_rewarded')->default(false)->after('referred_by');
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid');
            $table->string('subject', 150);
            $table->text('message');
            $table->string('status', 20)->default('open'); // open | in_progress | closed
            $table->text('admin_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
            $table->index('user_uuid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['referral_code']);
            $table->dropColumn(['referral_code', 'referred_by', 'referral_rewarded']);
        });
    }
};
