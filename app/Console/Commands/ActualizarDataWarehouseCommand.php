<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ActualizarDataWarehouseCommand extends Command
{
    protected $signature = 'dw:actualizar-todo {--date= : Fecha específica (Y-m-d)}';
    protected $description = 'Actualizar todo el Data Warehouse (dimensión temporal, cartera, morosidad, KPIs)';

    public function handle()
    {
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('   ACTUALIZACIÓN DEL DATA WAREHOUSE - Sistema ATUK');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        $date = $this->option('date');
        $startTime = now();

        // 1. Poblar dimensión temporal
        $this->info('1. Poblando dimensión temporal...');
        Artisan::call('dw:poblar-dimension-temporal', [], $this->getOutput());
        $this->newLine();

        // 2. Actualizar cartera
        $this->info('2. Actualizando hechos de cartera...');
        $params = $date ? ['--date' => $date] : [];
        Artisan::call('dw:actualizar-cartera', $params, $this->getOutput());
        $this->newLine();

        // 3. Actualizar morosidad
        $this->info('3. Actualizando hechos de morosidad...');
        Artisan::call('dw:actualizar-morosidad', $params, $this->getOutput());
        $this->newLine();

        // 4. Actualizar rentabilidad
        $this->info('4. Actualizando hechos de rentabilidad...');
        Artisan::call('dw:actualizar-rentabilidad', $params, $this->getOutput());
        $this->newLine();

        // 5. Actualizar KPIs
        $this->info('5. Actualizando KPIs históricos...');
        Artisan::call('dw:actualizar-kpis', $params, $this->getOutput());
        $this->newLine();

        $endTime = now();
        $duration = $endTime->diffInSeconds($startTime);

        $this->info('═══════════════════════════════════════════════════════');
        $this->info("✓ Data Warehouse actualizado exitosamente");
        $this->info("  Tiempo de ejecución: {$duration} segundos");
        $this->info('═══════════════════════════════════════════════════════');

        return Command::SUCCESS;
    }
}
