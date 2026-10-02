<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_aprobacion_profesional', function (Blueprint $table) {
            $table->id();

            $table->foreignId('profesional_id')
                ->constrained('profesionales')
                ->cascadeOnDelete();

            $table->json('especialidades_requieren_aprobacion');

            $table->enum('estado', [
                'pendiente',
                'aprobada',
                'rechazada'
            ])->default('pendiente');

            $table->foreignId('revisado_por')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete();

            $table->text('motivo_rechazo')->nullable();

            $table->timestamp('revisado_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_aprobacion_profesional');
    }
};