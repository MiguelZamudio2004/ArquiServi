<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profesionales', function (Blueprint $table) {
            $table->enum('estado_aprobacion', [
                'no_requerida',
                'pendiente',
                'aprobado',
                'rechazado'
            ])
                ->default('no_requerida')
                ->after('zona_trabajo');
        });
    }

    public function down(): void
    {
        Schema::table('profesionales', function (Blueprint $table) {
            $table->dropColumn('estado_aprobacion');
        });
    }
};