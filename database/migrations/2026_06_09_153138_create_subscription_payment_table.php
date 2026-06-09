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
        Schema::create('subscription_payment', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('subscription_payment_user_subscription_id')->index('subscription_payment_user_subscription_id');
            $table->decimal('subscription_payment_amount', 19, 4);
            $table->dateTime('subscription_payment_date')->useCurrent();
            $table->string('subscription_payment_method');
            $table->enum('subscription_payment_status', ['Completed', 'Pending', 'Failed', 'Refunded'])->default('Pending');
            $table->string('subscription_payment_reference')->nullable();
            $table->dateTime('subscription_payment_period_start');
            $table->dateTime('subscription_payment_period_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_payment');
    }
};
