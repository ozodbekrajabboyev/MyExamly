<?php

namespace App\Filament\Widgets;

use App\Models\Exam;
use Filament\Widgets\ChartWidget;

class ChsbBsbWidget extends ChartWidget
{
    protected static ?string $heading = 'BSB va CHSB';
    protected function getData(): array
    {

        $chsb = Exam::all()->where('type', 'CHSB')
            ->where('maktab_id', auth()->user()->maktab_id)->count();

        $bsb = Exam::all()->where('type', 'BSB')
            ->where('maktab_id', auth()->user()->maktab_id)->count();


        return [
            'chart' => [
                'title' => 'Exams',
                'height' => 100,
                'width' => 100,
            ],
            'datasets' => [
                [
                    'label' => 'BSB va CHSB',
                    'data' => [$chsb, $bsb],
                    'backgroundColor' => ['#3b82f6', '#10b981'],
                    'borderColor' => 'gray',
                ],
            ],
            'labels' => ['CHSB', 'BSB'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
