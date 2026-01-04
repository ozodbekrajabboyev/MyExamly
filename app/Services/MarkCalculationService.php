<?php

namespace App\Services;

use App\Models\Mark;
use App\Models\Student;
use App\Models\Exam;
use Illuminate\Support\Collection;

class MarkCalculationService
{
    /**
     * Calculate total marks for a student in an exam using consistent logic
     */
    public function calculateStudentTotal(int $studentId, int $examId, ?Collection $marksLookup = null): float
    {
        // Get the exam to know which problems are valid
        $exam = \App\Models\Exam::find($examId);
        $validProblemIds = collect($exam->getProblems())->pluck('id')->toArray();

        if ($marksLookup) {
            // Use pre-loaded marks for better performance
            $studentMarks = $marksLookup->get($studentId);
            if (!$studentMarks) {
                return 0.0;
            }
            // Sum only marks for problems defined in exam structure
            return (float) $studentMarks->filter(function ($mark) use ($validProblemIds) {
                return in_array($mark->problem_id, $validProblemIds);
            })->sum(function ($mark) {
                return $mark->mark;
            });
        }

        // Fallback to database query (filter by valid problem IDs)
        return (float) Mark::where('student_id', $studentId)
            ->where('exam_id', $examId)
            ->whereIn('problem_id', $validProblemIds)
            ->sum('mark');
    }

    /**
     * Calculate totals for all students in an exam with optimized queries
     */
    public function calculateAllTotals(Exam $exam): Collection
    {
        return Mark::where('exam_id', $exam->id)
            ->selectRaw('student_id, SUM(mark) as total_mark')
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');
    }

    /**
     * Get pre-loaded marks lookup for an exam to avoid N+1 queries
     */
    public function getMarksLookup(int $examId): Collection
    {
        return Mark::where('exam_id', $examId)
            ->get()
            ->groupBy('student_id')
            ->map(function ($studentMarks) {
                return $studentMarks->keyBy('problem_id');
            });
    }

    /**
     * Validate mark consistency between JavaScript and PHP calculations
     */
    public function validateMarkConsistency(int $studentId, int $examId, float $expectedTotal): bool
    {
        $actualTotal = $this->calculateStudentTotal($studentId, $examId);

        // Allow for small floating point differences (0.1)
        return abs($actualTotal - $expectedTotal) < 0.1;
    }

    /**
     * Format total mark consistently across the application
     */
    public function formatTotal(float $total): string
    {
        return number_format($total, 1);
    }

    /**
     * Get JavaScript-compatible calculation logic
     */
    public function getJavaScriptCalculationCode(): string
    {
        return "
        function calculateTotal(inputs) {
            let total = 0;
            inputs.forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });
            return parseFloat(total.toFixed(1));
        }
        ";
    }
}
