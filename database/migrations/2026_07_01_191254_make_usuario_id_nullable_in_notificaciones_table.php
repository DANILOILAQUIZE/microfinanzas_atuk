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
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });
        
        DB::statement('ALTER TABLE notificaciones MODIFY usuario_id BIGINT UNSIGNED NULL');
        
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });
        
        DB::statement('ALTER TABLE notificaciones MODIFY usuario_id BIGINT UNSIGNED NOT NULL');
        
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }
};
