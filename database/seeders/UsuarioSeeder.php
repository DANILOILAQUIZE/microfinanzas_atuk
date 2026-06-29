<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRol = Rol::where('nombre', 'Administrador')->first();

        if ($adminRol) {
            Usuario::create([
                'rol_id' => $adminRol->id,
                'nombre' => 'Admin',
                'apellido' => 'Sistema',
                'email' => 'admin@atuk.com',
                'password' => Hash::make('admin123'),
                'estado' => 'ACTIVO',
            ]);
        }
    }
}
