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
        Schema::create('cuotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestamo_id')->constrained('prestamos');
            $table->integer('numero')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('capital', 12, 2)->nullable();
            $table->decimal('interes', 12, 2)->nullable();
            $table->decimal('iva', 12, 2)->nullable();
            $table->decimal('mora', 12, 2)->nullable();
            $table->decimal('total', 12, 2)->nullable();
            $table->enum('estado', ['PENDIENTE', 'PAGADA', 'VENCIDA'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuotas');
    }
};
