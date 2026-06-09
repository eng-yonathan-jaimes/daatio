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
        Schema::table('client_list_order', function (Blueprint $table) {
            $table->foreign(['client_list_order_client_id'], 'client_list_order_ibfk_1')->references(['id'])->on('client')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['client_list_order_store_id'], 'client_list_order_ibfk_2')->references(['id'])->on('store')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['client_list_order_id'], 'client_list_order_ibfk_3')->references(['id'])->on('client_order')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['client_list_order_product_id'], 'client_list_order_ibfk_4')->references(['id'])->on('product')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_list_order', function (Blueprint $table) {
            $table->dropForeign('client_list_order_ibfk_1');
            $table->dropForeign('client_list_order_ibfk_2');
            $table->dropForeign('client_list_order_ibfk_3');
            $table->dropForeign('client_list_order_ibfk_4');
        });
    }
};
