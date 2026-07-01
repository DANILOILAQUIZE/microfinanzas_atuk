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
        Schema::table('cuentas_ahorro', function (Blueprint $table) {
            // Agregar nuevas columnas
            $table->date('fecha_apertura')->after('numero_cuenta');
            $table->decimal('deposito_inicial', 12, 2)->after('fecha_apertura');
            $table->decimal('saldo_disponible', 12, 2)->default('0.00')->after('saldo');
            $table->decimal('saldo_bloqueado', 12, 2)->default('0.00')->after('saldo_disponible');
            $table->text('observaciones')->nullable()->after('estado');
            
            // Modificar enum de estado para incluir BLOQUEADA
            $table->enum('estado', ['ACTIVA', 'INACTIVA', 'BLOQUEADA'])->default('ACTIVA')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuentas_ahorro', function (Blueprint $table) {
            $table->dropColumn([
                'fecha_apertura',
                'deposito_inicial',
                'saldo_disponible',
                'saldo_bloqueado',
                'observaciones'
            ]);
            
            // Revertir enum de estado
            $table->enum('estado', ['ACTIVA', 'INACTIVA'])->default('ACTIVA')->change();
        });
    }
};
