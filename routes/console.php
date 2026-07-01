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

// Programar generación de alertas diaria a las 00:15 AM
Schedule::command('alertas:generar')
    ->daily()
    ->at('00:15')
    ->withoutOverlapping()
    ->onOneServer();

// Programar envío de notificaciones diaria a las 08:00 AM y 18:00 PM
Schedule::command('notificaciones:enviar')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('notificaciones:enviar')
    ->dailyAt('18:00')
    ->withoutOverlapping()
    ->onOneServer();

// Programar actualización del Data Warehouse diaria a las 00:30 AM
Schedule::command('dw:actualizar-todo')
    ->daily()
    ->at('00:30')
    ->withoutOverlapping()
    ->onOneServer();
