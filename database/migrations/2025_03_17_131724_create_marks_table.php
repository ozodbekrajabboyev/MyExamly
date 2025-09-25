<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Maktab;
use App\Models\Sinf;
use App\Models\Student;
use App\Models\Exam;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Student::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Exam::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Sinf::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Maktab::class)->constrained()->cascadeOnDelete();
            $table->integer('problem_id'); // This will store the problem ID from JSON
            $table->decimal('mark', 5, 1)->default(0);
            $table->timestamps();

            // Ensure unique combination of student, exam, and problem
            $table->unique(['student_id', 'exam_id', 'problem_id']);

            // Indexes for better performance
            $table->index(['exam_id', 'student_id']);
            $table->index(['sinf_id', 'exam_id']);
            $table->index(['maktab_id', 'exam_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};
