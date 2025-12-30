<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Exam;
use App\Models\Sinf;
use App\Models\Subject;
use App\Models\FbMark;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuarterStatisticsService
{
    /**
     * Calculate comprehensive statistics for BSB and CHSB exams by quarter, sinf, and subject
     */
    public static function getQuarterStatistics(
        int $sinfId,
        int $subjectId,
        ?string $quarter = null
    ): array {
        $students = Student::where('sinf_id', $sinfId)
            ->orderBy('full_name')
            ->get();

        if ($students->isEmpty()) {
            return [
                'students_data' => [],
                'summary' => self::getEmptySummary()
            ];
        }

        // Check if we have any exam data at all for this sinf and subject
        $examQuery = DB::table('exams')
            ->where('sinf_id', $sinfId)
            ->where('subject_id', $subjectId);

        if ($quarter) {
            $examQuery->where('quarter', $quarter);
        } else {
            // Exclude null quarters when no specific quarter is selected
            $examQuery->whereNotNull('quarter');
        }

        $hasAnyExams = $examQuery->exists();

        if (!$hasAnyExams) {
            return [
                'students_data' => [],
                'summary' => self::getEmptySummary()
            ];
        }

        $studentsData = $students->map(function ($student) use ($sinfId, $subjectId, $quarter) {
            // Get BSB and CHSB results for this student
            $bsbResults = self::getExamResultsByType($student->id, $sinfId, $subjectId, 'BSB', $quarter);
            $chsbResults = self::getExamResultsByType($student->id, $sinfId, $subjectId, 'CHSB', $quarter);

            // Get FB marks for this student
            $fbMarks = self::getFbMarksByStudent($student->id, $sinfId, $subjectId, $quarter);

            $bsbTotal = $bsbResults['total_sum'];
            $chsbTotal = $chsbResults['total_sum'];
            $bsbPercentage = $bsbResults['percentage_avg'];
            $chsbPercentage = $chsbResults['percentage_avg'];

            // Calculate overall statistics
            $overallTotal = $bsbTotal + $chsbTotal;
            $overallPercentage = $bsbResults['exam_count'] > 0 && $chsbResults['exam_count'] > 0
                ? round(($bsbPercentage + $chsbPercentage) / 2, 2)
                : ($bsbResults['exam_count'] > 0 ? $bsbPercentage : $chsbPercentage);

            return [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'bsb' => [
                    'total' => $bsbTotal,
                    'percentage' => $bsbPercentage,
                    'exam_count' => $bsbResults['exam_count']
                ],
                'chsb' => [
                    'total' => $chsbTotal,
                    'percentage' => $chsbPercentage,
                    'exam_count' => $chsbResults['exam_count']
                ],
                'fb_marks' => $fbMarks,
                'overall_total' => round($overallTotal, 2),
                'overall_percentage' => $overallPercentage
            ];
        })->toArray();

        return [
            'students_data' => $studentsData,
            'summary' => self::calculateSummaryStatistics($studentsData)
        ];
    }

    /**
     * Get exam results by type (BSB or CHSB) for a specific student
     */
    private static function getExamResultsByType(
        int $studentId,
        int $sinfId,
        int $subjectId,
        string $examType,
        ?string $quarter = null
    ): array {
        $query = DB::table('student_exams')
            ->join('exams', 'student_exams.exam_id', '=', 'exams.id')
            ->where('student_exams.student_id', $studentId)
            ->where('exams.subject_id', $subjectId)
            ->where('exams.sinf_id', $sinfId)
            ->where('exams.type', $examType);

        if ($quarter) {
            $query->where('exams.quarter', $quarter);
        } else {
            // Exclude null quarters when no specific quarter is selected
            $query->whereNotNull('exams.quarter');
        }

        $results = $query->select('student_exams.total', 'student_exams.percentage')->get();

        $totalSum = $results->sum('total');
        $percentageAvg = $results->count() > 0 ? round($results->avg('percentage'), 2) : 0;
        $examCount = $results->count();

        return [
            'results' => $results,
            'total_sum' => $totalSum,
            'percentage_avg' => $percentageAvg,
            'exam_count' => $examCount
        ];
    }

    /**
     * Get FB marks for a specific student, sinf, subject, and quarter
     */
    private static function getFbMarksByStudent(
        int $studentId,
        int $sinfId,
        int $subjectId,
        ?string $quarter = null
    ): array {
        $query = FbMark::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('sinf_id', $sinfId);

        if ($quarter) {
            // Specific quarter requested
            $query->where('quarter', $quarter);
            $fbMark = $query->first();

            return [
                'current_quarter' => $quarter,
                'fb_value' => $fbMark ? $fbMark->fb : 4, // Default to 4 if no record exists
                'fb_id' => $fbMark ? $fbMark->id : null,
                'is_sum' => false
            ];
        } else {
            // All quarters - return sum
            $fbMarks = $query->get();
            $totalFb = $fbMarks->sum('fb');

            // If no records exist, return sum of default values for all quarters (4 * 4 = 16)
            if ($fbMarks->isEmpty()) {
                $totalFb = 16; // 4 quarters * default value of 4
            }

            return [
                'current_quarter' => null,
                'fb_value' => $totalFb,
                'fb_id' => null,
                'is_sum' => true,
                'quarters_data' => $fbMarks->keyBy('quarter')->toArray()
            ];
        }
    }

    /**
     * Get aggregated statistics by quarter for charts and overview
     */
    public static function getQuarterAggregates(
        int $sinfId,
        int $subjectId,
        string $examType
    ): array {
        $quarters = ['I', 'II', 'III', 'IV'];
        $data = [];

        foreach ($quarters as $quarter) {
            $stats = self::getQuarterStatistics($sinfId, $subjectId, $quarter);
            $summary = $stats['summary'];

            $data[$quarter] = [
                'quarter' => $quarter,
                'student_count' => $summary['total_students'],
                'avg_percentage' => $examType === 'BSB' ? $summary['avg_bsb_percentage'] : $summary['avg_chsb_percentage'],
                'total_score' => $examType === 'BSB' ? $summary['total_bsb_score'] : $summary['total_chsb_score']
            ];
        }

        return $data;
    }

    /**
     * Calculate summary statistics for a group of students
     */
    private static function calculateSummaryStatistics(array $studentsData): array
    {
        if (empty($studentsData)) {
            return self::getEmptySummary();
        }

        $totalStudents = count($studentsData);
        $totalBsbScore = collect($studentsData)->sum('bsb.total');
        $totalChsbScore = collect($studentsData)->sum('chsb.total');
        $avgBsbPercentage = collect($studentsData)->avg('bsb.percentage');
        $avgChsbPercentage = collect($studentsData)->avg('chsb.percentage');
        $avgOverallPercentage = collect($studentsData)->avg('overall_percentage');

        // Performance levels
        $excellentCount = collect($studentsData)->where('overall_percentage', '>=', 80)->count();
        $goodCount = collect($studentsData)->whereBetween('overall_percentage', [60, 79.99])->count();
        $satisfactoryCount = collect($studentsData)->whereBetween('overall_percentage', [40, 59.99])->count();
        $poorCount = collect($studentsData)->where('overall_percentage', '<', 40)->where('overall_percentage', '>', 0)->count();

        return [
            'total_students' => $totalStudents,
            'total_bsb_score' => round($totalBsbScore, 2),
            'total_chsb_score' => round($totalChsbScore, 2),
            'avg_bsb_percentage' => round($avgBsbPercentage, 2),
            'avg_chsb_percentage' => round($avgChsbPercentage, 2),
            'avg_overall_percentage' => round($avgOverallPercentage, 2),
            'performance_levels' => [
                'excellent' => $excellentCount,
                'good' => $goodCount,
                'satisfactory' => $satisfactoryCount,
                'poor' => $poorCount
            ]
        ];
    }

    /**
     * Get empty summary structure
     */
    private static function getEmptySummary(): array
    {
        return [
            'total_students' => 0,
            'total_bsb_score' => 0,
            'total_chsb_score' => 0,
            'avg_bsb_percentage' => 0,
            'avg_chsb_percentage' => 0,
            'avg_overall_percentage' => 0,
            'performance_levels' => [
                'excellent' => 0,
                'good' => 0,
                'satisfactory' => 0,
                'poor' => 0
            ]
        ];
    }

    /**
     * Get available quarters that have exam data for a specific sinf and subject
     */
    public static function getAvailableQuarters(int $sinfId, int $subjectId): array
    {
        $quarters = DB::table('exams')
            ->join('marks', 'exams.id', '=', 'marks.exam_id')
            ->where('exams.sinf_id', $sinfId)
            ->where('exams.subject_id', $subjectId)
            ->whereNotNull('exams.quarter')
            ->distinct()
            ->pluck('exams.quarter')
            ->sort()
            ->values()
            ->toArray();

        return $quarters;
    }

    /**
     * Get comparison statistics across quarters
     */
    public static function getQuarterComparison(int $sinfId, int $subjectId): array
    {
        $quarters = ['I', 'II', 'III', 'IV'];
        $comparison = [];

        foreach ($quarters as $quarter) {
            $stats = self::getQuarterStatistics($sinfId, $subjectId, $quarter);
            $summary = $stats['summary'];

            $comparison[$quarter] = [
                'quarter' => $quarter,
                'total_students' => $summary['total_students'],
                'avg_bsb' => $summary['avg_bsb_percentage'],
                'avg_chsb' => $summary['avg_chsb_percentage'],
                'avg_overall' => $summary['avg_overall_percentage'],
                'excellent_count' => $summary['performance_levels']['excellent'],
                'good_count' => $summary['performance_levels']['good']
            ];
        }

        return $comparison;
    }
}
