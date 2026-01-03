<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schedule as ScheduleAlias;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('certificates:check-expiry')->everySixHours();
Schedule::command('imports:cleanup-failed-rows')->dailyAt('02:00');
