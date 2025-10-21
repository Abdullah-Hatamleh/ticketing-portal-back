<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\LogoutAllUsersJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// return function (Schedule $schedule) {
//     $schedule->job(new LogoutAllUsersJob)->dailyAt("07:36");
// };

Schedule::call(function() {
    LogoutAllUsersJob::dispatch();
})->dailyAt('00:00');
