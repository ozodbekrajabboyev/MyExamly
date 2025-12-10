<?php

namespace App\Console\Commands;

use App\Models\Exam;
use App\Models\Student;
use App\Services\ExamCalculationService;
use Illuminate\Console\Command;

class TestCalculationConsistency extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:calculation-consistency {exam_id?}';

    /**
     * The console command description.
     */
    protected $description = 'Test calculation consistency across different methods';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $examId = $this->argument('exam_id');

        if (!$examId) {
            $exam = Exam::whereHas('marks')->first();
            if (!$exam) {
                $this->error('No exam with marks found');
                return 1;
            }
        } else {
            $exam = Exam::find($examId);
            if (!$exam) {
                $this->error("Exam with ID {$examId} not found");
                return 1;
            }
        }

        $this->info("Testing calculation consistency for Exam ID: {$exam->id}");
        $this->info("Exam: {$exam->sinf->name} | {$exam->subject->name} | {$exam->serial_number}-{$exam->type}");

        $students = Student::where('sinf_id', $exam->sinf_id)
            ->with(['exams' => function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            }])
            ->take(3) // Test first 3 students
            ->get();

        $this->info("\nTesting calculation consistency for first 3 students:");

        foreach ($students as $student) {
            $this->info("\n--- Student: {$student->full_name} ---");

            // Method 1: Using our centralized service
            $serviceCalc = ExamCalculationService::calculateStudentScore($student, $exam);
            $this->info("ExamCalculationService: Total={$serviceCalc['total']}, Percentage={$serviceCalc['percentage']}%");

            // Method 2: Current pivot data
            $pivotData = $student->exams->first()?->pivot;
            if ($pivotData) {
                $this->info("Current Pivot Data: Total={$pivotData->total}, Percentage={$pivotData->percentage}%");

                // Check consistency
                if ($serviceCalc['total'] == $pivotData->total && $serviceCalc['percentage'] == $pivotData->percentage) {
                    $this->info("✅ CONSISTENT - Values match!");
                } else {
                    $this->error("❌ INCONSISTENT - Values don't match!");
                    $this->warn("Service vs Pivot: Total ({$serviceCalc['total']} vs {$pivotData->total}), Percentage ({$serviceCalc['percentage']} vs {$pivotData->percentage})");
                }
            } else {
                $this->warn("No pivot data found for this student");
            }
        }

        $this->info("\n=== Statistics ===");
        $stats = ExamCalculationService::getCalculationStats($exam);
        foreach ($stats as $key => $value) {
            $this->info("{$key}: {$value}");
        }

        return 0;
    }
}
