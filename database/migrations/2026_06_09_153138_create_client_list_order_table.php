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
        Schema::create('client_list_order', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('client_list_order_client_id')->index('client_list_order_client_id');
            $table->integer('client_list_order_store_id')->index('client_list_order_store_id');
            $table->integer('client_list_order_id')->index('client_list_order_id');
            $table->integer('client_list_order_product_id')->index('client_list_order_product_id');
            $table->decimal('client_list_order_weight', 10, 4);
            $table->dateTime('client_list_order_registration_date');
            $table->decimal('client_list_order_value', 19, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_list_order');
    }
};
