<?php

namespace App\Console\Commands;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Services\ExamCalculationService;
use Illuminate\Console\Command;

class FixCalculationInconsistencies extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fix:calculation-inconsistencies {--force : Force fix without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Fix calculation inconsistencies across all exams';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for calculation inconsistencies...');

        $exams = Exam::whereHas('marks')->get();
        $inconsistentExams = [];

        foreach ($exams as $exam) {
            $students = Student::where('sinf_id', $exam->sinf_id)
                ->with(['exams' => function ($query) use ($exam) {
                    $query->where('exam_id', $exam->id);
                }])
                ->get();

            $hasInconsistencies = false;

            foreach ($students as $student) {
                $serviceCalc = ExamCalculationService::calculateStudentScore($student, $exam);
                $pivotData = $student->exams->first()?->pivot;

                if ($pivotData) {
                    if ($serviceCalc['total'] != $pivotData->total ||
                        $serviceCalc['percentage'] != $pivotData->percentage) {
                        $hasInconsistencies = true;
                        break;
                    }
                }
            }

            if ($hasInconsistencies) {
                $inconsistentExams[] = $exam;
            }
        }

        if (empty($inconsistentExams)) {
            $this->info('✅ All calculations are consistent!');
            return 0;
        }

        $this->warn("Found " . count($inconsistentExams) . " exam(s) with calculation inconsistencies:");

        foreach ($inconsistentExams as $exam) {
            $this->line("- Exam ID {$exam->id}: {$exam->sinf->name} | {$exam->subject->name} | {$exam->serial_number}-{$exam->type}");
        }

        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to fix these inconsistencies?')) {
                $this->info('Aborted.');
                return 1;
            }
        }

        $this->info('Fixing inconsistencies...');

        $fixed = 0;
        foreach ($inconsistentExams as $exam) {
            $this->line("Fixing Exam ID {$exam->id}...");

            $students = Student::where('sinf_id', $exam->sinf_id)->get();

            foreach ($students as $student) {
                $calculation = ExamCalculationService::calculateStudentScore($student, $exam);

                $exam->students()->syncWithoutDetaching([
                    $student->id => [
                        'total' => $calculation['total'],
                        'percentage' => $calculation['percentage'],
                        'updated_at' => now(),
                    ]
                ]);
            }

            $fixed++;
        }

        $this->info("✅ Fixed {$fixed} exam(s) with calculation inconsistencies.");

        return 0;
    }
}
