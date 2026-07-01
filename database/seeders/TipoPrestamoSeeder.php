<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoPrestamo;

class TipoPrestamoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposPrestamo = [
            [
                'nombre' => 'Microcrédito',
                'descripcion' => 'Préstamos pequeños para emprendimientos y actividades productivas',
                'interes' => 18.50,
                'monto_minimo' => 500.00,
                'monto_maximo' => 5000.00,
                'plazo_minimo' => 6,
                'plazo_maximo' => 24,
                'estado' => 'ACTIVO',
                'requiere_garantia' => false,
            ],
            [
                'nombre' => 'Consumo',
                'descripcion' => 'Préstamos para gastos personales y consumo',
                'interes' => 16.00,
                'monto_minimo' => 1000.00,
                'monto_maximo' => 10000.00,
                'plazo_minimo' => 12,
                'plazo_maximo' => 36,
                'estado' => 'ACTIVO',
                'requiere_garantia' => false,
            ],
            [
                'nombre' => 'Vivienda',
                'descripcion' => 'Préstamos para compra, construcción o mejora de vivienda',
                'interes' => 12.00,
                'monto_minimo' => 5000.00,
                'monto_maximo' => 50000.00,
                'plazo_minimo' => 24,
                'plazo_maximo' => 120,
                'estado' => 'ACTIVO',
                'requiere_garantia' => true,
            ],
            [
                'nombre' => 'Emergencia',
                'descripcion' => 'Préstamos rápidos para situaciones de emergencia',
                'interes' => 20.00,
                'monto_minimo' => 200.00,
                'monto_maximo' => 2000.00,
                'plazo_minimo' => 3,
                'plazo_maximo' => 12,
                'estado' => 'ACTIVO',
                'requiere_garantia' => false,
            ],
            [
                'nombre' => 'Educación',
                'descripcion' => 'Préstamos para gastos educativos, matrículas y materiales',
                'interes' => 14.00,
                'monto_minimo' => 500.00,
                'monto_maximo' => 8000.00,
                'plazo_minimo' => 12,
                'plazo_maximo' => 48,
                'estado' => 'ACTIVO',
                'requiere_garantia' => false,
            ],
        ];

        foreach ($tiposPrestamo as $tipo) {
            TipoPrestamo::firstOrCreate(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }
    }
}
