<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Exam;
use App\Models\Mark;
use Illuminate\Support\Facades\DB;

class StatisticsByFanCHSB extends ChartWidget
{
    protected static ?string $heading = "Fanlar kesimida CHSB o‘zlashtirish foizi";

    protected function getData(): array
    {
        $maktabId = auth()->user()->maktab_id;

        // Get all subjects for this maktab (assuming subjects table structure)
        // NEED TO CONFIRM: What's your subjects table name and structure?
        $allSubjects = DB::table('subjects') // or 'fans'?
        ->where('maktab_id', $maktabId) // if subjects are school-specific
        ->orderBy('name')
            ->get();

        $labels = [];
        $values = [];

        foreach ($allSubjects as $subject) {
            // Get all CHSB exams for this subject across all sinfs
            $exams = Exam::query()
                ->where('maktab_id', $maktabId)
                ->where('subject_id', $subject->id)
                ->where('type', 'CHSB')
                ->whereHas('problems')
                ->get();

            if ($exams->isEmpty()) {
                $labels[] = $subject->name;
                $values[] = 0;
                continue;
            }

            $examIds = $exams->pluck('id');

            // Get maximum marks per exam
            $maxMarksPerExam = DB::table('problems')
                ->whereIn('exam_id', $examIds)
                ->groupBy('exam_id')
                ->select('exam_id', DB::raw('SUM(max_mark) as total_max_mark'))
                ->get()
                ->keyBy('exam_id');

            // Get student marks per exam (across all sinfs for this subject)
            $studentMarksPerExam = Mark::query()
                ->whereIn('exam_id', $examIds)
                ->groupBy('exam_id', 'student_id')
                ->select('exam_id', 'student_id', DB::raw('SUM(mark) as total_student_mark'))
                ->get()
                ->groupBy('exam_id');

            $totalMasteryPercentages = [];

            foreach ($exams as $exam) {
                $totalMaxScore = $maxMarksPerExam->get($exam->id)?->total_max_mark;
                if (!$totalMaxScore || $totalMaxScore == 0) continue;

                $marksForThisExam = $studentMarksPerExam->get($exam->id);
                if (!$marksForThisExam || $marksForThisExam->isEmpty()) continue;

                $averageScore = $marksForThisExam->avg('total_student_mark');
                $masteryPercentage = ($averageScore / $totalMaxScore) * 100;

                $totalMasteryPercentages[] = $masteryPercentage;
            }

            // Calculate average mastery percentage for this subject
            $avgMasteryPercentage = empty($totalMasteryPercentages) ? 0 : round(array_sum($totalMasteryPercentages) / count($totalMasteryPercentages), 1);

            $labels[] = $subject->name;
            $values[] = $avgMasteryPercentage;
        }

        return [
            'datasets' => [
                [
                    'label' => "CHSB o‘zlashtirish foizi (%)",
                    'data' => $values,
                    'backgroundColor' => '#3B82F6',
                    'borderColor' => '#1D4ED8',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
