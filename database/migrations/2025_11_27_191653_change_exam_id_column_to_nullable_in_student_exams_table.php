<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_exams', function (Blueprint $table) {
            // Drop existing foreign key for exam_id
            $table->dropForeign(['exam_id']);

            // Make column nullable
            $table->unsignedBigInteger('exam_id')->nullable()->change();

            // Add foreign key with nullOnDelete
            $table->foreign('exam_id')
                ->references('id')
                ->on('exams')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_exams', function (Blueprint $table) {
            // Drop foreign key for exam_id
            $table->dropForeign(['exam_id']);

            // Make column not nullable again
            $table->unsignedBigInteger('exam_id')->nullable(false)->change();

            // Restore previous foreign key
            $table->foreign('exam_id')
                ->references('id')
                ->on('exams');
        });
    }
};
