<?php

namespace App\Console\Commands;

use App\Models\CuentaAhorro;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CorregirSaldosBloqueados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cuentas:corregir-saldos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige los saldos bloqueados de las cuentas existentes (deposito_inicial)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Corrigiendo saldos bloqueados de cuentas de ahorro...');
        $this->newLine();

        try {
            DB::beginTransaction();

            // Obtener todas las cuentas activas
            $cuentas = CuentaAhorro::whereIn('estado', ['ACTIVA', 'BLOQUEADA'])->get();

            $this->info("📊 Se encontraron {$cuentas->count()} cuentas para corregir");
            $this->newLine();

            $corregidas = 0;
            $bar = $this->output->createProgressBar($cuentas->count());

            foreach ($cuentas as $cuenta) {
                $saldoAnteriorBloqueado = $cuenta->saldo_bloqueado;
                $saldoAnteriorDisponible = $cuenta->saldo_disponible;

                // El saldo bloqueado debe ser igual al depósito inicial
                $nuevoSaldoBloqueado = $cuenta->deposito_inicial;
                
                // El saldo disponible es: saldo total - saldo bloqueado
                $nuevoSaldoDisponible = $cuenta->saldo - $nuevoSaldoBloqueado;

                // Actualizar la cuenta
                $cuenta->update([
                    'saldo_bloqueado' => $nuevoSaldoBloqueado,
                    'saldo_disponible' => $nuevoSaldoDisponible,
                ]);

                $this->newLine();
                $this->line("✅ Cuenta: {$cuenta->numero_cuenta} - {$cuenta->socio->nombres} {$cuenta->socio->apellidos}");
                $this->line("   Saldo Total: $" . number_format($cuenta->saldo, 2));
                $this->line("   Bloqueado: $" . number_format($saldoAnteriorBloqueado, 2) . " → $" . number_format($nuevoSaldoBloqueado, 2));
                $this->line("   Disponible: $" . number_format($saldoAnteriorDisponible, 2) . " → $" . number_format($nuevoSaldoDisponible, 2));

                $corregidas++;
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            DB::commit();

            $this->info("✅ Se corrigieron {$corregidas} cuentas exitosamente");
            $this->newLine();

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error al corregir saldos: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
