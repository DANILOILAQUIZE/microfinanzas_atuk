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
        // MySQL no permite modificar ENUM directamente, hay que usar ALTER TABLE con CHANGE
        DB::statement("ALTER TABLE prestamos MODIFY COLUMN estado ENUM('PENDIENTE', 'ACTIVO', 'CANCELADO', 'VENCIDO', 'RECHAZADO') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE prestamos MODIFY COLUMN estado ENUM('PENDIENTE', 'APROBADO', 'FINALIZADO', 'MORA') NULL");
    }
};
