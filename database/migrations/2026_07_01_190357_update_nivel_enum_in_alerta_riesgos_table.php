<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cambiar el tipo de columna nivel de enum
        DB::statement("ALTER TABLE alerta_riesgos MODIFY COLUMN nivel ENUM('BAJO', 'MEDIO', 'ALTO', 'CRITICO') DEFAULT 'MEDIO'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE alerta_riesgos MODIFY COLUMN nivel ENUM('INFO', 'WARNING', 'DANGER', 'CRITICAL') DEFAULT 'INFO'");
    }
};
