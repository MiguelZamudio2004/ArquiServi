<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->foreignId('solicitante_id')
                ->nullable()
                ->after('id')
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->foreignId('destinatario_id')
                ->nullable()
                ->after('solicitante_id')
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->foreignId('material_id')
                ->nullable()
                ->after('servicio_id')
                ->constrained('materiales')
                ->nullOnDelete();
        });

        DB::statement("
            UPDATE solicitudes s
            INNER JOIN profesionales p
                ON p.id = s.profesional_id
            SET
                s.solicitante_id = s.usuario_id,
                s.destinatario_id = p.usuario_id
        ");

        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('usuario_id');
            $table->dropConstrainedForeignId('profesional_id');
        });

        DB::statement("
            ALTER TABLE solicitudes
            MODIFY solicitante_id BIGINT UNSIGNED NOT NULL,
            MODIFY destinatario_id BIGINT UNSIGNED NOT NULL,
            MODIFY servicio_id BIGINT UNSIGNED NULL
        ");
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->foreignId('profesional_id')
                ->nullable()
                ->constrained('profesionales')
                ->cascadeOnDelete();
        });

        DB::statement("
            UPDATE solicitudes s
            INNER JOIN profesionales p
                ON p.usuario_id = s.destinatario_id
            SET
                s.usuario_id = s.solicitante_id,
                s.profesional_id = p.id
        ");

        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('material_id');
            $table->dropConstrainedForeignId('solicitante_id');
            $table->dropConstrainedForeignId('destinatario_id');
        });
    }
};