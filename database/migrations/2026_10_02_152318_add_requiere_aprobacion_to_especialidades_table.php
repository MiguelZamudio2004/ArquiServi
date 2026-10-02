<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('especialidades', function (Blueprint $table) {
            $table->boolean('requiere_aprobacion')
                ->default(false)
                ->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('especialidades', function (Blueprint $table) {
            $table->dropColumn('requiere_aprobacion');
        });
    }
};