<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('especialidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profesion_id')->constrained('profesiones')->onDelete('cascade');
            $table->string('nombre', 100);
            $table->string('descripcion', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['profesion_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especialidades');
    }
};