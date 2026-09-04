<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        Rol::firstOrCreate([
            'nombre' => 'usuario',
        ]);

        Rol::firstOrCreate([
            'nombre' => 'profesional',
        ]);

        Rol::firstOrCreate([
            'nombre' => 'proveedor',
        ]);

        Rol::firstOrCreate([
            'nombre' => 'administrador',
        ]);
    }
}
