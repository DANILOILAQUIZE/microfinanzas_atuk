<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerarHistorialDW extends Command
{
    protected $signature = 'dw:generar-historial {--meses=6 : Número de meses hacia atrás}';
    protected $description = 'Genera datos históricos del Data Warehouse para los últimos N meses';

    public function handle()
    {
        $meses = (int) $this->option('meses');
        
        $this->info("╔════════════════════════════════════════════════════════╗");
        $this->info("║  Generando historial del Data Warehouse ({$meses} meses)  ║");
        $this->info("╚════════════════════════════════════════════════════════╝");
        $this->newLine();
        
        $fechaInicio = Carbon::now()->subMonths($meses)->startOfMonth();
        $fechaActual = Carbon::now();
        
        $this->info("📅 Rango: {$fechaInicio->format('d/m/Y')} → {$fechaActual->format('d/m/Y')}");
        $this->newLine();
        
        // Generar para el primer día de cada mes
        $fecha = $fechaInicio->copy();
        $totalRegistros = 0;
        
        $this->withProgressBar($meses, function () use (&$fecha, &$totalRegistros, $fechaActual) {
            while ($fecha->lte($fechaActual)) {
                // Actualizar cartera
                $this->call('dw:actualizar-cartera', [
                    '--date' => $fecha->format('Y-m-d')
                ]);
                
                // Actualizar morosidad
                $this->call('dw:actualizar-morosidad', [
                    '--date' => $fecha->format('Y-m-d')
                ]);
                
                // Actualizar rentabilidad
                $this->call('dw:actualizar-rentabilidad', [
                    '--date' => $fecha->format('Y-m-d')
                ]);
                
                // Actualizar KPIs
                $this->call('dw:actualizar-kpis', [
                    '--date' => $fecha->format('Y-m-d')
                ]);
                
                $totalRegistros++;
                $fecha->addMonth();
            }
        });
        
        $this->newLine(2);
        $this->info("✅ Historial generado exitosamente");
        $this->info("   Total de periodos procesados: {$totalRegistros}");
        
        return 0;
    }
}
