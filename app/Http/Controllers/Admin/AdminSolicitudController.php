<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class AdminSolicitudController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim(
            (string) $request->query(
                'buscar',
                ''
            )
        );

        $estado = $request->query(
            'estado'
        );

        $tipo = $request->query(
            'tipo'
        );

        $estados = [
            'pendiente' => 'Pendiente',
            'aceptada' => 'Aceptada',
            'rechazada' => 'Rechazada',
            'cancelada' => 'Cancelada',
            'terminada' => 'Terminada'
        ];

        $tipos = [
            'profesional' => 'Profesional',
            'proveedor' => 'Proveedor'
        ];

        $query = Solicitud::query()
            ->with([
                'solicitante.rol',
                'destinatario.rol',
                'servicio',
                'materiales',
                'calificaciones'
            ]);

        if ($buscar !== '') {
            $query->where(function ($query) use ($buscar) {
                $query
                    ->where(
                        'descripcion',
                        'like',
                        '%' . $buscar . '%'
                    )
                    ->orWhereHas(
                        'solicitante',
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
                        'destinatario',
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
                        'servicio',
                        function ($query) use ($buscar) {
                            $query->where(
                                'nombre',
                                'like',
                                '%' . $buscar . '%'
                            );
                        }
                    )
                    ->orWhereHas(
                        'materiales',
                        function ($query) use ($buscar) {
                            $query->where(
                                'materiales.nombre',
                                'like',
                                '%' . $buscar . '%'
                            );
                        }
                    );
            });
        }

        if (
            $estado &&
            array_key_exists(
                $estado,
                $estados
            )
        ) {
            $query->where(
                'estado',
                $estado
            );
        }

        if (
            $tipo &&
            array_key_exists(
                $tipo,
                $tipos
            )
        ) {
            $query->whereHas(
                'destinatario.rol',
                function ($query) use ($tipo) {
                    $query->where(
                        'nombre',
                        $tipo
                    );
                }
            );
        }

        $solicitudes = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalSolicitudes =
            Solicitud::count();

        $totalPendientes =
            Solicitud::where(
                'estado',
                'pendiente'
            )->count();

        $totalAceptadas =
            Solicitud::where(
                'estado',
                'aceptada'
            )->count();

        $totalTerminadas =
            Solicitud::where(
                'estado',
                'terminada'
            )->count();

        return view(
            'admin.solicitudes.index',
            compact(
                'solicitudes',
                'buscar',
                'estado',
                'tipo',
                'estados',
                'tipos',
                'totalSolicitudes',
                'totalPendientes',
                'totalAceptadas',
                'totalTerminadas'
            )
        );
    }

    public function mostrar(
        Solicitud $solicitud
    ) {
        $solicitud->load([
            'solicitante.rol',
            'destinatario.rol',
            'servicio',
            'materiales',
            'calificaciones'
        ]);

        return view(
            'admin.solicitudes.mostrar',
            compact(
                'solicitud'
            )
        );
    }
}