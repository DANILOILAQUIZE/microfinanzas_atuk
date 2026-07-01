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
        Schema::table('tipos_prestamo', function (Blueprint $table) {
            $table->decimal('monto_minimo', 10, 2)->nullable()->after('interes');
            $table->decimal('monto_maximo', 10, 2)->nullable()->after('monto_minimo');
            $table->integer('plazo_minimo')->nullable()->after('monto_maximo');
            $table->text('descripcion')->nullable()->after('nombre');
            $table->enum('estado', ['ACTIVO', 'INACTIVO'])->default('ACTIVO')->after('plazo_maximo');
            $table->boolean('requiere_garantia')->default(false)->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipos_prestamo', function (Blueprint $table) {
            $table->dropColumn(['monto_minimo', 'monto_maximo', 'plazo_minimo', 'descripcion', 'estado', 'requiere_garantia']);
        });
    }
};
