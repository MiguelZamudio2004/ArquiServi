<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profesional_profesion', function (Blueprint $table) {
            $table->foreignId('profesional_id')
                ->constrained('profesionales')
                ->cascadeOnDelete();

            $table->foreignId('profesion_id')
                ->constrained('profesiones')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->primary(['profesional_id', 'profesion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profesional_profesion');
    }
};