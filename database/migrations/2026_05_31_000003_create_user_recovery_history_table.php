<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_recovery_history', function (Blueprint $table): void {
            $table->id();
            $table->dateTime('user_recovery_history_intent_date')->useCurrent();
            $table->string('user_recovery_history_recovery_answered', 255);
            $table->boolean('user_recovery_history_recovered_success');
            $table->string('user_recovery_history_method_used', 255);
            $table->string('user_recovery_history_ip', 200);
            $table->foreignId('user_recovery_history_user_id')->constrained('user');
            $table->string('user_recovery_history_decive', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_recovery_history');
    }
};
