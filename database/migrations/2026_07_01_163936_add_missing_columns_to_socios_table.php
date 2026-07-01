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
        Schema::table('socios', function (Blueprint $table) {
            $table->date('fecha_nacimiento')->nullable()->after('apellidos');
            $table->enum('genero', ['M', 'F', 'Otro'])->nullable()->after('fecha_nacimiento');
            $table->string('ciudad', 100)->nullable()->after('direccion');
            $table->string('ocupacion', 100)->nullable()->after('ciudad');
            $table->decimal('ingresos_mensuales', 12, 2)->nullable()->after('ocupacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('socios', function (Blueprint $table) {
            $table->dropColumn(['fecha_nacimiento', 'genero', 'ciudad', 'ocupacion', 'ingresos_mensuales']);
        });
    }
};
