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
        Schema::create('product', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('product_store_id')->index('product_store_id');
            $table->string('product_name');
            $table->integer('product_type');
            $table->decimal('product_weight', 10, 4);
            $table->decimal('product_value', 19, 4);
            $table->string('product_currency', 3)->default('USD');
            $table->enum('product_state', ['In Stock', 'Out of Stock'])->default('Out of Stock');
            $table->dateTime('product_registration_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
