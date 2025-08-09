<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ChsbBsbWidget;
use App\Filament\Widgets\StatisticsChartWidget;
use Filament\Pages\Page;

class Statistics extends Page
{
    /**
     * The icon to be used for the navigation item.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    /**
     * The view to be used for the page.
     *
     * @var string
     */
    protected static string $view = 'filament.pages.statistics';

    /**
     * The title of the page.
     *
     * @var string
     */
    protected static ?string $title = 'Exam Statistics';

    /**
     * The navigation group for the page.
     * This helps organize the sidebar.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Reports';

    /**
     * Get the header widgets that should be displayed on the page.
     * This method registers our chart widget to appear at the top of the page.
     *
     * @return array<class-string<\Filament\Widgets\Widget>|string>
     */

    public function getFooterWidgets(): array
    {
        return [
            StatisticsChartWidget::class,
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->role_id === 2 || auth()->user()->role_id === 3;
    }
}
