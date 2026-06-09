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
        Schema::create('subscription', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('subscription_type');
            $table->text('subscription_description')->nullable();
            $table->decimal('subscription_value', 19, 4);
            $table->string('subscription_period');
            $table->integer('subscription_days');
            $table->integer('subscription_max_stores')->default(1);
            $table->dateTime('subscription_creation_date')->useCurrent();
            $table->boolean('subscription_enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription');
    }
};
