<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema',
                'estado' => 'ACTIVO',
            ],
            [
                'nombre' => 'Gerente',
                'descripcion' => 'Gestión de préstamos y socios',
                'estado' => 'ACTIVO',
            ],
            [
                'nombre' => 'Cajero',
                'descripcion' => 'Gestión de pagos y movimientos',
                'estado' => 'ACTIVO',
            ],
            [
                'nombre' => 'Analista',
                'descripcion' => 'Acceso a reportes y análisis',
                'estado' => 'ACTIVO',
            ],
        ];

        foreach ($roles as $rol) {
            Rol::create($rol);
        }
    }
}
