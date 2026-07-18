<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
Schedule::command('amc:check-expiry')->daily();
Schedule::command('social:publish-scheduled')->everyMinute();
Schedule::command('campaigns:publish-scheduled')->everyMinute();

