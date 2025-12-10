<?php

namespace App\Console\Commands;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Mark;
use App\Services\ExamCalculationService;
use Illuminate\Console\Command;

class DebugDashboardValues extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'debug:dashboard-values {exam_id}';

    /**
     * The console command description.
     */
    protected $description = 'Debug dashboard values to see what gets displayed vs PDF';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $examId = $this->argument('exam_id');
        $exam = Exam::find($examId);

        if (!$exam) {
            $this->error("Exam with ID {$examId} not found");
            return 1;
        }

        $this->info("=== Dashboard Values Debug for Exam ID: {$examId} ===");
        $this->info("Exam: {$exam->sinf->name} | {$exam->subject->name} | {$exam->serial_number}-{$exam->type}");

        $students = Student::where('sinf_id', $exam->sinf_id)
            ->with(['exams' => function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            }])
            ->orderBy('full_name')
            ->take(5) // First 5 students
            ->get();

        $problems = $exam->problems ?? [];
        $totalMaxScore = collect($problems)->sum('max_mark');

        $this->info("Total Max Score: {$totalMaxScore}");
        $this->info("Problems: " . json_encode($problems));

        // Load all marks for this exam at once (simulating dashboard logic)
        $allMarks = Mark::where('exam_id', $examId)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $this->info("\n=== Student Values (Dashboard Logic) ===");

        foreach ($students as $index => $student) {
            $this->info("\n--- Student: {$student->full_name} ---");

            // Dashboard logic - exactly as in the blade file
            $pivotData = $student->exams->first()?->pivot ?? null;

            if ($pivotData) {
                $overall = $pivotData->total;
                $percentage = $pivotData->percentage;
                $this->info("Using Pivot Data: Total={$overall}, Percentage={$percentage}%");
            } else {
                $calculation = ExamCalculationService::calculateStudentScore($student, $exam);
                $overall = $calculation['total'];
                $percentage = $calculation['percentage'];
                $this->info("Using Service Calculation: Total={$overall}, Percentage={$percentage}%");
            }

            $studentMarks = $allMarks->get($student->id, collect());

            $this->info("Individual Problem Scores:");
            $calculatedTotal = 0;
            foreach ($problems as $problem) {
                $mark = $studentMarks->firstWhere('problem_id', $problem['id']);
                $rawScore = $mark ? $mark->mark : 0;
                $score = min($rawScore, $problem['max_mark']); // With max limit
                $calculatedTotal += $score;

                $this->info("  Problem {$problem['id']}: Raw={$rawScore}, Displayed={$score}, Max={$problem['max_mark']}");
            }

            $this->info("Manual Total: {$calculatedTotal}");
            $this->info("Display Total: {$overall}");

            if ($calculatedTotal != $overall) {
                $this->error("❌ MISMATCH: Manual calculation doesn't match display total!");
            } else {
                $this->info("✅ CONSISTENT: Manual calculation matches display total");
            }
        }

        return 0;
    }
}
