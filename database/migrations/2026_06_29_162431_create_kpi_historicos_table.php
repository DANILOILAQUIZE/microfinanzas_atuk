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
        Schema::create('kpi_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimension_temporal_id')->constrained('dimension_temporal');
            $table->string('nombre_kpi', 100);
            $table->string('slug', 100);
            $table->decimal('valor', 12, 2);
            $table->string('unidad_medida', 20)->nullable();
            $table->decimal('valor_anterior', 12, 2)->nullable();
            $table->decimal('variacion', 5, 2)->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_historicos');
    }
};
