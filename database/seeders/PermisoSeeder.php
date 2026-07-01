<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permiso;
use App\Models\Rol;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir todos los permisos del sistema
        $permisos = [
            // Socios
            ['nombre' => 'Ver socios', 'slug' => 'ver_socios', 'modulo' => 'Socios', 'descripcion' => 'Ver listado de socios'],
            ['nombre' => 'Crear socios', 'slug' => 'crear_socios', 'modulo' => 'Socios', 'descripcion' => 'Crear nuevos socios'],
            ['nombre' => 'Editar socios', 'slug' => 'editar_socios', 'modulo' => 'Socios', 'descripcion' => 'Editar información de socios'],
            ['nombre' => 'Eliminar socios', 'slug' => 'eliminar_socios', 'modulo' => 'Socios', 'descripcion' => 'Eliminar socios'],
            
            // Préstamos
            ['nombre' => 'Ver préstamos', 'slug' => 'ver_prestamos', 'modulo' => 'Préstamos', 'descripcion' => 'Ver listado de préstamos'],
            ['nombre' => 'Crear préstamos', 'slug' => 'crear_prestamos', 'modulo' => 'Préstamos', 'descripcion' => 'Crear solicitudes de préstamos'],
            ['nombre' => 'Aprobar préstamos', 'slug' => 'aprobar_prestamos', 'modulo' => 'Préstamos', 'descripcion' => 'Aprobar o rechazar préstamos'],
            ['nombre' => 'Editar préstamos', 'slug' => 'editar_prestamos', 'modulo' => 'Préstamos', 'descripcion' => 'Editar información de préstamos'],
            ['nombre' => 'Eliminar préstamos', 'slug' => 'eliminar_prestamos', 'modulo' => 'Préstamos', 'descripcion' => 'Eliminar préstamos pendientes o rechazados'],
            
            // Pagos
            ['nombre' => 'Ver pagos', 'slug' => 'ver_pagos', 'modulo' => 'Pagos', 'descripcion' => 'Ver historial de pagos'],
            ['nombre' => 'Registrar pagos', 'slug' => 'registrar_pagos', 'modulo' => 'Pagos', 'descripcion' => 'Registrar pagos de cuotas'],
            
            // Cuentas de Ahorro
            ['nombre' => 'Ver cuentas ahorro', 'slug' => 'ver_cuentas_ahorro', 'modulo' => 'Cuentas Ahorro', 'descripcion' => 'Ver cuentas de ahorro'],
            ['nombre' => 'Crear cuentas ahorro', 'slug' => 'crear_cuentas_ahorro', 'modulo' => 'Cuentas Ahorro', 'descripcion' => 'Abrir cuentas de ahorro'],
            ['nombre' => 'Gestionar movimientos', 'slug' => 'gestionar_movimientos', 'modulo' => 'Cuentas Ahorro', 'descripcion' => 'Realizar depósitos y retiros'],
            
            // Usuarios
            ['nombre' => 'Ver usuarios', 'slug' => 'ver_usuarios', 'modulo' => 'Usuarios', 'descripcion' => 'Ver listado de usuarios'],
            ['nombre' => 'Crear usuarios', 'slug' => 'crear_usuarios', 'modulo' => 'Usuarios', 'descripcion' => 'Crear nuevos usuarios'],
            ['nombre' => 'Editar usuarios', 'slug' => 'editar_usuarios', 'modulo' => 'Usuarios', 'descripcion' => 'Editar información de usuarios'],
            ['nombre' => 'Eliminar usuarios', 'slug' => 'eliminar_usuarios', 'modulo' => 'Usuarios', 'descripcion' => 'Eliminar usuarios'],
            
            // Roles y Permisos
            ['nombre' => 'Gestionar roles', 'slug' => 'gestionar_roles', 'modulo' => 'Roles', 'descripcion' => 'Gestionar roles y permisos'],
            
            // Reportes
            ['nombre' => 'Ver reportes', 'slug' => 'ver_reportes', 'modulo' => 'Reportes', 'descripcion' => 'Acceder a reportes y estadísticas'],
            ['nombre' => 'Exportar reportes', 'slug' => 'exportar_reportes', 'modulo' => 'Reportes', 'descripcion' => 'Exportar reportes en PDF/Excel'],
            
            // Parámetros
            ['nombre' => 'Gestionar parámetros', 'slug' => 'gestionar_parametros', 'modulo' => 'Parámetros', 'descripcion' => 'Configurar parámetros del sistema'],
            
            // Auditoría
            ['nombre' => 'Ver auditoría', 'slug' => 'ver_auditoria', 'modulo' => 'Auditoría', 'descripcion' => 'Ver logs de auditoría'],
        ];

        // Crear los permisos
        foreach ($permisos as $permiso) {
            Permiso::firstOrCreate(
                ['slug' => $permiso['slug']],
                $permiso
            );
        }

        // Asignar permisos a roles
        $this->asignarPermisosARoles();
    }

    private function asignarPermisosARoles()
    {
        // Administrador - todos los permisos
        $admin = Rol::where('nombre', 'Administrador')->first();
        if ($admin) {
            $todosPermisos = Permiso::all()->pluck('id');
            $admin->permisos()->sync($todosPermisos);
        }

        // Gerente - casi todos excepto gestión de usuarios y roles
        $gerente = Rol::where('nombre', 'Gerente')->first();
        if ($gerente) {
            $permisosGerente = Permiso::whereIn('slug', [
                'ver_socios', 'crear_socios', 'editar_socios',
                'ver_prestamos', 'crear_prestamos', 'aprobar_prestamos', 'editar_prestamos',
                'ver_pagos', 'registrar_pagos',
                'ver_cuentas_ahorro', 'crear_cuentas_ahorro', 'gestionar_movimientos',
                'ver_reportes', 'exportar_reportes',
                'ver_usuarios', // puede ver pero no modificar
            ])->pluck('id');
            $gerente->permisos()->sync($permisosGerente);
        }

        // Cajero - operaciones básicas
        $cajero = Rol::where('nombre', 'Cajero')->first();
        if ($cajero) {
            $permisosCajero = Permiso::whereIn('slug', [
                'ver_socios', 'crear_socios',
                'ver_prestamos', 'crear_prestamos',
                'ver_pagos', 'registrar_pagos',
                'ver_cuentas_ahorro', 'crear_cuentas_ahorro', 'gestionar_movimientos',
            ])->pluck('id');
            $cajero->permisos()->sync($permisosCajero);
        }

        // Auditor - solo lectura
        $auditor = Rol::where('nombre', 'Auditor')->first();
        if ($auditor) {
            $permisosAuditor = Permiso::whereIn('slug', [
                'ver_socios',
                'ver_prestamos',
                'ver_pagos',
                'ver_cuentas_ahorro',
                'ver_reportes',
                'ver_auditoria',
            ])->pluck('id');
            $auditor->permisos()->sync($permisosAuditor);
        }
    }
}
