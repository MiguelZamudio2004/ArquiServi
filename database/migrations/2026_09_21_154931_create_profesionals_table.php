<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profesionales', function (Blueprint $table) {
        $table->id();
        $table->foreignId('usuario_id')->unique()->constrained('usuarios')->onDelete('cascade');
        $table->unsignedInteger('anios_experiencia')->default(0);
        $table->text('descripcion');
        $table->string('portafolio_url', 500)->nullable();
        $table->string('zona_trabajo', 200);
        $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('profesionales');
    }
};