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
        Schema::create('client_state', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('client_state_client_id')->index('client_state_client_id');
            $table->integer('client_state_store_id')->index('client_state_store_id');
            $table->decimal('client_state_amount', 19, 4);
            $table->enum('client_state_state', ['Favor', 'Debit', 'Settled'])->default('Settled');
            $table->dateTime('client_state_last_transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_state');
    }
};
