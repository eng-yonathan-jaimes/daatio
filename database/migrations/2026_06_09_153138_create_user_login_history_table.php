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
        Schema::create('user_login_history', function (Blueprint $table) {
            $table->integer('id', true);
            $table->dateTime('user_login_history_login_date')->useCurrent();
            $table->string('user_login_history_ip', 15);
            $table->string('user_login_history_device');
            $table->integer('user_login_history_user_id')->index('user_login_history_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_login_history');
    }
};
