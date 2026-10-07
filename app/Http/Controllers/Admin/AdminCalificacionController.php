<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use Illuminate\Http\Request;

class AdminCalificacionController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim(
            (string) $request->query(
                'buscar',
                ''
            )
        );

        $tipo = $request->query(
            'tipo'
        );

        $promedio = $request->query(
            'promedio'
        );

        $tipos = [
            'profesional' => 'Profesional',
            'proveedor' => 'Proveedor',
            'cliente' => 'Cliente'
        ];

        $promedios = [
            '5.0' => '5.0',
            '4.5' => '4.5',
            '4.0' => '4.0',
            '3.5' => '3.5',
            '3.0' => '3.0',
            '2.5' => '2.5',
            '2.0' => '2.0',
            '1.5' => '1.5',
            '1.0' => '1.0',
            '0.5' => '0.5'
        ];

        $query = Calificacion::query()
            ->with([
                'evaluador.rol',
                'evaluado.rol',
                'solicitud.servicio',
                'solicitud.materiales'
            ]);

        if ($buscar !== '') {
            $query->where(
                function ($query) use ($buscar) {
                    $query
                        ->where(
                            'comentario',
                            'like',
                            '%' . $buscar . '%'
                        )
                        ->orWhereHas(
                            'evaluador',
                            function ($query) use ($buscar) {
                                $query
                                    ->where(
                                        'nombre',
                                        'like',
                                        '%' . $buscar . '%'
                                    )
                                    ->orWhere(
                                        'apellido_paterno',
                                        'like',
                                        '%' . $buscar . '%'
                                    )
                                    ->orWhere(
                                        'apellido_materno',
                                        'like',
                                        '%' . $buscar . '%'
                                    )
                                    ->orWhere(
                                        'correo',
                                        'like',
                                        '%' . $buscar . '%'
                                    );
                            }
                        )
                        ->orWhereHas(
                            'evaluado',
                            function ($query) use ($buscar) {
                                $query
                                    ->where(
                                        'nombre',
                                        'like',
                                        '%' . $buscar . '%'
                                    )
                                    ->orWhere(
                                        'apellido_paterno',
                                        'like',
                                        '%' . $buscar . '%'
                                    )
                                    ->orWhere(
                                        'apellido_materno',
                                        'like',
                                        '%' . $buscar . '%'
                                    )
                                    ->orWhere(
                                        'correo',
                                        'like',
                                        '%' . $buscar . '%'
                                    );
                            }
                        );
                }
            );
        }

        if (
            $tipo &&
            array_key_exists(
                $tipo,
                $tipos
            )
        ) {
            $query->where(
                'tipo_evaluado',
                $tipo
            );
        }

        if (
            $promedio !== null &&
            $promedio !== '' &&
            array_key_exists(
                $promedio,
                $promedios
            )
        ) {
            $query->where(
                'promedio',
                (float) $promedio
            );
        }

        $calificaciones = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalCalificaciones =
            Calificacion::count();

        $promedioGeneral =
            Calificacion::count() > 0
                ? round(
                    (float) Calificacion::avg(
                        'promedio'
                    ),
                    1
                )
                : 0;

        $totalProfesionales =
            Calificacion::where(
                'tipo_evaluado',
                'profesional'
            )->count();

        $totalProveedores =
            Calificacion::where(
                'tipo_evaluado',
                'proveedor'
            )->count();

        return view(
            'admin.calificaciones.index',
            compact(
                'calificaciones',
                'buscar',
                'tipo',
                'promedio',
                'tipos',
                'promedios',
                'totalCalificaciones',
                'promedioGeneral',
                'totalProfesionales',
                'totalProveedores'
            )
        );
    }

    public function mostrar(
        Calificacion $calificacion
    ) {
        $calificacion->load([
            'evaluador.rol',
            'evaluado.rol',
            'solicitud.solicitante.rol',
            'solicitud.destinatario.rol',
            'solicitud.servicio',
            'solicitud.materiales'
        ]);

        $criteriosEtiquetas =
            $this->obtenerCriteriosEtiquetas(
                $calificacion->tipo_evaluado
            );

        return view(
            'admin.calificaciones.mostrar',
            compact(
                'calificacion',
                'criteriosEtiquetas'
            )
        );
    }

    private function obtenerCriteriosEtiquetas(
        string $tipo
    ): array {
        return match ($tipo) {
            'profesional' => [
                'calidad_trabajo' =>
                    'Calidad del trabajo',

                'puntualidad' =>
                    'Puntualidad',

                'comunicacion' =>
                    'Comunicación',

                'profesionalismo' =>
                    'Profesionalismo',

                'cumplimiento' =>
                    'Cumplimiento'
            ],

            'proveedor' => [
                'calidad_materiales' =>
                    'Calidad de los materiales',

                'cumplimiento_entrega' =>
                    'Cumplimiento de entrega',

                'comunicacion' =>
                    'Comunicación',

                'atencion' =>
                    'Atención',

                'cumplimiento' =>
                    'Cumplimiento'
            ],

            'cliente' => [
                'claridad_requerimientos' =>
                    'Claridad de requerimientos',

                'comunicacion' =>
                    'Comunicación',

                'trato' =>
                    'Trato',

                'responsabilidad' =>
                    'Responsabilidad',

                'cumplimiento' =>
                    'Cumplimiento'
            ],

            default => []
        };
    }
}