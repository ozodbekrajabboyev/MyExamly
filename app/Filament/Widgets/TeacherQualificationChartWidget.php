<?php

namespace App\Filament\Widgets;

use App\Models\Teacher;
use App\Models\Maktab;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class TeacherQualificationChartWidget extends ChartWidget
{
    protected static ?string $heading = 'O\'qituvchilar Malaka Toifasi Statistikasi';

    public static function canView(): bool
    {
        return request()->routeIs('filament.app.pages.malaka-statistikasi');
    }

    protected static ?string $pollingInterval = null;

    public ?int $regionId = null;
    public ?int $districtId = null;

    #[On('updateTeacherStats')]
    public function updateTeacherStats(?int $regionId, ?int $districtId): void
    {
        $this->regionId = $regionId;
        $this->districtId = $districtId;
    }

    protected function getData(): array
    {
        $query = Teacher::query()
            ->join('maktabs', 'teachers.maktab_id', '=', 'maktabs.id');

        if ($this->regionId) {
            $query->where('maktabs.region_id', $this->regionId);
        }

        if ($this->districtId) {
            $query->where('maktabs.district_id', $this->districtId);
        }

        $qualificationStats = $query
            ->select('teachers.malaka_toifa_daraja', DB::raw('COUNT(*) as count'))
            ->groupBy('teachers.malaka_toifa_daraja')
            ->get();

        $qualificationLabels = [
            'oliy-toifa' => 'Oliy toifa',
            '1-toifa' => '1-toifa',
            '2-toifa' => '2-toifa',
            'mutaxasis' => 'Mutaxasis',
        ];

        $labels = [];
        $data = [];
        $percentages = []; // Pastdagi foizlar uchun
        $backgroundColors = [
            'rgba(255, 99, 132, 0.8)', // 1-toifa
            'rgba(54, 162, 235, 0.8)', // 2-toifa
            'rgba(255, 205, 86, 0.8)', // Mutaxasis
            'rgba(75, 192, 192, 0.8)', // Oliy toifa
        ];
        $borderColors = [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 205, 86, 1)',
            'rgba(75, 192, 192, 1)',
        ];
        $unknownColor = 'rgba(169, 169, 169, 0.8)'; // Och kulrang, chiroyliroq
        $unknownBorderColor = 'rgba(169, 169, 169, 1)';

        $usedColors = [];
        $usedBorderColors = [];
        $colorIndex = 0;
        $total = $qualificationStats->sum('count'); // Umumiy son

        foreach ($qualificationStats as $stat) {
            $qualification = $stat->malaka_toifa_daraja;
            $count = $stat->count;

            if ($count > 0 && isset($qualificationLabels[$qualification])){
                $label = $qualificationLabels[$qualification] ?? 'Noma\'lum';
                $labels[] = $label;
                $data[] = $count;

                // Rang tanlash
                if ($label === 'Noma\'lum') {
                    $usedColors[] = $unknownColor;
                    $usedBorderColors[] = $unknownBorderColor;
                } else {
                    $usedColors[] = $backgroundColors[$colorIndex] ?? 'rgba(128, 128, 128, 0.8)';
                    $usedBorderColors[] = $borderColors[$colorIndex] ?? 'rgba(128, 128, 128, 1)';
                    $colorIndex++;
                }

                // Foiz hisoblash (pastda ko'rsatish uchun)
//                $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
//                $percentages[] = "{$label}: {$percentage}%";
            }
        }

        if (empty($data)) {
            return [
                'datasets' => [
                    [
                        'label' => 'O\'qituvchilar soni',
                        'data' => [1],
                        'backgroundColor' => ['rgba(200, 200, 200, 0.8)'],
                        'borderColor' => ['rgba(200, 200, 200, 1)'],
                    ],
                ],
                'labels' => ['Ma\'lumot topilmadi'],
                'percentages' => ['Ma\'lumot topilmadi: 100%'],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'O\'qituvchilar soni',
                    'data' => $data,
                    'backgroundColor' => $usedColors,
                    'borderColor' => $usedBorderColors,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
            'percentages' => $percentages, // Pastdagi foizlar
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    protected function getOptions(): RawJs // Butunlay RawJs ga o'zgartirildi
    {
        return RawJs::make(<<<'JS'
        {
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return `${label}: ${value} ta (${percentage}%)`;
                        }
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false,
        }
        JS);
    }

    // Pastdagi foizlarni ko'rsatish uchun
    public function getDescription(): ?string
    {
        $data = $this->getData();
        if (isset($data['percentages'])) {
            return implode(', ', $data['percentages']);
        }
        return null;
    }
}
