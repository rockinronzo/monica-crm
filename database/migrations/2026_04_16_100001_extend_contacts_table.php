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
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('relationship_origin')->nullable()->after('prefix');
            $table->string('met_at')->nullable()->after('relationship_origin');
            $table->date('met_on')->nullable()->after('met_at');
            $table->uuid('introduced_by_contact_id')->nullable()->after('met_on');
            $table->timestamp('last_interaction_at')->nullable()->after('introduced_by_contact_id');
            $table->integer('interaction_frequency_days')->nullable()->after('last_interaction_at');

            $table->foreign('introduced_by_contact_id')
                ->references('id')
                ->on('contacts')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['introduced_by_contact_id']);
            $table->dropColumn([
                'relationship_origin',
                'met_at',
                'met_on',
                'introduced_by_contact_id',
                'last_interaction_at',
                'interaction_frequency_days',
            ]);
        });
    }
};
