<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AlertaRiesgo;
use App\Models\Prestamo;
use App\Models\Socio;
use App\Models\Cuota;
use App\Models\Parametro;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerarAlertasCommand extends Command
{
    protected $signature = 'alertas:generar';
    protected $description = 'Generar alertas de riesgo automáticamente';

    public function handle()
    {
        $this->info("Generando alertas de riesgo...");

        try {
            DB::beginTransaction();

            $alertasGeneradas = 0;

            // 1. ALERTAS DE MORA TEMPRANA (cuotas próximas a vencer)
            $alertasGeneradas += $this->alertasMoraTemprana();

            // 2. ALERTAS DE CONCENTRACIÓN DE CRÉDITO (socio con muchos préstamos)
            $alertasGeneradas += $this->alertasConcentracionCredito();

            // 3. ALERTAS DE CAPACIDAD DE PAGO (ingresos vs deuda)
            $alertasGeneradas += $this->alertasCapacidadPago();

            // 4. ALERTAS DE MOROSIDAD RECURRENTE
            $alertasGeneradas += $this->alertasMorosidadRecurrente();

            // 5. ALERTAS DE CARTERA GLOBAL
            $alertasGeneradas += $this->alertasCarteraGlobal();

            DB::commit();

            $this->info("✓ Alertas generadas: {$alertasGeneradas}");
            Log::info("Alertas de riesgo generadas: {$alertasGeneradas}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error al generar alertas: " . $e->getMessage());
            Log::error("Error al generar alertas: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Detectar cuotas próximas a vencer (5 días antes)
     */
    private function alertasMoraTemprana()
    {
        $diasAnticipacion = Parametro::where('clave', 'dias_anticipacion_notificacion')->value('valor') ?? 5;
        $fechaLimite = Carbon::today()->addDays($diasAnticipacion);

        $cuotasProximas = Cuota::where('estado', 'PENDIENTE')
            ->whereBetween('fecha_vencimiento', [Carbon::today(), $fechaLimite])
            ->with('prestamo.socio')
            ->get();

        $alertas = 0;
        foreach ($cuotasProximas as $cuota) {
            // Verificar si ya existe alerta similar reciente (últimas 24 horas)
            $existe = AlertaRiesgo::where('prestamo_id', $cuota->prestamo_id)
                ->where('tipo_alerta', 'MORA_TEMPRANA')
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->exists();

            if (!$existe) {
                AlertaRiesgo::create([
                    'socio_id' => $cuota->prestamo->socio_id,
                    'prestamo_id' => $cuota->prestamo_id,
                    'tipo_alerta' => 'MORA_TEMPRANA',
                    'nivel' => 'MEDIO',
                    'mensaje' => "Cuota #{$cuota->numero_cuota} próxima a vencer el {$cuota->fecha_vencimiento->format('d/m/Y')}. Monto: $" . number_format($cuota->monto, 2),
                    'leida' => false,
                    'fecha_alerta' => Carbon::now(),
                ]);
                $alertas++;
            }
        }

        return $alertas;
    }

    /**
     * Detectar socios con múltiples préstamos activos (concentración de riesgo)
     */
    private function alertasConcentracionCredito()
    {
        $socios = Socio::withCount(['prestamos' => function ($query) {
                $query->whereIn('estado', ['ACTIVO', 'VENCIDO']);
            }])
            ->having('prestamos_count', '>=', 3)
            ->get();

        $alertas = 0;
        foreach ($socios as $socio) {
            $montosActivos = Prestamo::where('socio_id', $socio->id)
                ->whereIn('estado', ['ACTIVO', 'VENCIDO'])
                ->sum('saldo');

            // No generar alerta si ya existe una reciente (últimos 7 días)
            $existe = AlertaRiesgo::where('socio_id', $socio->id)
                ->where('tipo_alerta', 'CONCENTRACION_CREDITO')
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->exists();

            if (!$existe) {
                AlertaRiesgo::create([
                    'socio_id' => $socio->id,
                    'prestamo_id' => null,
                    'tipo_alerta' => 'CONCENTRACION_CREDITO',
                    'nivel' => 'ALTO',
                    'mensaje' => "Socio con {$socio->prestamos_count} préstamos activos. Saldo total: $" . number_format($montosActivos, 2),
                    'leida' => false,
                    'fecha_alerta' => Carbon::now(),
                ]);
                $alertas++;
            }
        }

        return $alertas;
    }

    /**
     * Detectar socios cuya deuda supera el 40% de sus ingresos mensuales
     */
    private function alertasCapacidadPago()
    {
        $socios = Socio::where('estado', 'ACTIVO')
            ->where('ingresos_mensuales', '>', 0)
            ->get();

        $alertas = 0;
        foreach ($socios as $socio) {
            $cuotaMensual = Cuota::whereHas('prestamo', function ($query) use ($socio) {
                    $query->where('socio_id', $socio->id)
                        ->whereIn('estado', ['ACTIVO', 'VENCIDO']);
                })
                ->where('estado', 'PENDIENTE')
                ->whereYear('fecha_vencimiento', Carbon::now()->year)
                ->whereMonth('fecha_vencimiento', Carbon::now()->month)
                ->sum('monto');

            $ratio = ($cuotaMensual / $socio->ingresos_mensuales) * 100;

            if ($ratio > 40) {
                $existe = AlertaRiesgo::where('socio_id', $socio->id)
                    ->where('tipo_alerta', 'CAPACIDAD_PAGO')
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->exists();

                if (!$existe) {
                    AlertaRiesgo::create([
                        'socio_id' => $socio->id,
                        'prestamo_id' => null,
                        'tipo_alerta' => 'CAPACIDAD_PAGO',
                        'nivel' => 'ALTO',
                        'mensaje' => "Cuota mensual representa " . round($ratio, 1) . "% de ingresos (máximo recomendado: 40%). Cuota: $" . number_format($cuotaMensual, 2) . ", Ingresos: $" . number_format($socio->ingresos_mensuales, 2),
                        'leida' => false,
                        'fecha_alerta' => Carbon::now(),
                    ]);
                    $alertas++;
                }
            }
        }

        return $alertas;
    }

    /**
     * Detectar socios con historial de morosidad recurrente (2+ pagos atrasados en últimos 6 meses)
     */
    private function alertasMorosidadRecurrente()
    {
        $fechaLimite = Carbon::now()->subMonths(6);

        $sociosConMora = Cuota::where('estado', 'VENCIDA')
            ->whereHas('prestamo', function ($query) {
                $query->whereIn('estado', ['ACTIVO', 'VENCIDO']);
            })
            ->where('fecha_vencimiento', '>=', $fechaLimite)
            ->with('prestamo.socio')
            ->get()
            ->groupBy('prestamo.socio_id')
            ->filter(function ($cuotas) {
                return $cuotas->count() >= 2;
            });

        $alertas = 0;
        foreach ($sociosConMora as $socioId => $cuotas) {
            $existe = AlertaRiesgo::where('socio_id', $socioId)
                ->where('tipo_alerta', 'MOROSIDAD_RECURRENTE')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->exists();

            if (!$existe) {
                $socio = Socio::find($socioId);
                if ($socio) {
                    AlertaRiesgo::create([
                        'socio_id' => $socioId,
                        'prestamo_id' => null,
                        'tipo_alerta' => 'MOROSIDAD_RECURRENTE',
                        'nivel' => 'ALTO',
                        'mensaje' => "Socio con historial de morosidad: {$cuotas->count()} cuotas vencidas en últimos 6 meses",
                        'leida' => false,
                        'fecha_alerta' => Carbon::now(),
                    ]);
                    $alertas++;
                }
            }
        }

        return $alertas;
    }

    /**
     * Alertas de cartera global (índice de morosidad alto)
     */
    private function alertasCarteraGlobal()
    {
        $carteraTotal = Prestamo::whereIn('estado', ['ACTIVO', 'VENCIDO'])->sum('saldo');
        $carteraVencida = Prestamo::where('estado', 'VENCIDO')->sum('saldo');

        $indiceMorosidad = $carteraTotal > 0 ? ($carteraVencida / $carteraTotal) * 100 : 0;

        $alertas = 0;

        // Alerta si índice de morosidad > 10%
        if ($indiceMorosidad > 10) {
            $existe = AlertaRiesgo::where('tipo_alerta', 'INDICE_MOROSIDAD_ALTO')
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->exists();

            if (!$existe) {
                AlertaRiesgo::create([
                    'socio_id' => null,
                    'prestamo_id' => null,
                    'tipo_alerta' => 'INDICE_MOROSIDAD_ALTO',
                    'nivel' => $indiceMorosidad > 20 ? 'CRITICO' : 'ALTO',
                    'mensaje' => "Índice de morosidad de cartera: " . round($indiceMorosidad, 2) . "%. Cartera vencida: $" . number_format($carteraVencida, 2),
                    'leida' => false,
                    'fecha_alerta' => Carbon::now(),
                ]);
                $alertas++;
            }
        }

        return $alertas;
    }
}
