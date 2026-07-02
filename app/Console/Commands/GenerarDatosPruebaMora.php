<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Socio;
use App\Models\CuentaAhorro;
use App\Models\Prestamo;
use App\Models\TipoPrestamo;
use App\Models\Cuota;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerarDatosPruebaMora extends Command
{
    protected $signature = 'datos:generar-mora';
    protected $description = 'Genera 4 socios con préstamos en mora para pruebas';

    public function handle()
    {
        $this->info('🔄 Generando datos de prueba con mora...');
        
        DB::beginTransaction();
        try {
            // Obtener un tipo de préstamo existente
            $tipoPrestamo = TipoPrestamo::where('estado', 'ACTIVO')->first();
            if (!$tipoPrestamo) {
                $this->error('No hay tipos de préstamo activos. Crea uno primero.');
                return Command::FAILURE;
            }
            
            $socios = [
                [
                    'nombre' => 'María Rodríguez',
                    'cedula' => '0987654321',
                    'meses_atras' => 3,
                    'monto' => 1000,
                    'descripcion' => '3 meses de atraso (mora alta)',
                ],
                [
                    'nombre' => 'Pedro Sánchez',
                    'cedula' => '0912345678',
                    'meses_atras' => 2,
                    'monto' => 800,
                    'descripcion' => '2 meses de atraso (mora media)',
                ],
                [
                    'nombre' => 'Laura González',
                    'cedula' => '0923456789',
                    'meses_atras' => 1,
                    'monto' => 600,
                    'descripcion' => '1 mes de atraso (mora baja)',
                ],
                [
                    'nombre' => 'Jorge Martínez',
                    'cedula' => '0934567890',
                    'meses_atras' => 0,
                    'monto' => 500,
                    'descripcion' => 'Sin atraso (para comparar)',
                ],
            ];
            
            foreach ($socios as $dataSocio) {
                // 1. Crear el socio
                $socio = Socio::create([
                    'nombres' => explode(' ', $dataSocio['nombre'])[0],
                    'apellidos' => explode(' ', $dataSocio['nombre'])[1] ?? '',
                    'cedula' => $dataSocio['cedula'],
                    'fecha_nacimiento' => '1990-01-15',
                    'telefono' => '099' . rand(1000000, 9999999),
                    'correo' => strtolower(str_replace(' ', '.', $dataSocio['nombre'])) . '@test.com',
                    'direccion' => 'Dirección de prueba',
                    'estado' => 'ACTIVO',
                ]);
                
                // 2. Crear cuenta de ahorro
                $cuenta = CuentaAhorro::create([
                    'socio_id' => $socio->id,
                    'numero_cuenta' => 'AH' . str_pad($socio->id, 8, '0', STR_PAD_LEFT),
                    'deposito_inicial' => 50,
                    'saldo' => 500,
                    'saldo_bloqueado' => 50,
                    'fecha_apertura' => now()->subMonths(6),
                    'estado' => 'ACTIVA',
                ]);
                
                // 3. Crear préstamo con fecha antigua
                $mesesAtras = $dataSocio['meses_atras'];
                $fechaSolicitud = Carbon::now()->subMonths($mesesAtras + 6);
                $fechaDesembolso = $fechaSolicitud->copy()->addDays(1);
                
                $monto = $dataSocio['monto'];
                $plazo = 6; // 6 meses
                $tasaInteres = $tipoPrestamo->interes / 100; // Convertir a decimal
                $interesTotal = $monto * $tasaInteres;
                $montoTotal = $monto + $interesTotal;
                $montoCuota = round($montoTotal / $plazo, 2);
                
                $prestamo = Prestamo::create([
                    'socio_id' => $socio->id,
                    'tipo_prestamo_id' => $tipoPrestamo->id,
                    'usuario_id' => 1, // Admin
                    'usuario_aprobador_id' => 1,
                    'fecha_solicitud' => $fechaSolicitud,
                    'fecha_desembolso' => $fechaDesembolso,
                    'monto' => $monto,
                    'monto_total' => $montoTotal,
                    'monto_cuota' => $montoCuota,
                    'interes' => $tipoPrestamo->interes,
                    'plazo' => $plazo,
                    'saldo' => $montoTotal,
                    'estado' => 'ACTIVO',
                    'estado_aprobacion' => 'APROBADO',
                    'fecha_aprobacion' => $fechaDesembolso,
                ]);
                
                // 4. Generar cuotas con fechas antiguas
                $capitalPorCuota = round($monto / $plazo, 2);
                $interesPorCuota = round($interesTotal / $plazo, 2);
                $saldoPendiente = $montoTotal;
                $fechaPrimeraCuota = $fechaDesembolso->copy()->addMonth();
                
                for ($i = 1; $i <= $plazo; $i++) {
                    $fechaVencimiento = $fechaPrimeraCuota->copy()->addMonths($i - 1);
                    
                    // Última cuota ajusta redondeo
                    if ($i === $plazo) {
                        $capitalActual = $monto - ($capitalPorCuota * ($plazo - 1));
                        $montoCuotaActual = $saldoPendiente;
                    } else {
                        $capitalActual = $capitalPorCuota;
                        $montoCuotaActual = $montoCuota;
                    }
                    
                    // Si la cuota ya venció, marcarla como VENCIDA (pero sin mora aún)
                    $estado = $fechaVencimiento->isPast() ? 'VENCIDA' : 'PENDIENTE';
                    
                    Cuota::create([
                        'prestamo_id' => $prestamo->id,
                        'numero_cuota' => $i,
                        'fecha_vencimiento' => $fechaVencimiento,
                        'monto' => round($montoCuotaActual, 2),
                        'capital' => round($capitalActual, 2),
                        'interes' => $interesPorCuota,
                        'mora' => 0, // La mora se calculará al hacer clic en "Detectar Mora"
                        'saldo_pendiente' => round($saldoPendiente, 2),
                        'estado' => $estado,
                    ]);
                    
                    $saldoPendiente -= $montoCuotaActual;
                }
                
                // Actualizar el saldo del préstamo
                $capitalPendiente = Cuota::where('prestamo_id', $prestamo->id)
                    ->whereIn('estado', ['PENDIENTE', 'VENCIDA'])
                    ->sum('capital');
                
                $prestamo->update([
                    'saldo' => $capitalPendiente,
                    'estado' => $mesesAtras > 0 ? 'VENCIDO' : 'ACTIVO',
                ]);
                
                $cuotasVencidas = Cuota::where('prestamo_id', $prestamo->id)
                    ->where('estado', 'VENCIDA')
                    ->count();
                
                $this->info("✓ {$dataSocio['nombre']}: Préstamo \${$monto} - {$cuotasVencidas} cuotas vencidas ({$dataSocio['descripcion']})");
            }
            
            DB::commit();
            
            $this->newLine();
            $this->info('✅ Datos generados exitosamente!');
            $this->newLine();
            $this->info('📋 Resumen:');
            $this->info('   - 4 socios creados con cuentas de ahorro');
            $this->info('   - 4 préstamos aprobados con diferentes niveles de mora');
            $this->info('   - Las cuotas están marcadas como VENCIDAS pero SIN MORA calculada');
            $this->newLine();
            $this->warn('⚡ Ahora haz clic en el botón "Detectar Mora" en la interfaz para calcular la mora de las cuotas vencidas.');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
