<?php

namespace App\Filament\Widgets;

use App\Models\Teacher;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class MalakaStatisticsChart extends ChartWidget
{
    protected static ?string $heading = 'Malaka Daraja Taqsimoti';

    protected static ?string $description = 'O\'qituvchilar malaka darajasining foiz ko\'rsatkichi';

    public static function canView(): bool
    {
        return request()->routeIs('filament.app.pages.malaka-statistikasi');
    }

    protected function getData(): array
    {
        $cacheKey = 'malaka_chart_stats';
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
            $stats = Teacher::selectRaw('malaka_toifa_daraja, COUNT(*) as count')
                ->groupBy('malaka_toifa_daraja')
                ->pluck('count', 'malaka_toifa_daraja')
                ->toArray();

            $total = array_sum($stats);

            $labels = [];
            $data = [];
            $backgroundColors = [];

            $qualificationMap = [
                'oliy-toifa' => ['label' => 'Oliy toifa', 'color' => '#10B981'],
                '1-toifa' => ['label' => '1-toifa', 'color' => '#8B5CF6'],
                '2-toifa' => ['label' => '2-toifa', 'color' => '#F59E0B'],
                'mutaxasis' => ['label' => 'Mutaxasis', 'color' => '#6B7280'],
            ];

            foreach ($qualificationMap as $key => $config) {
                if (isset($stats[$key]) && $stats[$key] > 0) {
                    $count = $stats[$key];
                    $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    
                    $labels[] = $config['label'] . " ({$count} - {$percentage}%)";
                    $data[] = $count;
                    $backgroundColors[] = $config['color'];
                }
            }

            // Add unspecified if there are any
            $specifiedTotal = array_sum(array_intersect_key($stats, $qualificationMap));
            $totalTeachers = Teacher::count();
            $unspecified = $totalTeachers - $specifiedTotal;
            
            if ($unspecified > 0) {
                $percentage = $totalTeachers > 0 ? round(($unspecified / $totalTeachers) * 100, 1) : 0;
                $labels[] = "Belgilanmagan ({$unspecified} - {$percentage}%)";
                $data[] = $unspecified;
                $backgroundColors[] = '#EF4444';
            }

            return [
                'datasets' => [
                    [
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                        'borderWidth' => 2,
                        'borderColor' => '#ffffff',
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => '#ffffff',
                    'bodyColor' => '#ffffff',
                    'borderColor' => '#374151',
                    'borderWidth' => 1,
                    'cornerRadius' => 8,
                    'displayColors' => true,
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return context.label.split(" (")[0] + ": " + context.raw + " (" + percentage + "%)";
                        }'
                    ],
                ],
            ],
            'animation' => [
                'animateRotate' => true,
                'animateScale' => true,
            ],
        ];
    }
}