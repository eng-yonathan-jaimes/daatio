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
        Schema::table('user_subscription', function (Blueprint $table) {
            $table->foreign(['user_subscription_user_id'], 'user_subscription_ibfk_1')->references(['id'])->on('user')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['user_subscription_subscription_id'], 'user_subscription_ibfk_2')->references(['id'])->on('subscription')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscription', function (Blueprint $table) {
            $table->dropForeign('user_subscription_ibfk_1');
            $table->dropForeign('user_subscription_ibfk_2');
        });
    }
};
