<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('tenant_id');
            $table->string('user_name', 255);
            $table->string('user_lastName', 255);
            $table->string('user_email', 255)->unique();
            $table->string('user_access', 255);
            $table->char('user_password', 60);
            $table->string('user_phone_number', 255);
            $table->dateTime('user_update_date');
            $table->dateTime('user_creation')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
