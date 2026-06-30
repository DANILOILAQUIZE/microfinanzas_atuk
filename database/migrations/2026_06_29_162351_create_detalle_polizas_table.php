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
        Schema::create('detalle_poliza', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poliza_id')->nullable()->constrained('polizas');
            $table->string('cuenta', 100)->nullable();
            $table->decimal('debe', 12, 2)->nullable();
            $table->decimal('haber', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_poliza');
    }
};
