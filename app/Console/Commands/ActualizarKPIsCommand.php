<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KpiHistorico;
use App\Models\Prestamo;
use App\Models\Socio;
use App\Models\CuentaAhorro;
use App\Models\Cuota;
use App\Models\Pago;
use App\Models\MovimientoAhorro;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActualizarKPIsCommand extends Command
{
    protected $signature = 'dw:actualizar-kpis {--date= : Fecha específica (Y-m-d)}';
    protected $description = 'Actualizar KPIs históricos en el Data Warehouse';

    public function handle()
    {
        $fecha = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
        
        $this->info("Actualizando KPIs para: {$fecha->toDateString()}");

        try {
            DB::beginTransaction();

            // Eliminar registros existentes de esta fecha
            KpiHistorico::where('fecha', $fecha->toDateString())->delete();

            // Calcular todos los KPIs
            $kpis = $this->calcularKPIs($fecha);

            // Guardar snapshot
            KpiHistorico::create(array_merge([
                'fecha' => $fecha->toDateString(),
            ], $kpis));

            DB::commit();

            $this->info("✓ KPIs actualizados");
            $this->line("  → Cartera total: $" . number_format($kpis['cartera_total'], 2));
            $this->line("  → Socios activos: {$kpis['socios_activos']}");
            $this->line("  → Índice de morosidad: {$kpis['indice_morosidad']}%");
            $this->line("  → Saldo en ahorro: $" . number_format($kpis['saldo_ahorro'], 2));

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error al actualizar KPIs: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function calcularKPIs($fecha)
    {
        // Cartera
        $carteraTotal = Prestamo::whereIn('estado', ['ACTIVO', 'VENCIDO'])
            ->whereDate('fecha_desembolso', '<=', $fecha)
            ->sum('saldo');

        $carteraVencida = Prestamo::where('estado', 'VENCIDO')
            ->whereDate('fecha_desembolso', '<=', $fecha)
            ->sum('saldo');

        $totalPrestamos = Prestamo::whereIn('estado', ['ACTIVO', 'VENCIDO'])
            ->whereDate('fecha_desembolso', '<=', $fecha)
            ->count();

        $prestamosPendientes = Prestamo::where('estado_aprobacion', 'PENDIENTE')
            ->whereDate('created_at', '<=', $fecha)
            ->count();

        // Socios
        $sociosActivos = Socio::where('estado', 'ACTIVO')
            ->whereDate('created_at', '<=', $fecha)
            ->count();

        $sociosTotales = Socio::whereDate('created_at', '<=', $fecha)->count();

        // Morosidad
        $indiceMorosidad = $carteraTotal > 0 ? ($carteraVencida / $carteraTotal) * 100 : 0;

        $cuotasVencidas = Cuota::where('estado', 'VENCIDA')
            ->whereDate('fecha_vencimiento', '<=', $fecha)
            ->count();

        $montoMoraTotal = Cuota::where('estado', 'VENCIDA')
            ->whereDate('fecha_vencimiento', '<=', $fecha)
            ->sum('mora');

        // Ahorro
        $saldoAhorro = CuentaAhorro::where('estado', 'ACTIVA')
            ->whereDate('created_at', '<=', $fecha)
            ->sum('saldo');

        $cuentasAhorro = CuentaAhorro::where('estado', 'ACTIVA')
            ->whereDate('created_at', '<=', $fecha)
            ->count();

        // Pagos del mes
        $pagosMes = Pago::whereYear('fecha_pago', $fecha->year)
            ->whereMonth('fecha_pago', $fecha->month)
            ->whereDate('fecha_pago', '<=', $fecha)
            ->count();

        $montoPagosMes = Pago::whereYear('fecha_pago', $fecha->year)
            ->whereMonth('fecha_pago', $fecha->month)
            ->whereDate('fecha_pago', '<=', $fecha)
            ->sum('monto');

        // Movimientos del mes
        $movimientosMes = MovimientoAhorro::whereYear('fecha_movimiento', $fecha->year)
            ->whereMonth('fecha_movimiento', $fecha->month)
            ->whereDate('fecha_movimiento', '<=', $fecha)
            ->count();

        $depositosMes = MovimientoAhorro::where('tipo_movimiento', 'DEPOSITO')
            ->whereYear('fecha_movimiento', $fecha->year)
            ->whereMonth('fecha_movimiento', $fecha->month)
            ->whereDate('fecha_movimiento', '<=', $fecha)
            ->sum('monto');

        $retirosMes = MovimientoAhorro::where('tipo_movimiento', 'RETIRO')
            ->whereYear('fecha_movimiento', $fecha->year)
            ->whereMonth('fecha_movimiento', $fecha->month)
            ->whereDate('fecha_movimiento', '<=', $fecha)
            ->sum('monto');

        // Aprobaciones del mes
        $prestamosAprobadosMes = Prestamo::where('estado_aprobacion', 'APROBADO')
            ->whereYear('fecha_aprobacion', $fecha->year)
            ->whereMonth('fecha_aprobacion', $fecha->month)
            ->whereDate('fecha_aprobacion', '<=', $fecha)
            ->count();

        return [
            'cartera_total' => $carteraTotal,
            'cartera_vencida' => $carteraVencida,
            'total_prestamos' => $totalPrestamos,
            'prestamos_pendientes' => $prestamosPendientes,
            'prestamos_aprobados_mes' => $prestamosAprobadosMes,
            'socios_activos' => $sociosActivos,
            'socios_totales' => $sociosTotales,
            'indice_morosidad' => round($indiceMorosidad, 2),
            'cuotas_vencidas' => $cuotasVencidas,
            'monto_mora_total' => $montoMoraTotal,
            'saldo_ahorro' => $saldoAhorro,
            'cuentas_ahorro' => $cuentasAhorro,
            'pagos_mes' => $pagosMes,
            'monto_pagos_mes' => $montoPagosMes,
            'movimientos_mes' => $movimientosMes,
            'depositos_mes' => $depositosMes,
            'retiros_mes' => $retirosMes,
        ];
    }
}
