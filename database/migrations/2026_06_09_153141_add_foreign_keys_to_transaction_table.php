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
        Schema::table('transaction', function (Blueprint $table) {
            $table->foreign(['transaction_client_id'], 'transaction_ibfk_1')->references(['id'])->on('client')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['transaction_store_id'], 'transaction_ibfk_2')->references(['id'])->on('store')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['transaction_client_order_id'], 'transaction_ibfk_3')->references(['id'])->on('client_order')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['transaction_user_id'], 'transaction_ibfk_4')->references(['id'])->on('user')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->dropForeign('transaction_ibfk_1');
            $table->dropForeign('transaction_ibfk_2');
            $table->dropForeign('transaction_ibfk_3');
            $table->dropForeign('transaction_ibfk_4');
        });
    }
};
