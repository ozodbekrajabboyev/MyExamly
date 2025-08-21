<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\StatisticsByFanBSB;
use App\Filament\Widgets\StatisticsByFanCHSB;
use App\Filament\Widgets\StatisticsBySinfBSB;
use App\Filament\Widgets\StatisticsBySinfCHSB;
use App\Filament\Widgets\ExamResultsWidget;
use App\Filament\Widgets\StatisticsChartWidgetByCHSB;
use App\Filament\Widgets\StatisticsChartWidgetByBSB;
use App\Filament\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->brandName('MyExamly')
//            ->default()
            ->id('app')
            ->profile(isSimple: false)
            ->loginRouteSlug('login')
            ->registrationRouteSlug('register')
            ->passwordResetRoutePrefix('password-reset')
            ->passwordResetRequestRouteSlug('request')
            ->passwordResetRouteSlug('reset')
            ->emailVerificationRoutePrefix('email-verification')
            ->emailVerificationPromptRouteSlug('prompt')
            ->emailVerificationRouteSlug('verify')
            ->path('/')
            ->login()
            ->colors([
                'primary' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([])
//            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->widgets([
                StatsOverview::class,
                ExamResultsWidget::class,
                StatisticsChartWidgetByBSB::class,
                StatisticsChartWidgetByCHSB::class,
                StatisticsBySinfBSB::class,
                StatisticsBySinfCHSB::class,
                StatisticsByFanBSB::class,
                StatisticsByFanCHSB::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('O\'quv boshqaruvi')->icon('heroicon-o-book-open'),
                NavigationGroup::make('Imtihonlar boshqaruvi'),
                NavigationGroup::make('Hisobotlar'),
                NavigationGroup::make('Foydalanuvchilar boshqaruvi')
            ])
            ->databaseNotifications()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
