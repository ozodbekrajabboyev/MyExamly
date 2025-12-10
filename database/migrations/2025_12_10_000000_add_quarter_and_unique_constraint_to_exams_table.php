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
        Schema::table('exams', function (Blueprint $table) {
            // Add quarter field
            $table->enum('quarter', ['I', 'II', 'III', 'IV'])->nullable()->after('serial_number');

            // Add unique constraint for serial_number within the same sinf and subject
            // Quarter is intentionally excluded from this constraint as requested
            $table->unique(['sinf_id', 'subject_id', 'serial_number'], 'exams_sinf_subject_serial_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Drop the unique constraint first
            $table->dropUnique('exams_sinf_subject_serial_unique');

            // Drop the quarter column
            $table->dropColumn('quarter');
        });
    }
};
