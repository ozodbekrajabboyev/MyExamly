<?php

namespace App\Console\Commands;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Mark;
use App\Services\ExamCalculationService;
use Illuminate\Console\Command;

class TestPdfConsistency extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:pdf-consistency {exam_id}';

    /**
     * The console command description.
     */
    protected $description = 'Test that PDF values match dashboard values exactly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $examId = $this->argument('exam_id');
        $exam = Exam::with(['sinf', 'subject'])->find($examId);

        if (!$exam) {
            $this->error("Exam with ID {$examId} not found");
            return 1;
        }

        $this->info("=== PDF vs Dashboard Consistency Test for Exam ID: {$examId} ===");
        $this->info("Exam: {$exam->sinf->name} | {$exam->subject->name} | {$exam->serial_number}-{$exam->type}");

        // ========== DASHBOARD LOGIC (as in dashboard.blade.php) ==========
        $problems = collect($exam->problems)->sortBy('id')->values();
        $totalMaxScore = $problems->sum('max_mark');

        $students = Student::where('sinf_id', $exam->sinf_id)
            ->with(['exams' => function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            }])
            ->orderBy('full_name')
            ->take(5)
            ->get();

        $allMarks = Mark::where('exam_id', $examId)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $this->info("\n=== Dashboard vs PDF Comparison ===");

        foreach ($students as $student) {
            $this->info("\n--- Student: {$student->full_name} ---");

            // DASHBOARD LOGIC
            $pivotData = $student->exams->first()?->pivot ?? null;

            if ($pivotData) {
                $dashboardTotal = $pivotData->total;
                $dashboardPercentage = $pivotData->percentage;
            } else {
                $calculation = ExamCalculationService::calculateStudentScore($student, $exam);
                $dashboardTotal = $calculation['total'];
                $dashboardPercentage = $calculation['percentage'];
            }

            // PDF LOGIC (as in dashboard-table.blade.php)
            $pivotData = $student->exams->first()?->pivot ?? null;

            if ($pivotData) {
                $pdfTotal = $pivotData->total;
                $pdfPercentage = $pivotData->percentage;
                $this->info("PDF: Using pivot data");
            } else {
                // Fallback calculation with max limit enforcement
                $pdfTotal = 0;
                foreach($problems as $problem) {
                    $mark = Mark::where('exam_id', $exam->id)
                        ->where('student_id', $student->id)
                        ->where('problem_id', $problem['id'])
                        ->first();

                    $rawMark = $mark->mark ?? 0;
                    $actualMark = min($rawMark, $problem['max_mark']);
                    $pdfTotal += $actualMark;
                }
                $pdfPercentage = $totalMaxScore > 0 ? round(($pdfTotal / $totalMaxScore) * 100, 1) : 0;
                $this->info("PDF: Using fallback calculation");
            }

            $this->info("Dashboard: Total={$dashboardTotal}, Percentage={$dashboardPercentage}%");
            $this->info("PDF:       Total={$pdfTotal}, Percentage={$pdfPercentage}%");

            if ($dashboardTotal == $pdfTotal && $dashboardPercentage == $pdfPercentage) {
                $this->info("✅ CONSISTENT - Values match perfectly!");
            } else {
                $this->error("❌ INCONSISTENT - Values don't match!");
                $this->warn("Dashboard vs PDF: Total ({$dashboardTotal} vs {$pdfTotal}), Percentage ({$dashboardPercentage} vs {$pdfPercentage})");
            }

            // Check individual problem scores consistency
            $studentMarks = $allMarks->get($student->id, collect());
            $this->info("Individual problem scores:");

            foreach ($problems as $problem) {
                // Dashboard logic
                $mark = $studentMarks->firstWhere('problem_id', $problem['id']);
                $dashboardScore = $mark ? min($mark->mark, $problem['max_mark']) : 0;

                // PDF logic
                $pdfMark = Mark::where('exam_id', $exam->id)
                    ->where('student_id', $student->id)
                    ->where('problem_id', $problem['id'])
                    ->first();
                $rawScore = $pdfMark->mark ?? 0;
                $pdfScore = min($rawScore, $problem['max_mark']);

                $this->info("  Problem {$problem['id']}: Dashboard={$dashboardScore}, PDF={$pdfScore}");

                if ($dashboardScore != $pdfScore) {
                    $this->error("  ❌ Problem {$problem['id']} scores don't match!");
                }
            }
        }

        return 0;
    }
}
