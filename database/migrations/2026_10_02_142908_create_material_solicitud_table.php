<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_solicitud', function (Blueprint $table) {
            $table->id();

            $table->foreignId('solicitud_id')
                ->constrained('solicitudes')
                ->cascadeOnDelete();

            $table->foreignId('material_id')
                ->constrained('materiales')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['solicitud_id', 'material_id']);
        });

        if (Schema::hasColumn('solicitudes', 'material_id')) {
            DB::table('solicitudes')
                ->whereNotNull('material_id')
                ->orderBy('id')
                ->chunkById(100, function ($solicitudes) {
                    foreach ($solicitudes as $solicitud) {
                        DB::table('material_solicitud')->insertOrIgnore([
                            'solicitud_id' => $solicitud->id,
                            'material_id' => $solicitud->material_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('material_solicitud');
    }
};