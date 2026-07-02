<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarDatosPrueba extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datos:limpiar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia todos los datos de prueba manteniendo la configuración (usuarios, roles, permisos, parámetros)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('    LIMPIEZA DE DATOS DE PRUEBA - MICROFINANZAS ATUK');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        if (!$this->confirm('¿Está seguro de que desea eliminar todos los datos de prueba? Esta acción NO se puede deshacer.')) {
            $this->warn('Operación cancelada.');
            return 0;
        }

        $this->newLine();
        $this->info('Iniciando limpieza de datos...');
        $this->newLine();

        try {
            // Desactivar restricciones de llaves foráneas temporalmente
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // 1. DATOS OPERATIVOS (SE ELIMINAN)
            $this->warn('🗑️  Eliminando datos operativos...');
            
            $this->line('   → Eliminando pagos...');
            DB::table('pagos')->truncate();
            
            $this->line('   → Eliminando cuotas...');
            DB::table('cuotas')->truncate();
            
            $this->line('   → Eliminando garantías...');
            DB::table('garantias')->truncate();
            
            $this->line('   → Eliminando préstamos...');
            DB::table('prestamos')->truncate();
            
            $this->line('   → Eliminando movimientos de ahorro...');
            DB::table('movimientos_ahorro')->truncate();
            
            $this->line('   → Eliminando cuentas de ahorro...');
            DB::table('cuentas_ahorro')->truncate();
            
            $this->line('   → Eliminando socios...');
            DB::table('socios')->truncate();
            
            $this->newLine();

            // 2. NOTIFICACIONES Y ALERTAS (SE ELIMINAN)
            $this->warn('🔔 Eliminando notificaciones y alertas...');
            
            if (DB::getSchemaBuilder()->hasTable('notificaciones')) {
                $this->line('   → Eliminando notificaciones...');
                DB::table('notificaciones')->truncate();
            }
            
            if (DB::getSchemaBuilder()->hasTable('alertas_riesgo')) {
                $this->line('   → Eliminando alertas de riesgo...');
                DB::table('alertas_riesgo')->truncate();
            }
            
            $this->newLine();

            // 3. REPORTES Y AUDITORÍA (SE ELIMINAN)
            $this->warn('📊 Eliminando datos de reportes y auditoría...');
            
            if (DB::getSchemaBuilder()->hasTable('auditoria_logs')) {
                $this->line('   → Eliminando registros de auditoría...');
                DB::table('auditoria_logs')->truncate();
            }
            
            if (DB::getSchemaBuilder()->hasTable('historial_cambios')) {
                $this->line('   → Eliminando historial de cambios...');
                DB::table('historial_cambios')->truncate();
            }
            
            if (DB::getSchemaBuilder()->hasTable('reportes')) {
                $this->line('   → Eliminando reportes generados...');
                DB::table('reportes')->truncate();
            }
            
            $this->newLine();

            // 4. DATA WAREHOUSE (SE ELIMINAN)
            $this->warn('🏢 Eliminando datos del Data Warehouse...');
            
            if (DB::getSchemaBuilder()->hasTable('hechos_cartera')) {
                $this->line('   → Eliminando hechos de cartera...');
                DB::table('hechos_cartera')->truncate();
            }
            
            if (DB::getSchemaBuilder()->hasTable('hechos_morosidad')) {
                $this->line('   → Eliminando hechos de morosidad...');
                DB::table('hechos_morosidad')->truncate();
            }
            
            if (DB::getSchemaBuilder()->hasTable('hechos_rentabilidad')) {
                $this->line('   → Eliminando hechos de rentabilidad...');
                DB::table('hechos_rentabilidad')->truncate();
            }
            
            if (DB::getSchemaBuilder()->hasTable('kpis_historicos')) {
                $this->line('   → Eliminando KPIs históricos...');
                DB::table('kpis_historicos')->truncate();
            }
            
            if (DB::getSchemaBuilder()->hasTable('dimension_temporal')) {
                $this->line('   → Eliminando dimensión temporal...');
                DB::table('dimension_temporal')->truncate();
            }
            
            $this->newLine();

            // Reactivar restricciones de llaves foráneas
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // 5. DATOS DE CONFIGURACIÓN (SE MANTIENEN)
            $this->info('✅ Datos de configuración que SE MANTIENEN:');
            $this->line('   ✓ Usuarios (' . DB::table('usuarios')->count() . ')');
            $this->line('   ✓ Roles (' . DB::table('roles')->count() . ')');
            $this->line('   ✓ Permisos (' . DB::table('permisos')->count() . ')');
            $this->line('   ✓ Tipos de Préstamo (' . DB::table('tipos_prestamo')->count() . ')');
            $this->line('   ✓ Parámetros (' . DB::table('parametros')->count() . ')');
            $this->line('   ✓ Pólizas (' . DB::table('polizas')->count() . ')');
            
            $this->newLine();
            $this->info('═══════════════════════════════════════════════════════');
            $this->info('✅ LIMPIEZA COMPLETADA EXITOSAMENTE');
            $this->info('═══════════════════════════════════════════════════════');
            $this->newLine();
            $this->line('Todos los datos de prueba han sido eliminados.');
            $this->line('La configuración del sistema se ha mantenido intacta.');
            $this->newLine();

            return 0;

        } catch (\Exception $e) {
            // Reactivar restricciones de llaves foráneas en caso de error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            $this->newLine();
            $this->error('═══════════════════════════════════════════════════════');
            $this->error('❌ ERROR AL LIMPIAR DATOS');
            $this->error('═══════════════════════════════════════════════════════');
            $this->error('Mensaje: ' . $e->getMessage());
            $this->newLine();

            return 1;
        }
    }
}
