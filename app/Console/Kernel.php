<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $log = storage_path('logs/certificate-checks.log');

        $schedule->command('certificates:check-expiry')
            ->dailyAt('09:00')
            ->timezone('Asia/Tashkent')
            ->appendOutputTo($log)
            ->emailOutputOnFailure('your-email@domain.com'); // Optional

        // For testing - you can change this time to a few minutes from now
        $schedule->command('certificates:check-expiry --debug')
            ->dailyAt('18:00')
            ->timezone('Asia/Tashkent')
            ->appendOutputTo($log);
    }


    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
