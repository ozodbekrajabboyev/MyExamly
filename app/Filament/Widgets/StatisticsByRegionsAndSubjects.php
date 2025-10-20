<?php

namespace App\Filament\Widgets;

use App\Models\Mark;
use App\Models\Region;
use App\Models\Subject;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StatisticsByRegionsAndSubjects extends ChartWidget
{
    protected static ?string $heading = 'Viloyatlar + Fanlar bo\'yicha o\'rtacha natijalar (%)';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->role_id === 3;
    }

    protected function getData(): array
    {
        $regions = Region::pluck('name', 'id');

        $requiredSubjects = ['Matematika', 'Ingliz tili', 'Fizika', 'Biologiya'];
        $subjects = Subject::whereIn('name', $requiredSubjects)
            ->pluck('name', 'id');

        $labels = $regions->values()->toArray(); // Viloyat nomlari
        $datasets = [];

        // Har bir fan uchun alohida rang belgilash
        $subjectColors = $this->getSubjectColors();

        foreach ($subjects as $subjectId => $subjectName) {
            $subjectData = [];
            foreach ($regions as $regionId => $regionName) {
                // Shu viloyat + fan uchun o'rtacha ballni hisoblash
                $avgMark = Mark::query()
                    ->join('exams', 'marks.exam_id', '=', 'exams.id')
                    ->join('maktabs', 'marks.maktab_id', '=', 'maktabs.id')
                    ->where('exams.subject_id', $subjectId)
                    ->where('maktabs.region_id', $regionId)
                    ->avg('marks.mark');
                $avgMark = $avgMark ? round($avgMark, 1) : 0;

                $subjectData[] = $avgMark;
            }

            $datasets[] = [
                'label' => $subjectName,
                'data' => $subjectData,
                'backgroundColor' => $subjectColors[$subjectName] ?? '#6B7280',
                'borderColor' => $subjectColors[$subjectName] ?? '#6B7280',
                'borderWidth' => 1,
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bar chart
    }

    private function getSubjectColors(): array
    {
        return [
            'Matematika' => '#3B82F6',    // Blue
            'Ingiliz tili' => '#10B981',  // Green
            'Fizika' => '#F59E0B',        // Yellow/Orange
            'Biologiya' => '#EF4444',     // Red
        ];
    }
}
