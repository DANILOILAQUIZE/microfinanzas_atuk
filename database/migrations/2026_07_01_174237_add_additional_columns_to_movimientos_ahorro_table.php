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
        Schema::table('movimientos_ahorro', function (Blueprint $table) {
            // Renombrar 'tipo' a 'tipo_movimiento' y 'fecha' a 'fecha_movimiento'
            $table->renameColumn('tipo', 'tipo_movimiento');
            $table->renameColumn('fecha', 'fecha_movimiento');
            
            // Agregar nuevas columnas
            $table->enum('metodo_transaccion', ['EFECTIVO', 'TRANSFERENCIA', 'CHEQUE', 'TARJETA'])->default('EFECTIVO')->after('tipo_movimiento');
            $table->string('referencia', 50)->nullable()->after('metodo_transaccion');
            $table->text('observaciones')->nullable()->after('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos_ahorro', function (Blueprint $table) {
            $table->renameColumn('tipo_movimiento', 'tipo');
            $table->renameColumn('fecha_movimiento', 'fecha');
            $table->dropColumn(['metodo_transaccion', 'referencia', 'observaciones']);
        });
    }
};
