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
        Schema::create('user_subscription', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_subscription_user_id')->index('user_subscription_user_id');
            $table->integer('user_subscription_subscription_id')->index('user_subscription_subscription_id');
            $table->dateTime('user_subscription_start_date')->useCurrent();
            $table->dateTime('user_subscription_end_date');
            $table->decimal('user_subscription_value', 19, 4);
            $table->enum('user_subscription_status', ['Active', 'Cancelled', 'Expired', 'Trial', 'Past Due', 'Pending'])->default('Pending');
            $table->dateTime('user_subscription_trial_ends_date')->nullable();
            $table->dateTime('user_subscription_cancelled_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscription');
    }
};
