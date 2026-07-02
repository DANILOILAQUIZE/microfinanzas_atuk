<?php

namespace App\Console\Commands;

use App\Models\HechoRentabilidad;
use App\Models\DimensionTemporal;
use App\Models\TipoPrestamo;
use App\Models\Pago;
use App\Models\Prestamo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActualizarRentabilidadCommand extends Command
{
    protected $signature = 'dw:actualizar-rentabilidad {--date= : Fecha específica (Y-m-d)}';
    protected $description = 'Actualizar hechos de rentabilidad en el Data Warehouse';

    public function handle()
    {
        $this->info('🔄 Actualizando hechos de rentabilidad...');
        $this->newLine();

        try {
            $fecha = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
            
            $this->info("📅 Fecha de procesamiento: {$fecha->format('d/m/Y')}");
            $this->newLine();

            // Obtener dimensión temporal
            $dimensionTemporal = DimensionTemporal::where('fecha', $fecha->format('Y-m-d'))->first();
            
            if (!$dimensionTemporal) {
                $this->error('❌ No existe la dimensión temporal para esta fecha. Ejecuta primero: php artisan dw:poblar-dimension-temporal');
                return Command::FAILURE;
            }

            // Obtener todos los tipos de préstamo
            $tiposPrestamo = TipoPrestamo::all();
            
            if ($tiposPrestamo->isEmpty()) {
                $this->warn('⚠️  No hay tipos de préstamo configurados');
                return Command::SUCCESS;
            }

            $bar = $this->output->createProgressBar($tiposPrestamo->count());
            $registrosActualizados = 0;

            foreach ($tiposPrestamo as $tipoPrestamo) {
                // Calcular ingresos por intereses (de cuotas pagadas en la fecha)
                // La relación es: Pago -> Cuota -> Prestamo
                $interesesGanados = Pago::whereDate('pagos.fecha_pago', $fecha)
                    ->whereHas('cuota.prestamo', function($query) use ($tipoPrestamo) {
                        $query->where('tipo_prestamo_id', $tipoPrestamo->id);
                    })
                    ->join('cuotas', 'pagos.cuota_id', '=', 'cuotas.id')
                    ->sum('cuotas.interes');

                // Calcular mora ganada (de cuotas pagadas con mora)
                $moraGanada = Pago::whereDate('pagos.fecha_pago', $fecha)
                    ->whereHas('cuota.prestamo', function($query) use ($tipoPrestamo) {
                        $query->where('tipo_prestamo_id', $tipoPrestamo->id);
                    })
                    ->join('cuotas', 'pagos.cuota_id', '=', 'cuotas.id')
                    ->where('cuotas.mora', '>', 0)
                    ->sum('cuotas.mora');

                // Comisiones (si tienes comisiones por desembolso o gestión)
                $comisionesGanadas = 0; // Puedes agregar lógica aquí si aplica

                // Calcular totales
                $ingresosTotales = $interesesGanados + $moraGanada + $comisionesGanadas;
                
                // Costos operativos (estimado como % de los ingresos)
                // Ajusta según tus datos reales de gastos administrativos
                $costosOperativos = $ingresosTotales * 0.30; // 30% de gastos operativos

                // Rentabilidad neta
                $rentabilidadNeta = $ingresosTotales - $costosOperativos;

                // ROI (Return on Investment) - Rentabilidad sobre cartera activa
                $carteraActiva = Prestamo::where('tipo_prestamo_id', $tipoPrestamo->id)
                    ->whereIn('estado', ['ACTIVO', 'VENCIDO'])
                    ->sum('saldo');
                
                $roi = $carteraActiva > 0 ? ($rentabilidadNeta / $carteraActiva) * 100 : 0;

                // Actualizar o crear registro
                HechoRentabilidad::updateOrCreate(
                    [
                        'dimension_temporal_id' => $dimensionTemporal->id,
                        'tipo_prestamo_id' => $tipoPrestamo->id,
                    ],
                    [
                        'intereses_ganados' => $interesesGanados,
                        'mora_ganada' => $moraGanada,
                        'comisiones_ganadas' => $comisionesGanadas,
                        'ingresos_totales' => $ingresosTotales,
                        'costos_operativos' => $costosOperativos,
                        'rentabilidad_neta' => $rentabilidadNeta,
                        'roi' => $roi,
                    ]
                );

                $registrosActualizados++;
                $bar->advance();

                if ($ingresosTotales > 0 || $carteraActiva > 0) {
                    $this->newLine();
                    $this->line("  ✓ {$tipoPrestamo->nombre}:");
                    $this->line("    Intereses ganados: $" . number_format($interesesGanados, 2));
                    $this->line("    Mora ganada: $" . number_format($moraGanada, 2));
                    $this->line("    Ingresos totales: $" . number_format($ingresosTotales, 2));
                    $this->line("    Rentabilidad neta: $" . number_format($rentabilidadNeta, 2));
                    $this->line("    ROI: " . number_format($roi, 2) . "%");
                }
            }

            $bar->finish();
            $this->newLine(2);

            $this->info("✅ Se actualizaron {$registrosActualizados} registros de rentabilidad");
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error al actualizar rentabilidad: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
