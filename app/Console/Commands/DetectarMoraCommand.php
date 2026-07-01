<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cuota;
use App\Models\Parametro;
use App\Models\Prestamo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetectarMoraCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mora:detectar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detecta cuotas vencidas y calcula automáticamente la mora según parámetros configurados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando detección de mora...');
        
        // Obtener parámetros de mora
        $diasGracia = (int) Parametro::where('clave', 'dias_gracia_mora')->value('valor') ?? 3;
        $tasaMora = (float) Parametro::where('clave', 'tasa_mora_mensual')->value('valor') ?? 2.5;
        $morasobreCapital = Parametro::where('clave', 'mora_sobre_capital')->value('valor') === 'true';
        
        $this->info("Parámetros: Días gracia={$diasGracia}, Tasa mora={$tasaMora}%, Sobre capital=" . ($morasobreCapital ? 'Sí' : 'No'));
        
        // Fecha límite considerando días de gracia
        $fechaLimite = Carbon::now()->subDays($diasGracia);
        
        // Obtener cuotas pendientes que han vencido (considerando días de gracia)
        $cuotasVencidas = Cuota::with(['prestamo'])
            ->where('estado', 'PENDIENTE')
            ->whereDate('fecha_vencimiento', '<', $fechaLimite)
            ->get();
        
        $totalCuotas = $cuotasVencidas->count();
        $this->info("Se encontraron {$totalCuotas} cuotas vencidas");
        
        if ($totalCuotas === 0) {
            $this->info('No hay cuotas en mora');
            return 0;
        }
        
        $cuotasActualizadas = 0;
        $prestamosActualizados = [];
        
        DB::beginTransaction();
        try {
            foreach ($cuotasVencidas as $cuota) {
                // Calcular días de mora (sin incluir días de gracia)
                $diasMora = Carbon::parse($cuota->fecha_vencimiento)->diffInDays(Carbon::now()) - $diasGracia;
                
                if ($diasMora > 0) {
                    // Calcular mora
                    $baseCalculo = $morasobreCapital ? $cuota->capital : $cuota->monto;
                    
                    // Mora = base * (tasa_mensual / 30) * días_mora
                    $mora = $baseCalculo * ($tasaMora / 100 / 30) * $diasMora;
                    
                    // Actualizar cuota
                    $cuota->update([
                        'estado' => 'VENCIDA',
                        'mora' => round($mora, 2),
                    ]);
                    
                    $cuotasActualizadas++;
                    
                    // Marcar para actualizar el estado del préstamo
                    if (!in_array($cuota->prestamo_id, $prestamosActualizados)) {
                        $prestamosActualizados[] = $cuota->prestamo_id;
                    }
                    
                    $this->line("Cuota #{$cuota->id} - Préstamo #{$cuota->prestamo_id}: {$diasMora} días mora, penalización: $" . number_format($mora, 2));
                }
            }
            
            // Actualizar el estado de los préstamos con cuotas vencidas
            foreach ($prestamosActualizados as $prestamoId) {
                $prestamo = Prestamo::find($prestamoId);
                if ($prestamo && $prestamo->estado === 'ACTIVO') {
                    $prestamo->update(['estado' => 'VENCIDO']);
                    $this->warn("Préstamo #{$prestamoId} marcado como VENCIDO");
                }
            }
            
            DB::commit();
            
            $this->info("✓ Proceso completado: {$cuotasActualizadas} cuotas actualizadas, " . count($prestamosActualizados) . " préstamos marcados como vencidos");
            
            // Registrar en log
            Log::info("Detección de mora ejecutada: {$cuotasActualizadas} cuotas actualizadas");
            
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error al procesar mora: ' . $e->getMessage());
            Log::error('Error en detección de mora: ' . $e->getMessage());
            return 1;
        }
    }
}
