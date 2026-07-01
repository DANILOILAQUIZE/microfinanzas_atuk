<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Socio;
use App\Models\CuentaAhorro;
use App\Models\Cuota;
use App\Models\Pago;
use App\Models\MovimientoAhorro;
use App\Models\TipoPrestamo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs principales
        $kpis = $this->getKPIs();
        
        // Datos para gráficas
        $evolucionCartera = $this->getEvolucionCartera();
        $distribucionTipos = $this->getDistribucionTipos();
        $morosidadMensual = $this->getMorosidadMensual();
        $crecimientoAhorro = $this->getCrecimientoAhorro();
        
        // Tablas de resumen
        $topSocios = $this->getTopSocios();
        $prestamosRecientes = $this->getPrestamosRecientes();
        $cuotasProximasVencer = $this->getCuotasProximasVencer();
        $movimientosRecientes = $this->getMovimientosRecientes();

        return view('dashboard', compact(
            'kpis',
            'evolucionCartera',
            'distribucionTipos',
            'morosidadMensual',
            'crecimientoAhorro',
            'topSocios',
            'prestamosRecientes',
            'cuotasProximasVencer',
            'movimientosRecientes'
        ));
    }

    /**
     * Calcular KPIs principales
     */
    private function getKPIs()
    {
        // Cartera total activa
        $carteraTotal = Prestamo::whereIn('estado', ['ACTIVO', 'VENCIDO'])
            ->sum('saldo');

        // Total de préstamos
        $totalPrestamos = Prestamo::whereIn('estado', ['ACTIVO', 'VENCIDO'])->count();
        
        // Préstamos pendientes de aprobación
        $prestamosPendientes = Prestamo::where('estado_aprobacion', 'PENDIENTE')->count();
        
        // Préstamos aprobados este mes
        $prestamosAprobadosMes = Prestamo::where('estado_aprobacion', 'APROBADO')
            ->whereMonth('fecha_aprobacion', Carbon::now()->month)
            ->whereYear('fecha_aprobacion', Carbon::now()->year)
            ->count();

        // Socios activos
        $sociosActivos = Socio::where('estado', 'ACTIVO')->count();

        // Índice de morosidad
        $carteraVencida = Prestamo::where('estado', 'VENCIDO')->sum('saldo');
        $indiceMorosidad = $carteraTotal > 0 ? ($carteraVencida / $carteraTotal) * 100 : 0;

        // Cuotas vencidas
        $cuotasVencidas = Cuota::where('estado', 'VENCIDA')->count();
        $montoMoraTotal = Cuota::where('estado', 'VENCIDA')->sum('mora');

        // Saldo total en ahorro
        $saldoAhorro = CuentaAhorro::where('estado', 'ACTIVA')->sum('saldo');
        $cuentasAhorro = CuentaAhorro::where('estado', 'ACTIVA')->count();

        // Pagos realizados hoy
        $pagosHoy = Pago::whereDate('fecha_pago', Carbon::today())->count();
        $montoPagosHoy = Pago::whereDate('fecha_pago', Carbon::today())->sum('monto');

        // Movimientos de ahorro hoy
        $movimientosHoy = MovimientoAhorro::whereDate('fecha_movimiento', Carbon::today())->count();

        return [
            'carteraTotal' => $carteraTotal,
            'totalPrestamos' => $totalPrestamos,
            'prestamosPendientes' => $prestamosPendientes,
            'prestamosAprobadosMes' => $prestamosAprobadosMes,
            'sociosActivos' => $sociosActivos,
            'indiceMorosidad' => round($indiceMorosidad, 2),
            'carteraVencida' => $carteraVencida,
            'cuotasVencidas' => $cuotasVencidas,
            'montoMoraTotal' => $montoMoraTotal,
            'saldoAhorro' => $saldoAhorro,
            'cuentasAhorro' => $cuentasAhorro,
            'pagosHoy' => $pagosHoy,
            'montoPagosHoy' => $montoPagosHoy,
            'movimientosHoy' => $movimientosHoy,
        ];
    }

    /**
     * Evolución de cartera últimos 6 meses
     */
    private function getEvolucionCartera()
    {
        $datos = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            
            // Cartera al final del mes
            $cartera = Prestamo::whereIn('estado', ['ACTIVO', 'VENCIDO', 'CANCELADO'])
                ->where('fecha_desembolso', '<=', $fecha->endOfMonth())
                ->sum('monto');
            
            // Saldo pendiente al final del mes
            $saldo = Prestamo::whereIn('estado', ['ACTIVO', 'VENCIDO'])
                ->where('fecha_desembolso', '<=', $fecha->endOfMonth())
                ->sum('saldo');
            
            $datos[] = [
                'mes' => $fecha->format('M Y'),
                'cartera' => floatval($cartera),
                'saldo' => floatval($saldo),
            ];
        }

        return $datos;
    }

    /**
     * Distribución de cartera por tipo de préstamo
     */
    private function getDistribucionTipos()
    {
        $datos = Prestamo::select('tipo_prestamo_id', DB::raw('SUM(saldo) as total'))
            ->whereIn('estado', ['ACTIVO', 'VENCIDO'])
            ->with('tipoPrestamo')
            ->groupBy('tipo_prestamo_id')
            ->get()
            ->map(function ($item) {
                return [
                    'tipo' => $item->tipoPrestamo->nombre ?? 'Desconocido',
                    'monto' => floatval($item->total),
                ];
            });

        return $datos;
    }

    /**
     * Morosidad mensual últimos 6 meses
     */
    private function getMorosidadMensual()
    {
        $datos = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            
            // Cuotas que vencieron en ese mes
            $cuotasVencidas = Cuota::whereYear('fecha_vencimiento', $fecha->year)
                ->whereMonth('fecha_vencimiento', $fecha->month)
                ->where('estado', 'VENCIDA')
                ->count();
            
            // Total de cuotas del mes
            $totalCuotas = Cuota::whereYear('fecha_vencimiento', $fecha->year)
                ->whereMonth('fecha_vencimiento', $fecha->month)
                ->count();
            
            $porcentaje = $totalCuotas > 0 ? ($cuotasVencidas / $totalCuotas) * 100 : 0;
            
            $datos[] = [
                'mes' => $fecha->format('M Y'),
                'porcentaje' => round($porcentaje, 2),
                'cuotas_vencidas' => $cuotasVencidas,
                'total_cuotas' => $totalCuotas,
            ];
        }

        return $datos;
    }

    /**
     * Crecimiento de ahorro últimos 6 meses
     */
    private function getCrecimientoAhorro()
    {
        $datos = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            
            // Saldo total al final del mes (suma de todos los movimientos hasta esa fecha)
            $depositos = MovimientoAhorro::where('tipo_movimiento', 'DEPOSITO')
                ->where('fecha_movimiento', '<=', $fecha->endOfMonth())
                ->sum('monto');
            
            $retiros = MovimientoAhorro::where('tipo_movimiento', 'RETIRO')
                ->where('fecha_movimiento', '<=', $fecha->endOfMonth())
                ->sum('monto');
            
            $saldoTotal = $depositos - $retiros;
            
            $datos[] = [
                'mes' => $fecha->format('M Y'),
                'saldo' => floatval($saldoTotal),
            ];
        }

        return $datos;
    }

    /**
     * Top 10 socios con mayor saldo
     */
    private function getTopSocios()
    {
        return CuentaAhorro::with('socio')
            ->where('estado', 'ACTIVA')
            ->orderBy('saldo', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($cuenta) {
                return [
                    'socio' => $cuenta->socio->nombres . ' ' . $cuenta->socio->apellidos,
                    'numero_cuenta' => $cuenta->numero_cuenta,
                    'saldo' => floatval($cuenta->saldo),
                ];
            });
    }

    /**
     * Últimos 5 préstamos registrados
     */
    private function getPrestamosRecientes()
    {
        return Prestamo::with(['socio', 'tipoPrestamo'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($prestamo) {
                return [
                    'id' => $prestamo->id,
                    'socio' => $prestamo->socio->nombres . ' ' . $prestamo->socio->apellidos,
                    'tipo' => $prestamo->tipoPrestamo->nombre,
                    'monto' => floatval($prestamo->monto),
                    'estado' => $prestamo->estado,
                    'estado_aprobacion' => $prestamo->estado_aprobacion,
                    'fecha_solicitud' => $prestamo->fecha_solicitud?->format('d/m/Y'),
                ];
            });
    }

    /**
     * Cuotas próximas a vencer (próximos 7 días)
     */
    private function getCuotasProximasVencer()
    {
        return Cuota::with(['prestamo.socio'])
            ->where('estado', 'PENDIENTE')
            ->whereBetween('fecha_vencimiento', [Carbon::today(), Carbon::today()->addDays(7)])
            ->orderBy('fecha_vencimiento')
            ->limit(10)
            ->get()
            ->map(function ($cuota) {
                $diasRestantes = Carbon::today()->diffInDays($cuota->fecha_vencimiento, false);
                
                return [
                    'prestamo_id' => $cuota->prestamo_id,
                    'socio' => $cuota->prestamo->socio->nombres . ' ' . $cuota->prestamo->socio->apellidos,
                    'numero_cuota' => $cuota->numero_cuota,
                    'monto' => floatval($cuota->monto),
                    'fecha_vencimiento' => $cuota->fecha_vencimiento->format('d/m/Y'),
                    'dias_restantes' => $diasRestantes,
                ];
            });
    }

    /**
     * Últimos 5 movimientos de ahorro
     */
    private function getMovimientosRecientes()
    {
        return MovimientoAhorro::with(['cuenta.socio'])
            ->latest('fecha_movimiento')
            ->limit(5)
            ->get()
            ->map(function ($movimiento) {
                return [
                    'id' => $movimiento->id,
                    'tipo' => $movimiento->tipo_movimiento,
                    'socio' => $movimiento->cuenta->socio->nombres . ' ' . $movimiento->cuenta->socio->apellidos,
                    'cuenta' => $movimiento->cuenta->numero_cuenta,
                    'monto' => floatval($movimiento->monto),
                    'fecha' => $movimiento->fecha_movimiento->format('d/m/Y H:i'),
                ];
            });
    }
}
