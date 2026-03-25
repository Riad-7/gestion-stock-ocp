<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Exécuter chaque nuit à minuit
Schedule::command('stock:verifier-expirations')
         ->dailyAt('00:00')
         ->withoutOverlapping()   // évite les doublons
         ->sendOutputTo(storage_path('logs/expirations.log'));

// Ajouter d'autres tâches planifiées :
Schedule::command('queue:prune-failed')->weekly();
