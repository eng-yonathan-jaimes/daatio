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
        Schema::table('subscription_payment', function (Blueprint $table) {
            $table->foreign(['subscription_payment_user_subscription_id'], 'subscription_payment_ibfk_1')->references(['id'])->on('user_subscription')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_payment', function (Blueprint $table) {
            $table->dropForeign('subscription_payment_ibfk_1');
        });
    }
};
