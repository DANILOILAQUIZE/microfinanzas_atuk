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
        Schema::create('movimientos_ahorro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_id')->constrained('cuentas_ahorro');
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->enum('tipo', ['DEPOSITO', 'RETIRO'])->nullable();
            $table->decimal('monto', 12, 2)->nullable();
            $table->dateTime('fecha')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_ahorros');
    }
};
