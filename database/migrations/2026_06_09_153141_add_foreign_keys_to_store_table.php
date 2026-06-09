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
        Schema::table('store', function (Blueprint $table) {
            $table->foreign(['store_user_id'], 'store_ibfk_1')->references(['id'])->on('user')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['store_type_id'], 'store_ibfk_2')->references(['id'])->on('store_type')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store', function (Blueprint $table) {
            $table->dropForeign('store_ibfk_1');
            $table->dropForeign('store_ibfk_2');
        });
    }
};
