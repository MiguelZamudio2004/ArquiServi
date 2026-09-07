<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('codigo_recuperacion', function (Blueprint $table) {

            // Elimina la relación incorrecta con users
            $table->dropForeign(['usuario_id']);

            // Crea la relación correcta con usuarios
            $table->foreign('usuario_id')
                ->references('id')
                ->on('usuarios')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('codigo_recuperacion', function (Blueprint $table) {

            $table->dropForeign(['usuario_id']);

            $table->foreign('usuario_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }
};
