<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix the accounts.id column type to uuid (char(36)).
     * This resolves "Data truncated for column 'id'" errors when
     * the HasUuids trait generates a UUIDv7 for the primary key.
     */
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->uuid('id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback — the column should remain uuid.
    }
};
