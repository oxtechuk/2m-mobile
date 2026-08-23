<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule monthly payroll auto-calculation on the 1st of every month at midnight
Schedule::command('payroll:process-monthly')->monthlyOn(1, '00:00');

