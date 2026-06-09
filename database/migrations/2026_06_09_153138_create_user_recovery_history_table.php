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
        Schema::create('user_recovery_history', function (Blueprint $table) {
            $table->integer('id', true);
            $table->dateTime('user_recovery_history_intent_date')->useCurrent();
            $table->string('user_recovery_history_recovery_answered');
            $table->boolean('user_recovery_history_recovered_success');
            $table->string('user_recovery_history_method_used');
            $table->string('user_recovery_history_ip', 200);
            $table->integer('user_recovery_history_user_id')->index('user_recovery_history_user_id');
            $table->string('user_recovery_history_decive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_recovery_history');
    }
};
