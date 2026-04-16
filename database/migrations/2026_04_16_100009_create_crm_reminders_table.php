<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_reminders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('account_id');
$table->uuid('contact_id')->nullable();
            $table->uuid('note_id')->nullable();
            $table->string('note_text');
            $table->date('remind_on');
            $table->boolean('completed')->default(false);
            $table->timestamp('created_at')->nullable();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('note_id')->references('id')->on('crm_notes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_reminders');
    }
};
