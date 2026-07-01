<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar columna fecha a hecho_cartera y actualizar estructura
        Schema::table('hecho_cartera', function (Blueprint $table) {
            if (!Schema::hasColumn('hecho_cartera', 'fecha')) {
                $table->date('fecha')->nullable()->after('id');
            }
            if (Schema::hasColumn('hecho_cartera', 'dimension_temporal_id')) {
                $table->dropForeign(['dimension_temporal_id']);
                $table->dropColumn('dimension_temporal_id');
            }
            
            // Agregar columnas necesarias para el comando
            if (!Schema::hasColumn('hecho_cartera', 'cartera_total')) {
                $table->decimal('cartera_total', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('hecho_cartera', 'cartera_vigente')) {
                $table->decimal('cartera_vigente', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('hecho_cartera', 'cartera_vencida')) {
                $table->decimal('cartera_vencida', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('hecho_cartera', 'numero_prestamos')) {
                $table->integer('numero_prestamos')->default(0);
            }
            if (!Schema::hasColumn('hecho_cartera', 'monto_desembolsado_mes')) {
                $table->decimal('monto_desembolsado_mes', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('hecho_cartera', 'monto_recuperado_mes')) {
                $table->decimal('monto_recuperado_mes', 12, 2)->default(0);
            }
        });

        // Agregar columna fecha a hecho_morosidad y actualizar estructura
        Schema::table('hecho_morosidad', function (Blueprint $table) {
            if (!Schema::hasColumn('hecho_morosidad', 'fecha')) {
                $table->date('fecha')->nullable()->after('id');
            }
            if (Schema::hasColumn('hecho_morosidad', 'dimension_temporal_id')) {
                $table->dropForeign(['dimension_temporal_id']);
                $table->dropColumn('dimension_temporal_id');
            }
            
            // Agregar columnas necesarias para el comando
            if (!Schema::hasColumn('hecho_morosidad', 'cartera_total')) {
                $table->decimal('cartera_total', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('hecho_morosidad', 'cartera_vencida')) {
                $table->decimal('cartera_vencida', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('hecho_morosidad', 'cuotas_vencidas_total')) {
                $table->integer('cuotas_vencidas_total')->default(0);
            }
            if (!Schema::hasColumn('hecho_morosidad', 'monto_mora_total')) {
                $table->decimal('monto_mora_total', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('hecho_morosidad', 'prestamos_vencidos')) {
                $table->integer('prestamos_vencidos')->default(0);
            }
            if (!Schema::hasColumn('hecho_morosidad', 'cuotas_mora_1_30')) {
                $table->integer('cuotas_mora_1_30')->default(0);
            }
            if (!Schema::hasColumn('hecho_morosidad', 'cuotas_mora_31_60')) {
                $table->integer('cuotas_mora_31_60')->default(0);
            }
            if (!Schema::hasColumn('hecho_morosidad', 'cuotas_mora_61_90')) {
                $table->integer('cuotas_mora_61_90')->default(0);
            }
            if (!Schema::hasColumn('hecho_morosidad', 'cuotas_mora_mas_90')) {
                $table->integer('cuotas_mora_mas_90')->default(0);
            }
            if (!Schema::hasColumn('hecho_morosidad', 'monto_mora_1_30')) {
                $table->decimal('monto_mora_1_30', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('hecho_morosidad', 'monto_mora_31_60')) {
                $table->decimal('monto_mora_31_60', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('hecho_morosidad', 'monto_mora_61_90')) {
                $table->decimal('monto_mora_61_90', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('hecho_morosidad', 'monto_mora_mas_90')) {
                $table->decimal('monto_mora_mas_90', 12, 2)->default(0);
            }
        });

        // Modificar kpi_historicos para nuevo formato
        Schema::table('kpi_historicos', function (Blueprint $table) {
            if (!Schema::hasColumn('kpi_historicos', 'fecha')) {
                $table->date('fecha')->nullable()->after('id');
            }
            
            // Agregar columnas para todos los KPIs si no existen
            if (!Schema::hasColumn('kpi_historicos', 'cartera_total')) {
                $table->decimal('cartera_total', 12, 2)->default(0);
                $table->decimal('cartera_vencida', 12, 2)->default(0);
                $table->integer('total_prestamos')->default(0);
                $table->integer('prestamos_pendientes')->default(0);
                $table->integer('prestamos_aprobados_mes')->default(0);
                $table->integer('socios_activos')->default(0);
                $table->integer('socios_totales')->default(0);
                $table->decimal('indice_morosidad', 5, 2)->default(0);
                $table->integer('cuotas_vencidas')->default(0);
                $table->decimal('monto_mora_total', 12, 2)->default(0);
                $table->decimal('saldo_ahorro', 12, 2)->default(0);
                $table->integer('cuentas_ahorro')->default(0);
                $table->integer('pagos_mes')->default(0);
                $table->decimal('monto_pagos_mes', 12, 2)->default(0);
                $table->integer('movimientos_mes')->default(0);
                $table->decimal('depositos_mes', 12, 2)->default(0);
                $table->decimal('retiros_mes', 12, 2)->default(0);
            }
            
            // Eliminar columnas antiguas si existen
            if (Schema::hasColumn('kpi_historicos', 'dimension_temporal_id')) {
                $table->dropForeign(['dimension_temporal_id']);
            }
            
            $columnsToRemove = [
                'dimension_temporal_id',
                'nombre_kpi',
                'slug',
                'valor',
                'unidad_medida',
                'valor_anterior',
                'variacion',
                'descripcion'
            ];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('kpi_historicos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hecho_cartera', function (Blueprint $table) {
            $table->dropColumn('fecha');
            $table->unsignedBigInteger('dimension_temporal_id')->nullable();
            $table->unsignedBigInteger('dimension_prestamo_id')->nullable();
            $table->foreign('dimension_temporal_id')->references('id')->on('dimension_temporal');
        });

        Schema::table('hecho_morosidad', function (Blueprint $table) {
            $table->dropColumn('fecha');
            $table->unsignedBigInteger('dimension_temporal_id')->nullable();
            $table->unsignedBigInteger('dimension_prestamo_id')->nullable();
            $table->foreign('dimension_temporal_id')->references('id')->on('dimension_temporal');
        });

        Schema::table('kpi_historicos', function (Blueprint $table) {
            $table->dropColumn([
                'fecha',
                'cartera_total',
                'cartera_vencida',
                'total_prestamos',
                'prestamos_pendientes',
                'prestamos_aprobados_mes',
                'socios_activos',
                'socios_totales',
                'indice_morosidad',
                'cuotas_vencidas',
                'monto_mora_total',
                'saldo_ahorro',
                'cuentas_ahorro',
                'pagos_mes',
                'monto_pagos_mes',
                'movimientos_mes',
                'depositos_mes',
                'retiros_mes'
            ]);
            
            $table->unsignedBigInteger('dimension_temporal_id')->nullable();
            $table->string('nombre_kpi', 100);
            $table->string('slug', 100);
            $table->decimal('valor', 12, 2);
            $table->string('unidad_medida', 50)->nullable();
            $table->decimal('valor_anterior', 12, 2)->nullable();
            $table->decimal('variacion', 12, 2)->nullable();
            $table->text('descripcion')->nullable();
            $table->foreign('dimension_temporal_id')->references('id')->on('dimension_temporal');
        });
    }
};
