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
        Schema::create('crm_note_tags', function (Blueprint $table) {
            $table->uuid('note_id');
            $table->uuid('tag_id');

            $table->primary(['note_id', 'tag_id']);

            $table->foreign('note_id')
                ->references('id')
                ->on('crm_notes')
                ->cascadeOnDelete();

            $table->foreign('tag_id')
                ->references('id')
                ->on('crm_tags')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_note_tags');
    }
};
