<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('solicitud_id')
                ->constrained('solicitudes')
                ->cascadeOnDelete();

            $table->foreignId('evaluador_id')
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->foreignId('evaluado_id')
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->enum('tipo_evaluado', [
                'profesional',
                'cliente',
                'proveedor'
            ]);

            $table->json('criterios');

            $table->decimal('promedio', 2, 1);

            $table->text('comentario')->nullable();

            $table->timestamps();

            $table->unique([
                'solicitud_id',
                'evaluador_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};