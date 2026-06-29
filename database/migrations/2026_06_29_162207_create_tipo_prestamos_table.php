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
        Schema::create('tipos_prestamo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->nullable();
            $table->decimal('interes', 5, 2)->nullable();
            $table->integer('plazo_maximo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_prestamos');
    }
};
