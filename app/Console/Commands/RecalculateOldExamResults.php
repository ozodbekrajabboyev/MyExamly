<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Exam;
use App\Models\Mark;

class RecalculateOldExamResults extends Command
{
    protected $signature = 'exams:recalculate-results';
    protected $description = 'Recalculate all exam results and save them in the student_exams pivot table';

    public function handle()
    {
        $this->info("Starting exam results recalculation...");

        // 1. Barcha imtihonlarni chaqiramiz, har bir exam bilan uning sinfi va o'quvchilari
        $exams = Exam::with(['sinf.students'])->get();

        foreach ($exams as $exam) {

            $this->info("Processing Exam ID: {$exam->id}");

            // Examdagi sinfdagi barcha o'quvchilar
            $students = $exam->sinf->students ?? collect();

            if ($students->isEmpty()) {
                $this->warn("  No students found for sinf_id {$exam->sinf_id}");
                continue;
            }

            // Exam problems JSON
            $problems = $exam->problems ?? [];
            $totalMaxScore = collect($problems)->sum('max_mark');

            if ($totalMaxScore <= 0) {
                $this->warn("  Exam ID {$exam->id} has no valid problems or totalMaxScore = 0");
                continue;
            }

            foreach ($students as $student) {

                // Har bir o'quvchining exam uchun ballarini yig'amiz
                $totalScore = Mark::where('exam_id', $exam->id)
                    ->where('student_id', $student->id)
                    ->sum('mark');

                $percentage = round(($totalScore / $totalMaxScore) * 100, 2);

                // Pivotga yozish yoki update qilish
                $exam->students()->syncWithoutDetaching([
                    $student->id => [
                        'total' => $totalScore,
                        'percentage' => $percentage,
                    ]
                ]);
            }

            $this->info("  Exam ID {$exam->id} recalculated successfully!");
        }

        $this->info("All exam results recalculated and saved to pivot table.");
        return Command::SUCCESS;
    }
}
