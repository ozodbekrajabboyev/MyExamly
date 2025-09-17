<?php

namespace App\Filament\Widgets;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->role_id !== 3;
    }

    protected function getStats(): array
    {
        $teachers = Teacher::all()->where('maktab_id', auth()->user()->maktab_id)->count();
        $students = Student::all()->where('maktab_id', auth()->user()->maktab_id)->count();
        $exams_count = Exam::all()->where('maktab_id', auth()->user()->maktab_id)->count();
        return [
            Stat::make("O'qituvchilar", $teachers)
                ->icon('heroicon-o-academic-cap'),
            Stat::make("O'quvchilar", $students)
                ->icon('heroicon-o-user'),
            Stat::make("Jami imtihonlar", $exams_count)
                    ->icon('heroicon-o-pencil-square'),
        ];
    }
}
