<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Exam;
use App\Models\Mark;
use Illuminate\Support\Facades\DB;

class StatisticsByFanBSB extends ChartWidget
{
    protected static ?string $heading = "Fanlar kesimida BSB o‘zlashtirish foizi";

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->role_id !== 3;
    }
    protected function getData(): array
    {
        $maktabId = auth()->user()->maktab_id;

        // Fanlar
        $allSubjects = DB::table('subjects')
            ->where('maktab_id', $maktabId)
            ->orderBy('name')
            ->get();

        $labels = [];
        $values = [];

        foreach ($allSubjects as $subject) {
            // Shu fanga oid BSB imtihonlar
            $exams = Exam::query()
                ->where('maktab_id', $maktabId)
                ->where('subject_id', $subject->id)
                ->where('type', 'BSB')
                ->get();

            if ($exams->isEmpty()) {
                $labels[] = $subject->name;
                $values[] = 0;
                continue;
            }

            $examIds = $exams->pluck('id');

            // Student ballari
            $studentMarksPerExam = Mark::query()
                ->whereIn('exam_id', $examIds)
                ->groupBy('exam_id', 'student_id')
                ->select('exam_id', 'student_id', DB::raw('SUM(mark) as total_student_mark'))
                ->get()
                ->groupBy('exam_id');

            $totalMasteryPercentages = [];

            foreach ($exams as $exam) {
                // JSONB problems dan umumiy maksimal ball hisoblash
                $problems = collect($exam->problems ?? []);
                $totalMaxScore = $problems->sum('max_mark');

                if (!$totalMaxScore || $totalMaxScore == 0) continue;

                $marksForThisExam = $studentMarksPerExam->get($exam->id);
                if (!$marksForThisExam || $marksForThisExam->isEmpty()) continue;

                $averageScore = round($marksForThisExam->avg('total_student_mark'), 1);
                $masteryPercentage = round(($averageScore / $totalMaxScore) * 100, 1);

                $totalMasteryPercentages[] = $masteryPercentage;
            }

            // O‘rtacha mastery % fanga ko‘ra
            $avgMasteryPercentage = empty($totalMasteryPercentages)
                ? 0
                : round(array_sum($totalMasteryPercentages) / count($totalMasteryPercentages), 1);

            $labels[] = $subject->name;
            $values[] = $avgMasteryPercentage;
        }

        return [
            'datasets' => [
                [
                    'label' => "BSB o‘zlashtirish foizi (%)",
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
