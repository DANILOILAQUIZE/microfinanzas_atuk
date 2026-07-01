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
        Schema::table('prestamos', function (Blueprint $table) {
            $table->foreignId('usuario_aprobador_id')->nullable()->after('usuario_id')->constrained('usuarios')->comment('Usuario que aprobó/rechazó');
            $table->decimal('monto_total', 12, 2)->nullable()->after('monto')->comment('Monto total a pagar (capital + intereses)');
            $table->decimal('monto_cuota', 12, 2)->nullable()->after('monto_total')->comment('Monto de cada cuota');
            $table->text('motivo_rechazo')->nullable()->after('observaciones');
            $table->date('fecha_solicitud')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prestamos', function (Blueprint $table) {
            $table->dropForeign(['usuario_aprobador_id']);
            $table->dropColumn(['usuario_aprobador_id', 'monto_total', 'monto_cuota', 'motivo_rechazo', 'fecha_solicitud']);
        });
    }
};
