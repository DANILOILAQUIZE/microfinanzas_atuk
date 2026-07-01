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
        Schema::table('garantias', function (Blueprint $table) {
            $table->enum('estado', ['ACTIVA', 'LIBERADA', 'EJECUTADA'])->default('ACTIVA')->after('valor');
            $table->string('documento_soporte', 255)->nullable()->after('estado')->comment('Ruta del documento de respaldo');
            $table->date('fecha_registro')->nullable()->after('documento_soporte');
            $table->date('fecha_liberacion')->nullable()->after('fecha_registro');
            $table->text('observaciones')->nullable()->after('fecha_liberacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('garantias', function (Blueprint $table) {
            $table->dropColumn(['estado', 'documento_soporte', 'fecha_registro', 'fecha_liberacion', 'observaciones']);
        });
    }
};
