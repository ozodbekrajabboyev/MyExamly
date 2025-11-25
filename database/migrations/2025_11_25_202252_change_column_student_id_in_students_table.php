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
        Schema::table('student_exams', function (Blueprint $table) {
            // Drop the existing foreign key constraint only
            $table->dropForeign(['student_id']);

            // Re-add the foreign key with nullOnDelete
            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_exams', function (Blueprint $table) {
            $table->dropForeign(['student_id']);

            // Restore previous foreign key (without nullOnDelete)
            $table->foreign('student_id')
                ->references('id')
                ->on('students');
        });
    }
};
