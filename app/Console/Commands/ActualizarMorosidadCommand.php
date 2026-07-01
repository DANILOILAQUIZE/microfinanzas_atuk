<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HechoMorosidad;
use App\Models\Prestamo;
use App\Models\Cuota;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActualizarMorosidadCommand extends Command
{
    protected $signature = 'dw:actualizar-morosidad {--date= : Fecha específica (Y-m-d)}';
    protected $description = 'Actualizar hechos de morosidad en el Data Warehouse';

    public function handle()
    {
        $fecha = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
        
        $this->info("Actualizando morosidad para: {$fecha->toDateString()}");

        try {
            DB::beginTransaction();

            // Eliminar registros existentes de esta fecha
            HechoMorosidad::where('fecha', $fecha->toDateString())->delete();

            // Calcular métricas de morosidad
            $carteraTotal = Prestamo::whereIn('estado', ['ACTIVO', 'VENCIDO'])
                ->whereDate('fecha_desembolso', '<=', $fecha)
                ->sum('saldo');

            $carteraVencida = Prestamo::where('estado', 'VENCIDO')
                ->whereDate('fecha_desembolso', '<=', $fecha)
                ->sum('saldo');

            $cuotasVencidas = Cuota::where('estado', 'VENCIDA')
                ->whereDate('fecha_vencimiento', '<=', $fecha)
                ->count();

            $montoMora = Cuota::where('estado', 'VENCIDA')
                ->whereDate('fecha_vencimiento', '<=', $fecha)
                ->sum('mora');

            $prestamosVencidos = Prestamo::where('estado', 'VENCIDO')
                ->whereDate('fecha_desembolso', '<=', $fecha)
                ->count();

            // SNAPSHOT GLOBAL: con rangos de mora por cantidad y monto
            $rangosCuotas = $this->calcularRangosCuotas($fecha);
            $rangosMonto = $this->calcularRangosMonto($fecha);

            HechoMorosidad::create([
                'fecha' => $fecha->toDateString(),
                'socio_id' => null,
                'cartera_total' => $carteraTotal,
                'cartera_vencida' => $carteraVencida,
                'cuotas_vencidas_total' => $cuotasVencidas,
                'monto_mora_total' => $montoMora,
                'prestamos_vencidos' => $prestamosVencidos,
                'cuotas_mora_1_30' => $rangosCuotas['1-30'],
                'cuotas_mora_31_60' => $rangosCuotas['31-60'],
                'cuotas_mora_61_90' => $rangosCuotas['61-90'],
                'cuotas_mora_mas_90' => $rangosCuotas['90+'],
                'monto_mora_1_30' => $rangosMonto['1-30'],
                'monto_mora_31_60' => $rangosMonto['31-60'],
                'monto_mora_61_90' => $rangosMonto['61-90'],
                'monto_mora_mas_90' => $rangosMonto['90+'],
            ]);

            DB::commit();

            $indiceMorosidad = $carteraTotal > 0 ? ($carteraVencida / $carteraTotal) * 100 : 0;
            
            $this->info("✓ Morosidad actualizada");
            $this->line("  → Índice de morosidad: " . round($indiceMorosidad, 2) . "%");
            $this->line("  → Cartera vencida: $" . number_format($carteraVencida, 2));
            $this->line("  → Préstamos vencidos: {$prestamosVencidos}");
            $this->line("  → Cuotas vencidas: {$cuotasVencidas}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error al actualizar morosidad: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function calcularRangosCuotas($fecha)
    {
        $rangos = [
            '1-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0,
        ];

        $cuotasVencidas = Cuota::where('estado', 'VENCIDA')
            ->whereDate('fecha_vencimiento', '<=', $fecha)
            ->get();

        foreach ($cuotasVencidas as $cuota) {
            $diasMora = $cuota->fecha_vencimiento->diffInDays($fecha);
            
            if ($diasMora <= 30) {
                $rangos['1-30']++;
            } elseif ($diasMora <= 60) {
                $rangos['31-60']++;
            } elseif ($diasMora <= 90) {
                $rangos['61-90']++;
            } else {
                $rangos['90+']++;
            }
        }

        return $rangos;
    }

    private function calcularRangosMonto($fecha)
    {
        $rangos = [
            '1-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0,
        ];

        $cuotasVencidas = Cuota::where('estado', 'VENCIDA')
            ->whereDate('fecha_vencimiento', '<=', $fecha)
            ->get();

        foreach ($cuotasVencidas as $cuota) {
            $diasMora = $cuota->fecha_vencimiento->diffInDays($fecha);
            
            if ($diasMora <= 30) {
                $rangos['1-30'] += $cuota->saldo_pendiente;
            } elseif ($diasMora <= 60) {
                $rangos['31-60'] += $cuota->saldo_pendiente;
            } elseif ($diasMora <= 90) {
                $rangos['61-90'] += $cuota->saldo_pendiente;
            } else {
                $rangos['90+'] += $cuota->saldo_pendiente;
            }
        }

        return $rangos;
    }
}
