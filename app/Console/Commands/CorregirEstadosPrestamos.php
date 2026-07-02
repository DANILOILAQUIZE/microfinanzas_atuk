<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Prestamo;

class CorregirEstadosPrestamos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prestamos:corregir-estados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige los estados de los préstamos según su saldo actual';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Corrigiendo estados de préstamos...');
        
        // 1. Préstamos con saldo 0 o negativo deben estar CANCELADOS
        $prestamosConSaldoCero = Prestamo::where('estado_aprobacion', 'APROBADO')
            ->where(function($query) {
                $query->where('saldo', '<=', 0.01)
                      ->orWhereNull('saldo');
            })
            ->where('estado', '!=', 'CANCELADO')
            ->get();
        
        $cancelados = 0;
        foreach ($prestamosConSaldoCero as $prestamo) {
            $prestamo->update([
                'estado' => 'CANCELADO',
                'saldo' => 0
            ]);
            $cancelados++;
            $this->line("✓ Préstamo #{$prestamo->id} marcado como CANCELADO (saldo: $" . number_format($prestamo->saldo, 2) . ")");
        }
        
        // 2. Préstamos con cuotas vencidas deben estar VENCIDOS
        $prestamosConCuotasVencidas = Prestamo::where('estado_aprobacion', 'APROBADO')
            ->where('saldo', '>', 0.01)
            ->where('estado', '!=', 'VENCIDO')
            ->whereHas('cuotas', function($query) {
                $query->where('estado', 'VENCIDA');
            })
            ->get();
        
        $vencidos = 0;
        foreach ($prestamosConCuotasVencidas as $prestamo) {
            $cuotasVencidas = $prestamo->cuotas()->where('estado', 'VENCIDA')->count();
            $prestamo->update(['estado' => 'VENCIDO']);
            $vencidos++;
            $this->line("✓ Préstamo #{$prestamo->id} marcado como VENCIDO ({$cuotasVencidas} cuotas vencidas)");
        }
        
        // 3. Préstamos sin cuotas vencidas y con saldo > 0 deben estar ACTIVOS
        $prestamosActivos = Prestamo::where('estado_aprobacion', 'APROBADO')
            ->where('saldo', '>', 0.01)
            ->whereDoesntHave('cuotas', function($query) {
                $query->where('estado', 'VENCIDA');
            })
            ->where('estado', 'VENCIDO')
            ->get();
        
        $activados = 0;
        foreach ($prestamosActivos as $prestamo) {
            $prestamo->update(['estado' => 'ACTIVO']);
            $activados++;
            $this->line("✓ Préstamo #{$prestamo->id} marcado como ACTIVO (sin cuotas vencidas)");
        }
        
        $this->newLine();
        $this->info('Corrección completada:');
        $this->table(
            ['Estado', 'Cantidad'],
            [
                ['Cancelados', $cancelados],
                ['Vencidos', $vencidos],
                ['Activados', $activados],
                ['Total', $cancelados + $vencidos + $activados]
            ]
        );
        
        return Command::SUCCESS;
    }
}
