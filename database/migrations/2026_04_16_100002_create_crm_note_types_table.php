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
        Schema::create('crm_note_types', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('name');              // lowercase, machine-friendly
            $table->string('display_name');      // preserves original casing
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_note_types');
    }
};
