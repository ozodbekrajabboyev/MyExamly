<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;


class ExamResultsWidget extends Widget
{
    protected static string $view = 'filament.widgets.exam-results-widget';
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->role_id !== 3;
    }
    protected int | string | array $columnSpan = 'full';
}
