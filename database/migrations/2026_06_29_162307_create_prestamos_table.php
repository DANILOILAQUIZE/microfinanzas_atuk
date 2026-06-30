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
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socio_id')->constrained('socios');
            $table->foreignId('tipo_prestamo_id')->constrained('tipos_prestamo');
            $table->decimal('monto', 12, 2)->nullable();
            $table->decimal('interes', 5, 2)->nullable();
            $table->integer('plazo')->nullable();
            $table->date('fecha_desembolso')->nullable();
            $table->decimal('saldo', 12, 2)->nullable();
            $table->enum('estado', ['PENDIENTE', 'APROBADO', 'FINALIZADO', 'MORA'])->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->comment('Usuario que registró/aprobó el préstamo');
            $table->enum('estado_aprobacion', ['PENDIENTE', 'APROBADO', 'RECHAZADO'])->default('PENDIENTE');
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};
