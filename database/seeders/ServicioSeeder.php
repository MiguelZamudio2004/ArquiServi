<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        $servicios = [
            'Diseño de planos',
            'Diseño arquitectónico',
            'Diseño de interiores',
            'Levantamiento arquitectónico',
            'Modelado 3D',
            'Renderizado arquitectónico',
            'Supervisión de obra',
            'Construcción',
            'Remodelación',
            'Ampliación de vivienda',
            'Administración de obra',
            'Presupuesto de obra',
            'Estimación de costos',
            'Diseño estructural',
            'Cálculo estructural',
            'Revisión estructural',
            'Instalación eléctrica',
            'Instalación hidráulica',
            'Instalación sanitaria',
            'Mantenimiento eléctrico',
            'Mantenimiento hidráulico',
            'Impermeabilización',
            'Pintura',
            'Albañilería',
            'Carpintería',
            'Herrería',
            'Colocación de pisos',
            'Colocación de azulejo',
            'Acabados',
            'Paisajismo',
            'Restauración arquitectónica',
            'Mantenimiento de inmuebles',
        ];

        foreach ($servicios as $nombre) {
            Servicio::firstOrCreate([
                'nombre' => $nombre,
            ]);
        }
    }
}