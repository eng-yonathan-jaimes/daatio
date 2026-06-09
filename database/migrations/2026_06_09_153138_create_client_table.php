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
        Schema::create('client', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('client_name');
            $table->string('client_last_name');
            $table->enum('client_document_type', ['Cedula', 'Passport', 'PPT'])->default('Cedula');
            $table->string('client_document_number');
            $table->string('client_email');
            $table->string('client_phone_number');
            $table->dateTime('client_registration_date');
            $table->dateTime('client_update_date');
            $table->boolean('client_active');
            $table->enum('client_type', ['Prompt', 'On-Term', 'Late', 'Partial', 'Deadbeat'])->default('On-Term');
            $table->json('client_stores_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client');
    }
};
