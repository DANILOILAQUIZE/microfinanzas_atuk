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
        Schema::create('dimension_temporal', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->integer('dia');
            $table->integer('mes');
            $table->integer('trimestre');
            $table->integer('anio');
            $table->string('nombre_mes', 20);
            $table->string('nombre_trimestre', 20);
            $table->boolean('es_fin_semana')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dimension_temporals');
    }
};
