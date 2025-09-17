<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TeacherQualificationChartWidget;
use Filament\Pages\Page;

class TeacherQualificationStatistics extends Page
{
    /**
     * The icon to be used for the navigation item.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    /**
     * The view to be used for the page.
     *
     * @var string
     */
    protected static string $view = 'filament.pages.teacher-qualification-statistics';

    /**
     * The title of the page.
     *
     * @var string
     */
    protected static ?string $title = 'Malaka Statistikasi';

    /**
     * The navigation group for the page.
     * This helps organize the sidebar.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Hisobotlar';

    /**
     * Get the footer widgets that should be displayed on the page.
     *
     * @return array<class-string<\Filament\Widgets\Widget>|string>
     */
    public function getFooterWidgets(): array
    {
        return [
            TeacherQualificationChartWidget::class
        ];
    }

    /**
     * Only allow users with role_id = 3 to access this page
     */
    public static function canAccess(): bool
    {
        return auth()->user()->role_id === 3;
    }
}
