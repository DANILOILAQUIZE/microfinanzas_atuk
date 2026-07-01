<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CuentaAhorro;
use App\Models\Socio;
use Carbon\Carbon;

class CuentaAhorroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socios = Socio::where('estado', 'ACTIVO')->get();

        if ($socios->count() < 3) {
            $this->command->warn('Se necesitan al menos 3 socios activos. Ejecuta primero SocioPrestamoSeeder.');
            return;
        }

        $cuentas = [
            [
                'socio_id' => $socios[0]->id,
                'numero_cuenta' => 'CA-' . Carbon::now()->format('Ymd') . '-0001',
                'fecha_apertura' => Carbon::now()->subMonths(6),
                'deposito_inicial' => 500.00,
                'saldo' => 1250.00,
                'saldo_disponible' => 1250.00,
                'saldo_bloqueado' => 0.00,
                'estado' => 'ACTIVA',
                'observaciones' => 'Cuenta aperturada con depósito inicial',
            ],
            [
                'socio_id' => $socios[1]->id,
                'numero_cuenta' => 'CA-' . Carbon::now()->format('Ymd') . '-0002',
                'fecha_apertura' => Carbon::now()->subMonths(4),
                'deposito_inicial' => 300.00,
                'saldo' => 850.00,
                'saldo_disponible' => 850.00,
                'saldo_bloqueado' => 0.00,
                'estado' => 'ACTIVA',
                'observaciones' => null,
            ],
            [
                'socio_id' => $socios[2]->id,
                'numero_cuenta' => 'CA-' . Carbon::now()->format('Ymd') . '-0003',
                'fecha_apertura' => Carbon::now()->subMonths(2),
                'deposito_inicial' => 1000.00,
                'saldo' => 2500.00,
                'saldo_disponible' => 2300.00,
                'saldo_bloqueado' => 200.00,
                'estado' => 'ACTIVA',
                'observaciones' => 'Cuenta con saldo bloqueado por garantía',
            ],
        ];

        foreach ($cuentas as $cuentaData) {
            // Verificar que el socio no tenga ya una cuenta
            $existe = CuentaAhorro::where('socio_id', $cuentaData['socio_id'])->first();
            if (!$existe) {
                CuentaAhorro::create($cuentaData);
                $this->command->info("Cuenta {$cuentaData['numero_cuenta']} creada exitosamente.");
            }
        }
    }
}
