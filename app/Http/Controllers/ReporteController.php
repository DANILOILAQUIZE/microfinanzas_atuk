<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HechoCartera;
use App\Models\HechoMorosidad;
use App\Models\KpiHistorico;
use App\Models\Prestamo;
use App\Models\Socio;
use App\Models\Pago;
use App\Models\TipoPrestamo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Página principal de reportes
     */
    public function index()
    {
        return view('reportes.index');
    }

    /**
     * REPORTE 1: Evolución de Cartera
     */
    public function cartera(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subMonths(6)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Datos de evolución desde Data Warehouse
        $evolucion = HechoCartera::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->whereNull('socio_id')
            ->whereNull('tipo_prestamo_id')
            ->orderBy('fecha')
            ->get();

        // Distribución actual por tipo de préstamo
        $distribucion = Prestamo::select('tipo_prestamo_id', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(saldo) as monto'))
            ->whereIn('estado', ['ACTIVO', 'VENCIDO'])
            ->with('tipoPrestamo')
            ->groupBy('tipo_prestamo_id')
            ->get();

        // Top 10 socios por saldo
        $topSocios = Prestamo::select('socio_id', DB::raw('SUM(saldo) as total_saldo'))
            ->whereIn('estado', ['ACTIVO', 'VENCIDO'])
            ->with('socio')
            ->groupBy('socio_id')
            ->orderByDesc('total_saldo')
            ->limit(10)
            ->get();

        // Estadísticas generales
        $stats = [
            'cartera_total' => Prestamo::whereIn('estado', ['ACTIVO', 'VENCIDO'])->sum('saldo'),
            'prestamos_activos' => Prestamo::where('estado', 'ACTIVO')->count(),
            'prestamos_vencidos' => Prestamo::where('estado', 'VENCIDO')->count(),
            'monto_desembolsado_mes' => Prestamo::whereMonth('fecha_desembolso', Carbon::now()->month)
                ->whereYear('fecha_desembolso', Carbon::now()->year)
                ->sum('monto'),
        ];

        return view('reportes.cartera', compact('evolucion', 'distribucion', 'topSocios', 'stats', 'fechaInicio', 'fechaFin'));
    }

    /**
     * REPORTE 2: Análisis de Morosidad
     */
    public function morosidad(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subMonths(6)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Evolución de morosidad desde DW
        $evolucion = HechoMorosidad::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->whereNull('socio_id')
            ->orderBy('fecha')
            ->get();

        // Rangos de mora actuales
        $rangosMora = [
            '1-30' => HechoMorosidad::whereDate('fecha', Carbon::today())
                ->whereNull('socio_id')
                ->value('cuotas_mora_1_30') ?? 0,
            '31-60' => HechoMorosidad::whereDate('fecha', Carbon::today())
                ->whereNull('socio_id')
                ->value('cuotas_mora_31_60') ?? 0,
            '61-90' => HechoMorosidad::whereDate('fecha', Carbon::today())
                ->whereNull('socio_id')
                ->value('cuotas_mora_61_90') ?? 0,
            '90+' => HechoMorosidad::whereDate('fecha', Carbon::today())
                ->whereNull('socio_id')
                ->value('cuotas_mora_mas_90') ?? 0,
        ];

        // Socios con más morosidad
        $sociosMorosos = Prestamo::where('estado', 'VENCIDO')
            ->with('socio')
            ->select('socio_id', DB::raw('SUM(saldo) as deuda_total'), DB::raw('COUNT(*) as prestamos_vencidos'))
            ->groupBy('socio_id')
            ->orderByDesc('deuda_total')
            ->limit(10)
            ->get();

        // Estadísticas
        $carteraTotal = Prestamo::whereIn('estado', ['ACTIVO', 'VENCIDO'])->sum('saldo');
        $carteraVencida = Prestamo::where('estado', 'VENCIDO')->sum('saldo');
        
        $stats = [
            'indice_morosidad' => $carteraTotal > 0 ? ($carteraVencida / $carteraTotal) * 100 : 0,
            'cartera_vencida' => $carteraVencida,
            'prestamos_vencidos' => Prestamo::where('estado', 'VENCIDO')->count(),
            'mora_promedio' => Prestamo::where('estado', 'VENCIDO')
                ->avg(DB::raw('DATEDIFF(NOW(), fecha_desembolso)')) ?? 0,
        ];

        return view('reportes.morosidad', compact('evolucion', 'rangosMora', 'sociosMorosos', 'stats', 'fechaInicio', 'fechaFin'));
    }

    /**
     * REPORTE 3: Rentabilidad y Pagos
     */
    public function rentabilidad(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subMonths(6)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Pagos por mes
        $pagosPorMes = Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
            ->select(
                DB::raw('DATE_FORMAT(fecha_pago, "%Y-%m") as mes'),
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('SUM(monto) as total')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Ingresos por concepto
        $ingresosPorConcepto = [
            'capital' => Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])->sum('capital'),
            'interes' => Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])->sum('interes'),
            'mora' => Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])->sum('mora'),
        ];

        // Tipos de préstamo más rentables
        $rentabilidadPorTipo = Prestamo::select(
                'tipo_prestamo_id',
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('SUM(monto) as monto_total'),
                DB::raw('SUM(monto * (tasa_interes/100) * (plazo/12)) as interes_total')
            )
            ->with('tipoPrestamo')
            ->where('estado_aprobacion', 'APROBADO')
            ->groupBy('tipo_prestamo_id')
            ->orderByDesc('interes_total')
            ->get();

        // Estadísticas
        $stats = [
            'ingresos_periodo' => array_sum($ingresosPorConcepto),
            'pagos_realizados' => Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])->count(),
            'promedio_pago' => Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])->avg('monto') ?? 0,
            'recuperacion_mes' => Pago::whereMonth('fecha_pago', Carbon::now()->month)
                ->whereYear('fecha_pago', Carbon::now()->year)
                ->sum('monto'),
        ];

        return view('reportes.rentabilidad', compact('pagosPorMes', 'ingresosPorConcepto', 'rentabilidadPorTipo', 'stats', 'fechaInicio', 'fechaFin'));
    }

    /**
     * REPORTE 4: KPIs Históricos
     */
    public function kpis(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subMonths(3)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Obtener KPIs históricos
        $kpis = KpiHistorico::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->orderBy('fecha')
            ->get();

        // KPIs actuales
        $kpiActual = KpiHistorico::whereDate('fecha', Carbon::today())->first();

        if (!$kpiActual) {
            // Si no hay KPI de hoy, calcular en tiempo real
            $carteraTotal = Prestamo::whereIn('estado', ['ACTIVO', 'VENCIDO'])->sum('saldo');
            $carteraVencida = Prestamo::where('estado', 'VENCIDO')->sum('saldo');
            
            $kpiActual = (object) [
                'cartera_total' => $carteraTotal,
                'cartera_vencida' => $carteraVencida,
                'total_prestamos' => Prestamo::whereIn('estado', ['ACTIVO', 'VENCIDO'])->count(),
                'socios_activos' => Socio::where('estado', 'ACTIVO')->count(),
                'indice_morosidad' => $carteraTotal > 0 ? ($carteraVencida / $carteraTotal) * 100 : 0,
                'saldo_ahorro' => DB::table('cuenta_ahorros')->where('estado', 'ACTIVA')->sum('saldo'),
            ];
        }

        return view('reportes.kpis', compact('kpis', 'kpiActual', 'fechaInicio', 'fechaFin'));
    }

    /**
     * REPORTE 5: Socios
     */
    public function socios(Request $request)
    {
        // Socios por estado
        $sociosPorEstado = Socio::select('estado', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('estado')
            ->get();

        // Socios más activos (más préstamos)
        $sociosActivos = Socio::withCount('prestamos')
            ->having('prestamos_count', '>', 0)
            ->orderByDesc('prestamos_count')
            ->limit(10)
            ->get();

        // Nuevos socios por mes (últimos 6 meses)
        $nuevosPorMes = Socio::where('created_at', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Estadísticas
        $stats = [
            'total_socios' => Socio::count(),
            'socios_activos' => Socio::where('estado', 'ACTIVO')->count(),
            'socios_con_prestamos' => Socio::has('prestamos')->count(),
            'socios_con_ahorro' => Socio::has('cuentasAhorro')->count(),
        ];

        return view('reportes.socios', compact('sociosPorEstado', 'sociosActivos', 'nuevosPorMes', 'stats'));
    }
}
