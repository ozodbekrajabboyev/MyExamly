<?php

namespace App\Filament\Widgets;

use App\Models\Exam;
use App\Models\Mark;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StatisticsBySinfCHSB extends ChartWidget
{

    protected static ?string $heading = "Sinflar kesimida CHSB imtihon natijalari";

    protected function getData(): array
    {
        $maktabId = auth()->user()->maktab_id;

        // Get all sinfs for this maktab
        $allSinfs = DB::table('sinfs')
            ->where('maktab_id', $maktabId)
            ->orderBy('name')
            ->get();

        $labels = [];
        $values = [];

        foreach ($allSinfs as $sinf) {
            // Get exams for this sinf
            $exams = Exam::query()
                ->where('maktab_id', $maktabId)
                ->where('sinf_id', $sinf->id)
                ->where('type', 'CHSB')
                ->whereHas('problems')
                ->get();

            if ($exams->isEmpty()) {
                $labels[] = $sinf->name;
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

            // Get student marks per exam
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

            // Calculate average mastery percentage for this sinf
            $avgMasteryPercentage = empty($totalMasteryPercentages) ? 0 : round(array_sum($totalMasteryPercentages) / count($totalMasteryPercentages), 1);

            $labels[] = $sinf->name;
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
