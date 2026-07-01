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
        Schema::table('parametros', function (Blueprint $table) {
            $table->string('clave', 100)->unique()->after('id');
            $table->text('descripcion')->nullable()->after('valor');
            $table->string('tipo', 50)->default('texto')->after('descripcion'); // texto, numero, porcentaje, booleano
            $table->string('grupo', 50)->nullable()->after('tipo'); // mora, sistema, transacciones
        });

        // Renombrar columnas existentes
        Schema::table('parametros', function (Blueprint $table) {
            $table->renameColumn('nombre', 'nombre_old');
            $table->renameColumn('valor', 'valor_old');
        });

        Schema::table('parametros', function (Blueprint $table) {
            $table->dropColumn(['nombre_old', 'valor_old']);
            $table->string('nombre', 150)->after('clave');
            $table->string('valor', 255)->nullable()->after('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->dropColumn(['clave', 'descripcion', 'tipo', 'grupo']);
        });
    }
};
