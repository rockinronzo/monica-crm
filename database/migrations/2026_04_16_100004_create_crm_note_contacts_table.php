<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_note_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('note_id');
            $table->uuid('contact_id');
            $table->enum('role', ['primary', 'secondary'])->default('secondary');
            $table->timestamp('created_at')->nullable();

            $table->unique(['note_id', 'contact_id']);
            $table->foreign('note_id')->references('id')->on('crm_notes')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_note_contacts');
    }
};
