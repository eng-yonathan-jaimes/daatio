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
        Schema::create('store', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('store_user_id')->index('store_user_id');
            $table->string('store_name');
            $table->boolean('store_active');
            $table->dateTime('store_update_date');
            $table->string('store_address', 200);
            $table->integer('store_type_id')->index('store_type_id');
            $table->enum('store_location', ['Physical', 'Online', 'Both'])->default('Physical');
            $table->dateTime('store_registration_date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store');
    }
};
