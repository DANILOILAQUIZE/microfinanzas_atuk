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
        Schema::create('hecho_cartera', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimension_temporal_id')->constrained('dimension_temporal');
            $table->foreignId('socio_id')->nullable()->constrained('socios');
            $table->foreignId('tipo_prestamo_id')->nullable()->constrained('tipos_prestamo');
            $table->decimal('monto_desembolsado', 12, 2)->default(0);
            $table->decimal('monto_pagado', 12, 2)->default(0);
            $table->decimal('saldo_pendiente', 12, 2)->default(0);
            $table->integer('prestamos_activos')->default(0);
            $table->integer('prestamos_nuevos')->default(0);
            $table->integer('prestamos_finalizados')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hecho_carteras');
    }
};
