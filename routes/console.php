<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar detección de mora diaria a las 00:01 AM
Schedule::command('mora:detectar')
    ->daily()
    ->at('00:01')
    ->withoutOverlapping()
    ->onOneServer();
