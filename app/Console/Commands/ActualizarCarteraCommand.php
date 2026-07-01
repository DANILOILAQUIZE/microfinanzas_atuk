<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HechoCartera;
use App\Models\Prestamo;
use App\Models\TipoPrestamo;
use App\Models\Socio;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActualizarCarteraCommand extends Command
{
    protected $signature = 'dw:actualizar-cartera {--date= : Fecha específica (Y-m-d)}';
    protected $description = 'Actualizar hechos de cartera en el Data Warehouse';

    public function handle()
    {
        $fecha = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
        
        $this->info("Actualizando cartera para: {$fecha->toDateString()}");

        try {
            DB::beginTransaction();

            // Eliminar registros existentes de esta fecha (por si se ejecuta dos veces)
            HechoCartera::where('fecha', $fecha->toDateString())->delete();

            // SNAPSHOT GLOBAL
            $this->generarSnapshotGlobal($fecha);

            // SNAPSHOT POR TIPO DE PRÉSTAMO
            $this->generarSnapshotPorTipo($fecha);

            // SNAPSHOT POR SOCIO (Top 50 socios con mayor saldo)
            $this->generarSnapshotPorSocio($fecha);

            DB::commit();

            $total = HechoCartera::where('fecha', $fecha->toDateString())->count();
            $this->info("✓ Cartera actualizada: {$total} registros insertados");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error al actualizar cartera: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function generarSnapshotGlobal($fecha)
    {
        $carteraTotal = Prestamo::whereIn('estado', ['ACTIVO', 'VENCIDO'])
            ->whereDate('fecha_desembolso', '<=', $fecha)
            ->sum('monto');

        $carteraVigente = Prestamo::where('estado', 'ACTIVO')
            ->whereDate('fecha_desembolso', '<=', $fecha)
            ->sum('saldo');

        $carteraVencida = Prestamo::where('estado', 'VENCIDO')
            ->whereDate('fecha_desembolso', '<=', $fecha)
            ->sum('saldo');

        $numeroPrestamos = Prestamo::whereIn('estado', ['ACTIVO', 'VENCIDO'])
            ->whereDate('fecha_desembolso', '<=', $fecha)
            ->count();

        HechoCartera::create([
            'fecha' => $fecha->toDateString(),
            'tipo_prestamo_id' => null,
            'socio_id' => null,
            'cartera_total' => $carteraTotal,
            'cartera_vigente' => $carteraVigente,
            'cartera_vencida' => $carteraVencida,
            'numero_prestamos' => $numeroPrestamos,
            'monto_desembolsado_mes' => $this->getMontoDesembolsadoMes($fecha),
            'monto_recuperado_mes' => $this->getMontoRecuperadoMes($fecha),
        ]);

        $this->line("  → Snapshot global generado");
    }

    private function generarSnapshotPorTipo($fecha)
    {
        $tipos = TipoPrestamo::all();

        foreach ($tipos as $tipo) {
            $carteraTotal = Prestamo::where('tipo_prestamo_id', $tipo->id)
                ->whereIn('estado', ['ACTIVO', 'VENCIDO'])
                ->whereDate('fecha_desembolso', '<=', $fecha)
                ->sum('monto');

            $carteraVigente = Prestamo::where('tipo_prestamo_id', $tipo->id)
                ->where('estado', 'ACTIVO')
                ->whereDate('fecha_desembolso', '<=', $fecha)
                ->sum('saldo');

            $carteraVencida = Prestamo::where('tipo_prestamo_id', $tipo->id)
                ->where('estado', 'VENCIDO')
                ->whereDate('fecha_desembolso', '<=', $fecha)
                ->sum('saldo');

            $numeroPrestamos = Prestamo::where('tipo_prestamo_id', $tipo->id)
                ->whereIn('estado', ['ACTIVO', 'VENCIDO'])
                ->whereDate('fecha_desembolso', '<=', $fecha)
                ->count();

            if ($carteraTotal > 0 || $numeroPrestamos > 0) {
                HechoCartera::create([
                    'fecha' => $fecha->toDateString(),
                    'tipo_prestamo_id' => $tipo->id,
                    'socio_id' => null,
                    'cartera_total' => $carteraTotal,
                    'cartera_vigente' => $carteraVigente,
                    'cartera_vencida' => $carteraVencida,
                    'numero_prestamos' => $numeroPrestamos,
                    'monto_desembolsado_mes' => 0,
                    'monto_recuperado_mes' => 0,
                ]);
            }
        }

        $this->line("  → Snapshots por tipo generados: {$tipos->count()} tipos");
    }

    private function generarSnapshotPorSocio($fecha)
    {
        // Top 50 socios con mayor saldo
        $socios = Prestamo::select('socio_id', DB::raw('SUM(saldo) as total_saldo'))
            ->whereIn('estado', ['ACTIVO', 'VENCIDO'])
            ->whereDate('fecha_desembolso', '<=', $fecha)
            ->groupBy('socio_id')
            ->orderBy('total_saldo', 'desc')
            ->limit(50)
            ->get();

        foreach ($socios as $socioData) {
            $carteraTotal = Prestamo::where('socio_id', $socioData->socio_id)
                ->whereIn('estado', ['ACTIVO', 'VENCIDO'])
                ->whereDate('fecha_desembolso', '<=', $fecha)
                ->sum('monto');

            $carteraVigente = Prestamo::where('socio_id', $socioData->socio_id)
                ->where('estado', 'ACTIVO')
                ->whereDate('fecha_desembolso', '<=', $fecha)
                ->sum('saldo');

            $carteraVencida = Prestamo::where('socio_id', $socioData->socio_id)
                ->where('estado', 'VENCIDO')
                ->whereDate('fecha_desembolso', '<=', $fecha)
                ->sum('saldo');

            $numeroPrestamos = Prestamo::where('socio_id', $socioData->socio_id)
                ->whereIn('estado', ['ACTIVO', 'VENCIDO'])
                ->whereDate('fecha_desembolso', '<=', $fecha)
                ->count();

            HechoCartera::create([
                'fecha' => $fecha->toDateString(),
                'tipo_prestamo_id' => null,
                'socio_id' => $socioData->socio_id,
                'cartera_total' => $carteraTotal,
                'cartera_vigente' => $carteraVigente,
                'cartera_vencida' => $carteraVencida,
                'numero_prestamos' => $numeroPrestamos,
                'monto_desembolsado_mes' => 0,
                'monto_recuperado_mes' => 0,
            ]);
        }

        $this->line("  → Snapshots por socio generados: {$socios->count()} socios");
    }

    private function getMontoDesembolsadoMes($fecha)
    {
        return Prestamo::whereYear('fecha_desembolso', $fecha->year)
            ->whereMonth('fecha_desembolso', $fecha->month)
            ->where('estado_aprobacion', 'APROBADO')
            ->sum('monto');
    }

    private function getMontoRecuperadoMes($fecha)
    {
        return DB::table('pagos')
            ->whereYear('fecha_pago', $fecha->year)
            ->whereMonth('fecha_pago', $fecha->month)
            ->sum('monto');
    }
}
