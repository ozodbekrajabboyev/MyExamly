<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class MarkService
{
    /**
     * Auto-create marks for an exam
     */
    public function createMarksForExam(Exam $exam): void
    {
        // Get all students in the exam's class
        $students = Student::where('sinf_id', $exam->sinf_id)
            ->where('maktab_id', $exam->maktab_id)
            ->get();

        // Get problems from the exam
        $problems = $exam->getProblems();

        // Auto-create marks for each student and problem combination
        foreach ($students as $student) {
            foreach ($problems as $problem) {
                Mark::firstOrCreate([
                    'student_id' => $student->id,
                    'exam_id' => $exam->id,
                    'problem_id' => $problem['id'],
                ], [
                    'mark' => 0, // Default mark is 0
                    'maktab_id' => $exam->maktab_id,
                    'sinf_id' => $exam->sinf_id, // Add the missing sinf_id
                ]);
            }
        }
    }

    /**
     * Bulk update marks for an exam
     */
    public function bulkUpdateMarks(Exam $exam, array $marksData): void
    {
        DB::transaction(function () use ($exam, $marksData) {
            foreach ($marksData as $key => $mark) {
                [$studentId, $problemId] = explode('_', $key);

                Mark::updateOrCreate([
                    'student_id' => $studentId,
                    'exam_id' => $exam->id,
                    'problem_id' => $problemId,
                ], [
                    'mark' => $mark,
                    'maktab_id' => $exam->maktab_id,
                    'sinf_id' => $exam->sinf_id, // Add the missing sinf_id
                ]);
            }
        });
    }

    /**
     * Validate mark values against problem max marks
     */
    public function validateMarks(Exam $exam, array $marksData): array
    {
        $errors = [];
        $problems = collect($exam->getProblems())->keyBy('id');

        foreach ($marksData as $key => $mark) {
            [$studentId, $problemId] = explode('_', $key);

            $problem = $problems->get($problemId);
            if (!$problem) {
                $errors[$key] = 'Noma\'lum topshiriq';
                continue;
            }

            if ($mark < 0) {
                $errors[$key] = 'Baho manfiy bo\'lishi mumkin emas';
            } elseif ($mark > $problem['max_mark']) {
                $errors[$key] = "Baho {$problem['max_mark']} dan oshmasligi kerak";
            }
        }

        return $errors;
    }

    /**
     * Get exam statistics
     */
    public function getExamStatistics(Exam $exam): array
    {
        $totalStudents = Student::where('sinf_id', $exam->sinf_id)
            ->where('maktab_id', $exam->maktab_id)
            ->count();

        $totalProblems = count($exam->getProblems());
        $maxPossibleScore = collect($exam->getProblems())->sum('max_mark');
        $totalMarks = $totalStudents * $totalProblems;

        $completedMarks = Mark::where('exam_id', $exam->id)
            ->where('mark', '>', 0)
            ->count();

        return [
            'total_students' => $totalStudents,
            'total_problems' => $totalProblems,
            'max_possible_score' => $maxPossibleScore,
            'total_marks' => $totalMarks,
            'completed_marks' => $completedMarks,
            'completion_percentage' => $totalMarks > 0 ? round(($completedMarks / $totalMarks) * 100, 2) : 0,
        ];
    }
}
