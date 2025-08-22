<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use Illuminate\Support\Collection;

class MarkService
{
    /**
     * Get all marks for an exam grouped by student and problem
     */
    public function getExamMarks(Exam $exam): Collection
    {
        return Mark::where('exam_id', $exam->id)
            ->with('student')
            ->get()
            ->groupBy(['student_id', 'problem_id']);
    }

    /**
     * Calculate total marks for a student in an exam
     */
    public function calculateStudentTotal(int $studentId, Exam $exam): float
    {
        return Mark::where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->sum('mark');
    }

    /**
     * Get exam statistics (average, highest, lowest, etc.)
     */
    public function getExamStatistics(Exam $exam): array
    {
        $problems = collect(is_string($exam->problems) ? json_decode($exam->problems, true) : ($exam->problems ?? []));
        $totalMaxMark = $problems->sum('max_mark');

        $studentTotals = Mark::where('exam_id', $exam->id)
            ->selectRaw('student_id, SUM(mark) as total_mark')
            ->groupBy('student_id')
            ->get()
            ->pluck('total_mark');

        if ($studentTotals->isEmpty()) {
            return [
                'total_students' => 0,
                'max_possible' => $totalMaxMark,
                'average' => 0,
                'highest' => 0,
                'lowest' => 0,
                'pass_rate' => 0,
            ];
        }

        $passThreshold = $totalMaxMark * 0.6; // 60% to pass
        $passedStudents = $studentTotals->filter(fn($total) => $total >= $passThreshold)->count();

        return [
            'total_students' => $studentTotals->count(),
            'max_possible' => $totalMaxMark,
            'average' => round($studentTotals->average(), 2),
            'highest' => $studentTotals->max(),
            'lowest' => $studentTotals->min(),
            'pass_rate' => $studentTotals->count() > 0 ? round(($passedStudents / $studentTotals->count()) * 100, 2) : 0,
        ];
    }

    /**
     * Get problem-wise statistics for an exam
     */
    public function getProblemStatistics(Exam $exam): array
    {
        $problems = collect(is_string($exam->problems) ? json_decode($exam->problems, true) : ($exam->problems ?? []));
        $statistics = [];

        foreach ($problems as $problem) {
            $marks = Mark::where('exam_id', $exam->id)
                ->where('problem_id', $problem['id'])
                ->get()
                ->pluck('mark');

            if ($marks->isEmpty()) {
                $statistics[] = [
                    'problem_id' => $problem['id'],
                    'max_mark' => $problem['max_mark'],
                    'average' => 0,
                    'highest' => 0,
                    'lowest' => 0,
                    'total_attempts' => 0,
                ];
                continue;
            }

            $statistics[] = [
                'problem_id' => $problem['id'],
                'max_mark' => $problem['max_mark'],
                'average' => round($marks->average(), 2),
                'highest' => $marks->max(),
                'lowest' => $marks->min(),
                'total_attempts' => $marks->count(),
            ];
        }

        return $statistics;
    }

    /**
     * Validate mark values against problem constraints
     */
    public function validateMarks(Exam $exam, array $marks): array
    {
        $problems = collect(is_string($exam->problems) ? json_decode($exam->problems, true) : ($exam->problems ?? []));
        $errors = [];

        foreach ($marks as $key => $markValue) {
            [$studentId, $problemId] = explode('_', $key);

            $problem = $problems->firstWhere('id', (int)$problemId);

            if (!$problem) {
                $errors[] = "Topshiriq {$problemId} topilmadi";
                continue;
            }

            if ($markValue < 0) {
                $errors[] = "Topshiriq {$problemId} uchun baho 0 dan kam bo'lishi mumkin emas";
            }

            if ($markValue > $problem['max_mark']) {
                $errors[] = "Topshiriq {$problemId} uchun baho maksimal bahon ({$problem['max_mark']}) dan oshishi mumkin emas";
            }
        }

        return $errors;
    }

    /**
     * Bulk save marks for an exam
     */
    public function saveMarks(Exam $exam, array $marks): array
    {
        $savedCount = 0;
        $updatedCount = 0;
        $errors = $this->validateMarks($exam, $marks);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'saved' => 0,
                'updated' => 0,
            ];
        }

        foreach ($marks as $key => $markValue) {
            [$studentId, $problemId] = explode('_', $key);

            $mark = Mark::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'problem_id' => $problemId,
                    'exam_id' => $exam->id,
                ],
                [
                    'mark' => $markValue,
                    'sinf_id' => $exam->sinf_id,
                    'maktab_id' => $exam->maktab_id,
                ]
            );

            if ($mark->wasRecentlyCreated) {
                $savedCount++;
            } else {
                $updatedCount++;
            }
        }

        return [
            'success' => true,
            'saved' => $savedCount,
            'updated' => $updatedCount,
            'errors' => [],
        ];
    }

    /**
     * Generate exam report data
     */
    public function generateExamReport(Exam $exam): array
    {
        $problems = collect(is_string($exam->problems) ? json_decode($exam->problems, true) : ($exam->problems ?? []));
        $students = $exam->sinf->students->sortBy('full_name');
        $marks = $this->getExamMarks($exam);
        $statistics = $this->getExamStatistics($exam);
        $problemStats = $this->getProblemStatistics($exam);

        $studentResults = [];

        foreach ($students as $student) {
            $studentMarks = [];
            $totalMark = 0;

            foreach ($problems as $problem) {
                $mark = $marks->get($student->id)?->get($problem['id'])?->first()?->mark ?? 0;
                $studentMarks[] = [
                    'problem_id' => $problem['id'],
                    'mark' => $mark,
                    'max_mark' => $problem['max_mark'],
                    'percentage' => $problem['max_mark'] > 0 ? round(($mark / $problem['max_mark']) * 100, 2) : 0,
                ];
                $totalMark += $mark;
            }

            $maxTotalMark = $problems->sum('max_mark');
            $percentage = $maxTotalMark > 0 ? round(($totalMark / $maxTotalMark) * 100, 2) : 0;

            $studentResults[] = [
                'student' => $student,
                'marks' => $studentMarks,
                'total_mark' => $totalMark,
                'max_total_mark' => $maxTotalMark,
                'percentage' => $percentage,
                'status' => $percentage >= 60 ? 'Muvaffaqiyatli' : 'Muvaffaqiyatsiz',
            ];
        }

        return [
            'exam' => $exam,
            'problems' => $problems->toArray(),
            'students' => $studentResults,
            'statistics' => $statistics,
            'problem_statistics' => $problemStats,
        ];
    }
}
