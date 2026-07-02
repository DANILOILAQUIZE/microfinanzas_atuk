<?php

namespace App\Console\Commands;

use App\Models\Prestamo;
use App\Models\Cuota;
use App\Models\Socio;
use App\Models\TipoPrestamo;
use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerarPrestamoConMora extends Command
{
    protected $signature = 'prestamo:generar-mora {socio_id} {meses_atras=3}';
    protected $description = 'Generar un préstamo con fecha antigua y cuotas vencidas para testing de mora';

    public function handle()
    {
        $socioId = $this->argument('socio_id');
        $mesesAtras = $this->argument('meses_atras');

        $this->info('🔧 Generando préstamo con mora para testing...');
        $this->newLine();

        try {
            // Verificar que el socio existe
            $socio = Socio::findOrFail($socioId);
            $this->info("✓ Socio encontrado: {$socio->nombres} {$socio->apellidos}");

            // Obtener un tipo de préstamo
            $tipoPrestamo = TipoPrestamo::first();
            if (!$tipoPrestamo) {
                $this->error('❌ No hay tipos de préstamo configurados');
                return Command::FAILURE;
            }
            $this->info("✓ Tipo de préstamo: {$tipoPrestamo->nombre}");

            // Obtener usuario administrador
            $usuario = Usuario::first();
            if (!$usuario) {
                $this->error('❌ No hay usuarios en el sistema');
                return Command::FAILURE;
            }

            DB::beginTransaction();

            // Calcular fechas
            $fechaSolicitud = Carbon::now()->subMonths($mesesAtras)->subDays(5);
            $fechaDesembolso = Carbon::now()->subMonths($mesesAtras);
            $fechaPrimerPago = Carbon::now()->subMonths($mesesAtras)->addMonth();
            
            $this->newLine();
            $this->info("📅 Fechas calculadas:");
            $this->line("   Solicitud: {$fechaSolicitud->format('d/m/Y')}");
            $this->line("   Desembolso: {$fechaDesembolso->format('d/m/Y')}");
            $this->line("   Primer pago: {$fechaPrimerPago->format('d/m/Y')}");

            // Datos del préstamo
            $monto = 1000.00;
            $plazo = 6; // 6 meses
            $tasaInteres = 20.00; // 20% anual
            $diaVencimiento = 20; // Vence el día 20 de cada mes

            // Calcular interés y monto total
            $interes = ($monto * ($tasaInteres / 100) * $plazo) / 12;
            $montoTotal = $monto + $interes;
            $montoCuota = $montoTotal / $plazo;

            $this->newLine();
            $this->info("💰 Datos del préstamo:");
            $this->line("   Monto: $" . number_format($monto, 2));
            $this->line("   Interés: $" . number_format($interes, 2));
            $this->line("   Monto total: $" . number_format($montoTotal, 2));
            $this->line("   Cuota mensual: $" . number_format($montoCuota, 2));
            $this->line("   Plazo: {$plazo} meses");
            $this->line("   Día de vencimiento: {$diaVencimiento}");

            // Crear préstamo
            $prestamo = Prestamo::create([
                'socio_id' => $socio->id,
                'tipo_prestamo_id' => $tipoPrestamo->id,
                'usuario_id' => $usuario->id,
                'usuario_aprobador_id' => $usuario->id,
                'fecha_solicitud' => $fechaSolicitud,
                'fecha_desembolso' => $fechaDesembolso,
                'fecha_aprobacion' => $fechaDesembolso,
                'monto' => $monto,
                'interes' => $interes,
                'monto_total' => $montoTotal,
                'monto_cuota' => $montoCuota,
                'plazo' => $plazo,
                'saldo' => $montoTotal,
                'estado' => 'ACTIVO',
                'estado_aprobacion' => 'APROBADO',
                'tasa_interes' => $tasaInteres,
                'dia_vencimiento' => $diaVencimiento,
            ]);

            $this->newLine();
            $this->info("✓ Préstamo creado con ID: {$prestamo->id}");
            $this->newLine();

            // Generar cuotas
            $this->info("📋 Generando cuotas:");
            $this->newLine();

            $saldoPendiente = $montoTotal;
            $capitalPorCuota = $monto / $plazo;
            $interesPorCuota = $interes / $plazo;

            $cuotasVencidas = 0;
            $cuotasPendientes = 0;

            for ($i = 1; $i <= $plazo; $i++) {
                // Calcular fecha de vencimiento
                $fechaVencimiento = $fechaPrimerPago->copy()->addMonths($i - 1);
                
                // Ajustar al día de vencimiento especificado
                $ultimoDiaMes = $fechaVencimiento->daysInMonth;
                $diaAjustado = min($diaVencimiento, $ultimoDiaMes);
                $fechaVencimiento->day = $diaAjustado;

                // Calcular montos
                $capitalCuota = ($i == $plazo) ? $saldoPendiente - $interesPorCuota : $capitalPorCuota;
                $montoCuotaActual = $capitalCuota + $interesPorCuota;
                $saldoPendiente -= $montoCuotaActual;

                // Determinar estado según fecha
                $estado = 'PENDIENTE';
                if ($fechaVencimiento->isPast()) {
                    $estado = 'VENCIDA';
                    $cuotasVencidas++;
                } else {
                    $cuotasPendientes++;
                }

                Cuota::create([
                    'prestamo_id' => $prestamo->id,
                    'numero_cuota' => $i,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'monto' => $montoCuotaActual,
                    'capital' => $capitalCuota,
                    'interes' => $interesPorCuota,
                    'mora' => 0,
                    'saldo_pendiente' => max(0, $saldoPendiente),
                    'estado' => $estado,
                ]);

                $icon = $estado === 'VENCIDA' ? '❌' : '✓';
                $color = $estado === 'VENCIDA' ? 'error' : 'info';
                
                if ($estado === 'VENCIDA') {
                    $diasVencidos = $fechaVencimiento->diffInDays(Carbon::now());
                    $this->line("   {$icon} Cuota {$i}: Vence {$fechaVencimiento->format('d/m/Y')} - <fg=red>VENCIDA ({$diasVencidos} días)</>");
                } else {
                    $this->line("   {$icon} Cuota {$i}: Vence {$fechaVencimiento->format('d/m/Y')} - <fg=green>PENDIENTE</>");
                }
            }

            DB::commit();

            $this->newLine();
            $this->info("═══════════════════════════════════════════════════════");
            $this->info("✅ Préstamo generado exitosamente");
            $this->info("═══════════════════════════════════════════════════════");
            $this->newLine();
            $this->line("📊 Resumen:");
            $this->line("   • Préstamo ID: {$prestamo->id}");
            $this->line("   • Socio: {$socio->nombres} {$socio->apellidos}");
            $this->line("   • Monto: $" . number_format($montoTotal, 2));
            $this->line("   • Plazo: {$plazo} cuotas");
            $this->line("   • <fg=red>Cuotas vencidas: {$cuotasVencidas}</>");
            $this->line("   • <fg=green>Cuotas pendientes: {$cuotasPendientes}</>");
            $this->newLine();
            $this->info("💡 Ahora puedes:");
            $this->line("   1. Ejecutar: php artisan mora:detectar");
            $this->line("   2. Ver el préstamo en la web y aplicar pagos");
            $this->line("   3. Probar alertas y notificaciones");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
