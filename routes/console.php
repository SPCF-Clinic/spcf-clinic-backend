<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Runs every second, per the design: sub-minute scheduling only actually
// fires while `php artisan schedule:work` (or an equivalent continuous
// `schedule:run` loop) is running as a persistent background process —
// a standard once-a-minute crontab entry cannot invoke this more than
// once a minute, so schedule:work needs to be kept alive on whichever
// machine runs this (e.g. via a system service / process supervisor).
Schedule::command('beds:check-timers')->everySecond();