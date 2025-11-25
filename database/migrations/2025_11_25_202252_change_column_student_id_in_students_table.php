<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_exams', function (Blueprint $table) {
            // Make column nullable
            $table->unsignedBigInteger('student_id')->nullable()->change();

            // Drop existing foreign key
            $table->dropForeign(['student_id']);

            // Add foreign key with nullOnDelete
            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_exams', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['student_id']);

            // Make column not nullable again
            $table->unsignedBigInteger('student_id')->nullable(false)->change();

            // Restore previous foreign key
            $table->foreign('student_id')
                ->references('id')
                ->on('students');
        });
    }
};
