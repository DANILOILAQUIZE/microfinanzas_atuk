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
        Schema::create('cuentas_ahorro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socio_id')->constrained('socios');
            $table->string('numero_cuenta', 30)->nullable()->unique();
            $table->decimal('saldo', 12, 2)->default('0.00');
            $table->enum('estado', ['ACTIVA', 'INACTIVA'])->default('ACTIVA');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuenta_ahorros');
    }
};
