<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\Profesion;
use Illuminate\Database\Seeder;

class ProfesionSeeder extends Seeder
{
    public function run(): void
    {
        $profesiones = [
            [
                'nombre' => 'Arquitectura',
                'descripcion' => 'Profesionales dedicados al diseño, planificación y desarrollo de proyectos arquitectónicos.',
                'especialidades' => [
                    [
                        'nombre' => 'Diseño arquitectónico',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Urbanismo',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Paisajismo',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Restauración arquitectónica',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Diseño de interiores',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Supervisor de obra',
                        'requiere_aprobacion' => true,
                    ],
                ],
            ],

            [
                'nombre' => 'Ingeniería Civil',
                'descripcion' => 'Profesionales especializados en construcción, estructuras e infraestructura.',
                'especialidades' => [
                    [
                        'nombre' => 'Estructuras',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Construcción',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Infraestructura',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Geotecnia',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Hidráulica',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Vías terrestres',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Administración de obra',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Supervisor de obra',
                        'requiere_aprobacion' => true,
                    ],
                ],
            ],

            [
                'nombre' => 'Ingeniería Arquitectónica',
                'descripcion' => 'Profesionales enfocados en arquitectura, construcción, estructuras e instalaciones.',
                'especialidades' => [
                    [
                        'nombre' => 'Diseño arquitectónico',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Construcción',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Estructuras',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Instalaciones',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Administración de obra',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Supervisor de obra',
                        'requiere_aprobacion' => true,
                    ],
                ],
            ],

            [
                'nombre' => 'Ingeniería Constructora o Municipal',
                'descripcion' => 'Profesionales dedicados a la construcción, administración e infraestructura urbana.',
                'especialidades' => [
                    [
                        'nombre' => 'Construcción',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Infraestructura urbana',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Administración de obra',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Mantenimiento de infraestructura',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Obras municipales',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Supervisor de obra',
                        'requiere_aprobacion' => true,
                    ],
                ],
            ],

            [
                'nombre' => 'Técnicos en Construcción',
                'descripcion' => 'Técnicos especializados en procesos constructivos, control, instalaciones y acabados.',
                'especialidades' => [
                    [
                        'nombre' => 'Procesos constructivos',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Control de obra',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Instalaciones',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Acabados',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Mantenimiento',
                        'requiere_aprobacion' => false,
                    ],
                    [
                        'nombre' => 'Supervisor de obra',
                        'requiere_aprobacion' => true,
                    ],
                ],
            ],
        ];

        foreach ($profesiones as $datosProfesion) {
            $profesion = Profesion::updateOrCreate(
                [
                    'nombre' =>
                        $datosProfesion['nombre']
                ],
                [
                    'descripcion' =>
                        $datosProfesion['descripcion'],

                    'activo' =>
                        true
                ]
            );

            foreach (
                $datosProfesion['especialidades']
                as $datosEspecialidad
            ) {
                Especialidad::updateOrCreate(
                    [
                        'profesion_id' =>
                            $profesion->id,

                        'nombre' =>
                            $datosEspecialidad['nombre']
                    ],
                    [
                        'activo' => true,

                        'requiere_aprobacion' =>
                            $datosEspecialidad[
                                'requiere_aprobacion'
                            ]
                    ]
                );
            }
        }
    }
}