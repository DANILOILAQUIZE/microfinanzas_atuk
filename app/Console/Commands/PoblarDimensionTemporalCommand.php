<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DimensionTemporal;
use Carbon\Carbon;

class PoblarDimensionTemporalCommand extends Command
{
    protected $signature = 'dw:poblar-dimension-temporal {--years=2 : Años hacia adelante}';
    protected $description = 'Poblar la dimensión temporal del Data Warehouse';

    public function handle()
    {
        $this->info('Iniciando población de dimensión temporal...');

        $years = (int) $this->option('years');
        $startDate = Carbon::now()->subYear(); // Desde hace 1 año
        $endDate = Carbon::now()->addYears($years); // Hasta N años adelante

        $inserted = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Verificar si ya existe
            $exists = DimensionTemporal::where('fecha', $currentDate->toDateString())->exists();

            if (!$exists) {
                DimensionTemporal::create([
                    'fecha' => $currentDate->toDateString(),
                    'anio' => $currentDate->year,
                    'mes' => $currentDate->month,
                    'trimestre' => $currentDate->quarter,
                    'dia' => $currentDate->day,
                    'nombre_mes' => $currentDate->locale('es')->monthName,
                    'nombre_trimestre' => 'Q' . $currentDate->quarter,
                    'nombre_dia' => $currentDate->locale('es')->dayName,
                    'dia_semana' => $currentDate->dayOfWeek,
                    'es_fin_semana' => $currentDate->isWeekend(),
                ]);

                $inserted++;
            }

            $currentDate->addDay();
        }

        $this->info("✓ Dimensión temporal poblada: {$inserted} registros insertados");
        $this->info("Rango: {$startDate->toDateString()} a {$endDate->toDateString()}");

        return Command::SUCCESS;
    }
}
