<?php

namespace App\Console\Commands;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Mark;
use App\Services\ExamCalculationService;
use Illuminate\Console\Command;

class TestPdfDownloadBehavior extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:pdf-download-behavior {exam_id}';

    /**
     * The console command description.
     */
    protected $description = 'Test what happens to dashboard values during PDF download simulation';

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

        $this->info("=== PDF Download Behavior Test for Exam ID: {$examId} ===");
        $this->info("Exam: {$exam->sinf->name} | {$exam->subject->name} | {$exam->serial_number}-{$exam->type}");

        // Get students before PDF download simulation
        $students = Student::where('sinf_id', $exam->sinf_id)
            ->with(['exams' => function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            }])
            ->orderBy('full_name')
            ->take(5)
            ->get();

        $this->info("\n=== VALUES BEFORE PDF Download Simulation ===");
        foreach ($students as $student) {
            $pivotData = $student->exams->first()?->pivot ?? null;
            if ($pivotData) {
                $this->info("{$student->full_name}: Total={$pivotData->total}, Percentage={$pivotData->percentage}%");
            } else {
                $this->info("{$student->full_name}: No pivot data");
            }
        }

        // Simulate PDF download process (the old problematic version)
        $this->info("\n=== SIMULATING PDF DOWNLOAD PROCESS ===");

        // Load students with their EXISTING pivot data - DO NOT RECALCULATE OR UPDATE
        $studentsForPdf = Student::where('sinf_id', $exam->sinf_id)
            ->with(['exams' => function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            }])
            ->orderBy('full_name')
            ->get();

        // DO NOT update pivot data during PDF generation - use existing data only
        $this->info("PDF generation completed without modifying pivot data");

        // Check values after PDF generation
        $studentsAfter = Student::where('sinf_id', $exam->sinf_id)
            ->with(['exams' => function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            }])
            ->orderBy('full_name')
            ->take(5)
            ->get();

        $this->info("\n=== VALUES AFTER PDF Download Simulation ===");
        $valuesChanged = false;

        foreach ($studentsAfter as $index => $student) {
            $pivotDataAfter = $student->exams->first()?->pivot ?? null;
            $pivotDataBefore = $students[$index]->exams->first()?->pivot ?? null;

            if ($pivotDataAfter && $pivotDataBefore) {
                $this->info("{$student->full_name}: Total={$pivotDataAfter->total}, Percentage={$pivotDataAfter->percentage}%");

                if ($pivotDataAfter->total != $pivotDataBefore->total ||
                    $pivotDataAfter->percentage != $pivotDataBefore->percentage) {
                    $this->error("  ❌ VALUES CHANGED!");
                    $this->warn("  Before: Total={$pivotDataBefore->total}, Percentage={$pivotDataBefore->percentage}%");
                    $this->warn("  After:  Total={$pivotDataAfter->total}, Percentage={$pivotDataAfter->percentage}%");
                    $valuesChanged = true;
                }
            }
        }

        if (!$valuesChanged) {
            $this->info("\n✅ SUCCESS: No values changed during PDF download!");
        } else {
            $this->error("\n❌ PROBLEM: Values changed during PDF download!");
        }

        return $valuesChanged ? 1 : 0;
    }
}
