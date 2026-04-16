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
        Schema::create('crm_note_contacts', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->uuid('note_id');
            $table->uuid('contact_id');
            $table->enum('role', ['primary', 'secondary']);

            $table->foreign('note_id')
                ->references('id')
                ->on('crm_notes')
                ->cascadeOnDelete();

            $table->foreign('contact_id')
                ->references('id')
                ->on('contacts')
                ->cascadeOnDelete();

            $table->unique(['note_id', 'contact_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_note_contacts');
    }
};
