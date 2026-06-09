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
        Schema::table('client_state', function (Blueprint $table) {
            $table->foreign(['client_state_client_id'], 'client_state_ibfk_1')->references(['id'])->on('client')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['client_state_store_id'], 'client_state_ibfk_2')->references(['id'])->on('store')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_state', function (Blueprint $table) {
            $table->dropForeign('client_state_ibfk_1');
            $table->dropForeign('client_state_ibfk_2');
        });
    }
};
