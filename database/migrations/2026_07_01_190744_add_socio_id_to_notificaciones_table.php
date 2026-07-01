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
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->foreignId('socio_id')->nullable()->after('usuario_id')->constrained('socios');
            $table->string('canal', 20)->default('SISTEMA')->after('tipo'); // SISTEMA, EMAIL, SMS
            $table->boolean('enviada')->default(false)->after('leida');
            $table->timestamp('fecha_envio')->nullable()->after('enviada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropForeign(['socio_id']);
            $table->dropColumn(['socio_id', 'canal', 'enviada', 'fecha_envio']);
        });
    }
};
