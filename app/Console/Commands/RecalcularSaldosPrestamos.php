<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Prestamo;

class RecalcularSaldosPrestamos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prestamos:recalcular-saldos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula el saldo de todos los préstamos según las cuotas pagadas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Recalculando saldos de préstamos...');
        
        $prestamos = Prestamo::where('estado_aprobacion', 'APROBADO')
            ->with('cuotas')
            ->get();
        
        $corregidos = 0;
        $cancelados = 0;
        
        foreach ($prestamos as $prestamo) {
            // Calcular saldo real: suma del capital de cuotas pendientes y vencidas
            $saldoReal = $prestamo->cuotas()
                ->whereIn('estado', ['PENDIENTE', 'VENCIDA'])
                ->sum('capital');
            
            $saldoReal = round($saldoReal, 2);
            $saldoAnterior = $prestamo->saldo;
            
            if (abs($saldoReal - $saldoAnterior) > 0.01) {
                $prestamo->update(['saldo' => $saldoReal]);
                
                // Si el saldo llegó a 0, marcar como cancelado
                if ($saldoReal <= 0.01) {
                    $prestamo->update([
                        'estado' => 'CANCELADO',
                        'saldo' => 0
                    ]);
                    $cancelados++;
                    $this->line("✓ Préstamo #{$prestamo->id}: Saldo \${$saldoAnterior} → \$0.00 - Estado: CANCELADO");
                } else {
                    $corregidos++;
                    $this->line("✓ Préstamo #{$prestamo->id}: Saldo \${$saldoAnterior} → \${$saldoReal}");
                }
            }
        }
        
        $this->newLine();
        $this->info('Recálculo completado:');
        $this->table(
            ['Tipo', 'Cantidad'],
            [
                ['Saldos corregidos', $corregidos],
                ['Préstamos cancelados', $cancelados],
                ['Total', $corregidos + $cancelados]
            ]
        );
        
        return Command::SUCCESS;
    }
}
