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
        Schema::create('user', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedInteger('tenant_id');
            $table->string('user_name');
            $table->string('user_lastName');
            $table->string('user_email');
            $table->string('user_access');
            $table->char('user_password', 60);
            $table->string('user_phone_number');
            $table->string('email_verification_code', 6)->nullable();
            $table->string('phone_verification_code', 6)->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->dateTime('phone_verified_at')->nullable();
            $table->dateTime('user_update_date')->nullable();
            $table->dateTime('user_creation')->useCurrent();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
