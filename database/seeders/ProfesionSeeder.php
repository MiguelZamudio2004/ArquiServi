<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\Profesion;
use Illuminate\Database\Seeder;

class ProfesionSeeder extends Seeder
{
    public function run(): void
    {
        $arquitectura = Profesion::firstOrCreate(
            ['nombre' => 'Arquitectura'],
            ['descripcion' => 'Profesionales dedicados al diseño y planificación arquitectónica.']
        );

        Especialidad::firstOrCreate(['profesion_id' => $arquitectura->id, 'nombre' => 'Diseño arquitectónico']);
        Especialidad::firstOrCreate(['profesion_id' => $arquitectura->id, 'nombre' => 'Urbanismo']);
        Especialidad::firstOrCreate(['profesion_id' => $arquitectura->id, 'nombre' => 'Paisajismo']);
        Especialidad::firstOrCreate(['profesion_id' => $arquitectura->id, 'nombre' => 'Restauración']);

        $ingenieriaCivil = Profesion::firstOrCreate(
            ['nombre' => 'Ingeniería Civil'],
            ['descripcion' => 'Profesionales relacionados con construcción e infraestructura.']
        );

        Especialidad::firstOrCreate(['profesion_id' => $ingenieriaCivil->id, 'nombre' => 'Estructuras']);
        Especialidad::firstOrCreate(['profesion_id' => $ingenieriaCivil->id, 'nombre' => 'Construcción']);
        Especialidad::firstOrCreate(['profesion_id' => $ingenieriaCivil->id, 'nombre' => 'Supervisión de obra']);
    }
}