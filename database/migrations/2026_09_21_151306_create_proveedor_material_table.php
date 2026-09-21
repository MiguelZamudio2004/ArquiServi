<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedor_material', function (Blueprint $table) {
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materiales')->onDelete('cascade');
            $table->boolean('disponible')->default(true);
            $table->timestamps();

            $table->primary(['proveedor_id', 'material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedor_material');
    }
};