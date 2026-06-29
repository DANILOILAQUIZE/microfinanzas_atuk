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
        Schema::create('hecho_rentabilidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimension_temporal_id')->constrained('dimension_temporal');
            $table->foreignId('tipo_prestamo_id')->nullable()->constrained('tipos_prestamo');
            $table->decimal('intereses_ganados', 12, 2)->default(0);
            $table->decimal('mora_ganada', 12, 2)->default(0);
            $table->decimal('comisiones_ganadas', 12, 2)->default(0);
            $table->decimal('ingresos_totales', 12, 2)->default(0);
            $table->decimal('costos_operativos', 12, 2)->default(0);
            $table->decimal('rentabilidad_neta', 12, 2)->default(0);
            $table->decimal('roi', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hecho_rentabilidads');
    }
};
