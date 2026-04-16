<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_contact_facts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contact_id');
            $table->uuid('note_id')->nullable();
            $table->string('label');
            $table->string('value');
            $table->enum('source', ['ai_extracted', 'manually_pinned'])->default('manually_pinned');
            $table->boolean('confirmed')->default(true);
            $table->timestamp('created_at')->nullable();

            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('note_id')->references('id')->on('crm_notes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_contact_facts');
    }
};
