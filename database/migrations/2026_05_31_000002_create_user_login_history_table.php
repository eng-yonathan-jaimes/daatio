<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_login_history', function (Blueprint $table): void {
            $table->id();
            $table->dateTime('user_login_history_login_date')->useCurrent();
            $table->string('user_login_history_ip', 15);
            $table->string('user_login_history_device', 255);
            $table->foreignId('user_login_history_user_id')->constrained('user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_login_history');
    }
};
