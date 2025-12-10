<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Exam;
use App\Models\Mark;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ExamCalculationService
{
    /**
     * Calculate total score and percentage for a specific student in an exam
     * This is the SINGLE SOURCE OF TRUTH for all calculations
     */
    public static function calculateStudentScore(Student $student, Exam $exam): array
    {
        $problems = $exam->problems ?? [];
        $totalScore = 0;
        $totalMaxScore = collect($problems)->sum('max_mark');

        $calculationLog = [];

        foreach ($problems as $problem) {
            $mark = Mark::where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->where('problem_id', $problem['id'])
                ->first();

            $rawMark = $mark ? $mark->mark : 0;
            $maxMark = $problem['max_mark'];

            // ENFORCE MAX LIMIT - Critical for consistency
            $actualMark = min($rawMark, $maxMark);
            $totalScore += $actualMark;

            $calculationLog[] = [
                'problem_id' => $problem['id'],
                'raw_mark' => $rawMark,
                'max_mark' => $maxMark,
                'actual_mark' => $actualMark
            ];
        }

        $percentage = $totalMaxScore > 0 ? round(($totalScore / $totalMaxScore) * 100, 2) : 0;

        // Log calculation details for debugging
        Log::info("Score calculation for student {$student->id} in exam {$exam->id}", [
            'total_score' => $totalScore,
            'total_max_score' => $totalMaxScore,
            'percentage' => $percentage,
            'details' => $calculationLog
        ]);

        return [
            'total' => $totalScore,
            'percentage' => $percentage,
            'calculation_details' => $calculationLog
        ];
    }

    /**
     * Calculate scores for all students in an exam
     */
    public static function calculateAllStudentScores(Exam $exam): Collection
    {
        $students = Student::where('sinf_id', $exam->sinf_id)->get();
        $results = collect();

        foreach ($students as $student) {
            $calculation = self::calculateStudentScore($student, $exam);
            $results->put($student->id, [
                'student' => $student,
                'total' => $calculation['total'],
                'percentage' => $calculation['percentage']
            ]);
        }

        return $results;
    }

    /**
     * Validate that a mark doesn't exceed the maximum allowed for a problem
     */
    public static function validateMark(float $mark, array $problem): bool
    {
        return $mark >= 0 && $mark <= $problem['max_mark'];
    }

    /**
     * Get calculation statistics for debugging
     */
    public static function getCalculationStats(Exam $exam): array
    {
        $problems = $exam->problems ?? [];
        $totalMaxScore = collect($problems)->sum('max_mark');
        $studentsCount = Student::where('sinf_id', $exam->sinf_id)->count();
        $marksCount = Mark::where('exam_id', $exam->id)->count();

        return [
            'exam_id' => $exam->id,
            'problems_count' => count($problems),
            'total_max_score' => $totalMaxScore,
            'students_count' => $studentsCount,
            'marks_count' => $marksCount,
            'expected_marks' => $studentsCount * count($problems)
        ];
    }
}
