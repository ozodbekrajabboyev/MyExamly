<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ExamResultsWidget extends Widget
{
    protected static string $view = 'filament.widgets.exam-results-widget';
    protected int | string | array $columnSpan = 'full'; // Full width
}
