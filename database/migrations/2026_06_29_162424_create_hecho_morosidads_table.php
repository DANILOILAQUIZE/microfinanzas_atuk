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
        Schema::create('hecho_morosidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimension_temporal_id')->constrained('dimension_temporal');
            $table->foreignId('socio_id')->nullable()->constrained('socios');
            $table->foreignId('prestamo_id')->nullable()->constrained('prestamos');
            $table->integer('dias_mora')->default(0);
            $table->decimal('monto_mora', 12, 2)->default(0);
            $table->decimal('monto_vencido', 12, 2)->default(0);
            $table->enum('nivel_riesgo', ['BAJO', 'MEDIO', 'ALTO', 'CRITICO'])->default('BAJO');
            $table->integer('cuotas_vencidas')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hecho_morosidads');
    }
};
