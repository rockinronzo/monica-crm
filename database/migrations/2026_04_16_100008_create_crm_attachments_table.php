<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('note_id');
            $table->string('file_path');
            $table->string('file_type');
            $table->string('original_name');
            $table->timestamp('created_at')->nullable();

            $table->foreign('note_id')->references('id')->on('crm_notes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_attachments');
    }
};
