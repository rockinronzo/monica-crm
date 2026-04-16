<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
$table->uuid('account_id');
$table->uuid('primary_contact_id')->nullable();
            $table->uuid('note_type_id')->nullable();
            $table->string('title')->nullable();
            $table->text('body');
            $table->date('noted_at');
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('primary_contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('note_type_id')->references('id')->on('crm_note_types')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_notes');
    }
};
