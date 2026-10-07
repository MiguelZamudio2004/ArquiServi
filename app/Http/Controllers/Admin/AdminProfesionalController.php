<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profesional;
use App\Models\Profesion;
use Illuminate\Http\Request;

class AdminProfesionalController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim(
            (string) $request->query('buscar', '')
        );

        $profesionId = $request->query('profesion');

        $estadoAprobacion = $request->query(
            'estado_aprobacion'
        );

        $estadoCuenta = $request->query(
            'estado_cuenta'
        );

        $estadosAprobacion = [
            'no_requerida' => 'No requiere aprobación',
            'pendiente' => 'Pendiente',
            'aprobado' => 'Aprobado',
            'rechazado' => 'Rechazado'
        ];

        $estadosCuenta = [
            'activo' => 'Activo',
            'inactivo' => 'Inactivo',
            'suspendido' => 'Suspendido'
        ];

        $query = Profesional::query()
            ->with([
                'usuario.rol',
                'profesiones',
                'especialidades'
            ]);

        if ($buscar !== '') {
            $query->where(
                function ($query) use ($buscar) {
                    $query
                        ->where(
                            'zona_trabajo',
                            'like',
                            '%' . $buscar . '%'
                        )
                        ->orWhereHas(
                            'usuario',
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
                                    )
                                    ->orWhere(
                                        'telefono',
                                        'like',
                                        '%' . $buscar . '%'
                                    );
                            }
                        );
                }
            );
        }

        if (
            $profesionId !== null &&
            $profesionId !== ''
        ) {
            $query->whereHas(
                'profesiones',
                function ($query) use ($profesionId) {
                    $query->where(
                        'profesiones.id',
                        $profesionId
                    );
                }
            );
        }

        if (
            $estadoAprobacion &&
            array_key_exists(
                $estadoAprobacion,
                $estadosAprobacion
            )
        ) {
            $query->where(
                'estado_aprobacion',
                $estadoAprobacion
            );
        }

        if (
            $estadoCuenta &&
            array_key_exists(
                $estadoCuenta,
                $estadosCuenta
            )
        ) {
            $query->whereHas(
                'usuario',
                function ($query) use ($estadoCuenta) {
                    $query->where(
                        'estado',
                        $estadoCuenta
                    );
                }
            );
        }

        $profesionales = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $profesiones = Profesion::where(
            'activo',
            true
        )
            ->orderBy('nombre')
            ->get();

        $totalProfesionales = Profesional::count();

        $totalPublicos = Profesional::whereIn(
            'estado_aprobacion',
            [
                'no_requerida',
                'aprobado'
            ]
        )
            ->whereHas(
                'usuario',
                function ($query) {
                    $query->where(
                        'estado',
                        'activo'
                    );
                }
            )
            ->count();

        $totalPendientes = Profesional::where(
            'estado_aprobacion',
            'pendiente'
        )->count();

        $totalRechazados = Profesional::where(
            'estado_aprobacion',
            'rechazado'
        )->count();

        return view(
            'admin.profesionales.index',
            compact(
                'profesionales',
                'profesiones',
                'buscar',
                'profesionId',
                'estadoAprobacion',
                'estadoCuenta',
                'estadosAprobacion',
                'estadosCuenta',
                'totalProfesionales',
                'totalPublicos',
                'totalPendientes',
                'totalRechazados'
            )
        );
    }

    public function mostrar(
        Profesional $profesional
    ) {
        $profesional->load([
            'usuario.rol',
            'profesiones',
            'especialidades.profesion',
            'servicios'
        ]);

        $estadosAprobacion = [
            'no_requerida' => 'No requiere aprobación',
            'pendiente' => 'Pendiente',
            'aprobado' => 'Aprobado',
            'rechazado' => 'Rechazado'
        ];

        return view(
            'admin.profesionales.mostrar',
            compact(
                'profesional',
                'estadosAprobacion'
            )
        );
    }
}