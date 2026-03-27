<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('stock:verifier-expirations')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->sendOutputTo(storage_path('logs/expirations.log'));

Schedule::command('backup:database')
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/database-backup.log'));

Schedule::command('queue:prune-failed')->weekly();
