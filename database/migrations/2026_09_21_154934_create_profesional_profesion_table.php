<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profesional_especialidad', function (Blueprint $table) {
            $table->foreignId('profesional_id')->constrained('profesionales')->onDelete('cascade');
            $table->foreignId('especialidad_id')->constrained('especialidades')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['profesional_id', 'especialidad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profesional_especialidad');
    }
};