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
        Schema::create('alerta_riesgos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socio_id')->nullable()->constrained('socios');
            $table->foreignId('prestamo_id')->nullable()->constrained('prestamos');
            $table->string('tipo_alerta', 50);
            $table->enum('nivel', ['INFO', 'WARNING', 'DANGER', 'CRITICAL'])->default('INFO');
            $table->text('mensaje');
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_alerta')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerta_riesgos');
    }
};
