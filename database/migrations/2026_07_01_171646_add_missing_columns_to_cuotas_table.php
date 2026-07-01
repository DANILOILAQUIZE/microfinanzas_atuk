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
        Schema::table('cuotas', function (Blueprint $table) {
            // Renombrar 'numero' a 'numero_cuota'
            $table->renameColumn('numero', 'numero_cuota');
            
            // Renombrar 'total' a 'monto'
            $table->renameColumn('total', 'monto');
            
            // Agregar nuevas columnas
            $table->date('fecha_pago')->nullable()->after('fecha_vencimiento');
            $table->decimal('saldo_pendiente', 12, 2)->default(0)->after('mora');
            
            // Eliminar columna 'iva' (no se usa en microfinanzas)
            $table->dropColumn('iva');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuotas', function (Blueprint $table) {
            // Revertir cambios
            $table->renameColumn('numero_cuota', 'numero');
            $table->renameColumn('monto', 'total');
            $table->dropColumn(['fecha_pago', 'saldo_pendiente']);
            $table->decimal('iva', 12, 2)->nullable()->after('interes');
        });
    }
};
