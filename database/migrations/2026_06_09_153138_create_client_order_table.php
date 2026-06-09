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
        Schema::create('client_order', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('client_order_client_id')->index('client_order_client_id');
            $table->integer('client_order_store_id')->index('client_order_store_id');
            $table->decimal('client_order_value', 19, 4);
            $table->enum('client_order_state', ['Favor', 'Debit', 'Settled'])->default('Settled');
            $table->dateTime('client_order_registration_date');
            $table->dateTime('client_order_date_due');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_order');
    }
};
