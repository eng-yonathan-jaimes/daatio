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
        Schema::create('transaction', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('transaction_client_id')->index('transaction_client_id');
            $table->integer('transaction_store_id')->index('transaction_store_id');
            $table->integer('transaction_client_order_id')->nullable()->index('transaction_ibfk_3');
            $table->enum('transaction_transaction', ['Selling', 'Buying', 'Paying', 'Retriving', 'Settle']);
            $table->decimal('transaction_amount', 19, 4);
            $table->enum('transaction_state', ['Favor', 'Debit', 'Settled']);
            $table->dateTime('transaction_registration_date');
            $table->string('transaction_description', 500)->nullable();
            $table->integer('transaction_user_id')->nullable()->index('transaction_ibfk_4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
