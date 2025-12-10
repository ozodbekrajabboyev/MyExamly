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
        // First, check what unique constraints exist on the exams table
        $existingConstraints = \DB::select("
            SELECT conname
            FROM pg_constraint
            WHERE conrelid = (SELECT oid FROM pg_class WHERE relname = 'exams')
            AND contype = 'u'
            AND (conname LIKE '%sinf%serial%' OR conname LIKE '%subject%serial%')
        ");

        // Drop existing constraints related to serial number uniqueness
        foreach ($existingConstraints as $constraint) {
            try {
                \DB::statement("ALTER TABLE exams DROP CONSTRAINT IF EXISTS {$constraint->conname}");
                echo "Dropped constraint: {$constraint->conname}\n";
            } catch (\Exception $e) {
                echo "Could not drop constraint {$constraint->conname}: " . $e->getMessage() . "\n";
            }
        }

        // Add new unique constraint that includes type
        $newConstraintExists = \DB::select("
            SELECT 1
            FROM pg_constraint
            WHERE conrelid = (SELECT oid FROM pg_class WHERE relname = 'exams')
            AND conname = 'exams_sinf_subject_type_serial_unique'
        ");

        if (empty($newConstraintExists)) {
            \DB::statement("
                ALTER TABLE exams
                ADD CONSTRAINT exams_sinf_subject_type_serial_unique
                UNIQUE (sinf_id, subject_id, type, serial_number)
            ");
            echo "Added new constraint: exams_sinf_subject_type_serial_unique\n";
        } else {
            echo "Constraint exams_sinf_subject_type_serial_unique already exists\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new constraint
        try {
            \DB::statement("ALTER TABLE exams DROP CONSTRAINT IF EXISTS exams_sinf_subject_type_serial_unique");
            echo "Dropped constraint: exams_sinf_subject_type_serial_unique\n";
        } catch (\Exception $e) {
            echo "Could not drop constraint: " . $e->getMessage() . "\n";
        }

        // Note: We don't restore the old constraint as it might cause conflicts
        // The old constraint should be recreated manually if needed
    }
};
