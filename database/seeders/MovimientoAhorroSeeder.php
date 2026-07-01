<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MovimientoAhorro;
use App\Models\CuentaAhorro;
use App\Models\Usuario;
use Carbon\Carbon;

class MovimientoAhorroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cuentas = CuentaAhorro::where('estado', 'ACTIVA')->get();
        $usuario = Usuario::first();

        if ($cuentas->count() < 1 || !$usuario) {
            $this->command->warn('Se necesitan cuentas activas y al menos un usuario. Ejecuta primero CuentaAhorroSeeder.');
            return;
        }

        // Movimientos para la primera cuenta
        if (isset($cuentas[0])) {
            $cuenta = $cuentas[0];
            $saldo = $cuenta->deposito_inicial; // Empieza con el depósito inicial

            // Depósito 1
            $movimiento1 = MovimientoAhorro::create([
                'cuenta_id' => $cuenta->id,
                'usuario_id' => $usuario->id,
                'tipo_movimiento' => 'DEPOSITO',
                'metodo_transaccion' => 'EFECTIVO',
                'referencia' => null,
                'monto' => 300.00,
                'saldo_anterior' => $saldo,
                'saldo_posterior' => $saldo + 300.00,
                'fecha_movimiento' => Carbon::now()->subMonths(4),
                'descripcion' => 'Depósito mensual',
                'observaciones' => null,
            ]);
            $saldo += 300.00;

            // Depósito 2
            $movimiento2 = MovimientoAhorro::create([
                'cuenta_id' => $cuenta->id,
                'usuario_id' => $usuario->id,
                'tipo_movimiento' => 'DEPOSITO',
                'metodo_transaccion' => 'TRANSFERENCIA',
                'referencia' => 'TRANS-001',
                'monto' => 200.00,
                'saldo_anterior' => $saldo,
                'saldo_posterior' => $saldo + 200.00,
                'fecha_movimiento' => Carbon::now()->subMonths(3),
                'descripcion' => 'Depósito por transferencia',
                'observaciones' => null,
            ]);
            $saldo += 200.00;

            // Retiro 1
            $movimiento3 = MovimientoAhorro::create([
                'cuenta_id' => $cuenta->id,
                'usuario_id' => $usuario->id,
                'tipo_movimiento' => 'RETIRO',
                'metodo_transaccion' => 'EFECTIVO',
                'referencia' => null,
                'monto' => 150.00,
                'saldo_anterior' => $saldo,
                'saldo_posterior' => $saldo - 150.00,
                'fecha_movimiento' => Carbon::now()->subMonths(2),
                'descripcion' => 'Retiro para emergencia',
                'observaciones' => null,
            ]);
            $saldo -= 150.00;

            // Depósito 3
            $movimiento4 = MovimientoAhorro::create([
                'cuenta_id' => $cuenta->id,
                'usuario_id' => $usuario->id,
                'tipo_movimiento' => 'DEPOSITO',
                'metodo_transaccion' => 'EFECTIVO',
                'referencia' => null,
                'monto' => 400.00,
                'saldo_anterior' => $saldo,
                'saldo_posterior' => $saldo + 400.00,
                'fecha_movimiento' => Carbon::now()->subMonth(),
                'descripcion' => 'Depósito mensual',
                'observaciones' => null,
            ]);

            $this->command->info("4 movimientos creados para la cuenta {$cuenta->numero_cuenta}");
        }

        // Movimientos para la segunda cuenta
        if (isset($cuentas[1])) {
            $cuenta = $cuentas[1];
            $saldo = $cuenta->deposito_inicial;

            // Depósito
            $movimiento = MovimientoAhorro::create([
                'cuenta_id' => $cuenta->id,
                'usuario_id' => $usuario->id,
                'tipo_movimiento' => 'DEPOSITO',
                'metodo_transaccion' => 'CHEQUE',
                'referencia' => 'CHQ-12345',
                'monto' => 550.00,
                'saldo_anterior' => $saldo,
                'saldo_posterior' => $saldo + 550.00,
                'fecha_movimiento' => Carbon::now()->subMonths(2),
                'descripcion' => 'Depósito por cheque',
                'observaciones' => 'Cheque del Banco Agrícola',
            ]);

            $this->command->info("1 movimiento creado para la cuenta {$cuenta->numero_cuenta}");
        }
    }
}
