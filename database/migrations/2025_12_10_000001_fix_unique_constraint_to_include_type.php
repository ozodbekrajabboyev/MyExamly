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
            // Drop the old unique constraint that doesn't include type
            try {
                $table->dropUnique('exams_sinf_subject_serial_unique');
            } catch (\Exception $e) {
                // Constraint might not exist or have different name
            }

            // Add new unique constraint that includes type
            $table->unique(['sinf_id', 'subject_id', 'type', 'serial_number'], 'exams_sinf_subject_type_serial_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Drop the new constraint
            $table->dropUnique('exams_sinf_subject_type_serial_unique');

            // Restore old constraint (if needed for rollback)
            $table->unique(['sinf_id', 'subject_id', 'serial_number'], 'exams_sinf_subject_serial_unique');
        });
    }
};
