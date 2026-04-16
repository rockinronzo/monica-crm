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
        Schema::create('crm_tags', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->uuid('account_id');
            $table->string('name');              // always lowercase
            $table->string('display_name');      // original casing
            $table->timestamp('created_at')->nullable();

            $table->foreign('account_id')
                ->references('id')
                ->on('accounts')
                ->cascadeOnDelete();

            $table->unique(['account_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_tags');
    }
};
