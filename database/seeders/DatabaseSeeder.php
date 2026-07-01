<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            PermisoSeeder::class,
            UsuarioSeeder::class,
            TipoPrestamoSeeder::class,
            ParametroSeeder::class,
            SocioPrestamoSeeder::class, // Datos de prueba
            CuentaAhorroSeeder::class, // Cuentas de ahorro de ejemplo
            MovimientoAhorroSeeder::class, // Movimientos de ahorro de ejemplo
        ]);
    }
}
