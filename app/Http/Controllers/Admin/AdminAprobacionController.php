<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use App\Models\SolicitudAprobacionProfesional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAprobacionController extends Controller
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

        $estados = [
            'pendiente' => 'Pendiente',
            'aprobada' => 'Aprobada',
            'rechazada' => 'Rechazada'
        ];

        $query = SolicitudAprobacionProfesional::query()
            ->with([
                'profesional.usuario.rol',
                'profesional.profesiones',
                'profesional.especialidades',
                'revisor'
            ]);

        if ($buscar !== '') {
            $query->whereHas(
                'profesional.usuario',
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

        $solicitudes = $query
            ->orderByRaw("
                CASE
                    WHEN estado = 'pendiente' THEN 1
                    WHEN estado = 'rechazada' THEN 2
                    WHEN estado = 'aprobada' THEN 3
                    ELSE 4
                END
            ")
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalSolicitudes =
            SolicitudAprobacionProfesional::count();

        $totalPendientes =
            SolicitudAprobacionProfesional::where(
                'estado',
                'pendiente'
            )->count();

        $totalAprobadas =
            SolicitudAprobacionProfesional::where(
                'estado',
                'aprobada'
            )->count();

        $totalRechazadas =
            SolicitudAprobacionProfesional::where(
                'estado',
                'rechazada'
            )->count();

        return view(
            'admin.aprobaciones.index',
            compact(
                'solicitudes',
                'buscar',
                'estado',
                'estados',
                'totalSolicitudes',
                'totalPendientes',
                'totalAprobadas',
                'totalRechazadas'
            )
        );
    }

    public function mostrar(
        SolicitudAprobacionProfesional $solicitud
    ) {
        $solicitud->load([
            'profesional.usuario.rol',
            'profesional.profesiones',
            'profesional.especialidades.profesion',
            'revisor'
        ]);

        $especialidadesIds = collect(
            $solicitud
                ->especialidades_requieren_aprobacion
                ?? []
        )
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $especialidadesRevision = Especialidad::with(
            'profesion'
        )
            ->whereIn(
                'id',
                $especialidadesIds
            )
            ->orderBy('nombre')
            ->get();

        return view(
            'admin.aprobaciones.mostrar',
            compact(
                'solicitud',
                'especialidadesRevision'
            )
        );
    }

    public function aprobar(
        SolicitudAprobacionProfesional $solicitud
    ) {
        DB::transaction(
            function () use ($solicitud) {

                $solicitudBloqueada =
                    SolicitudAprobacionProfesional::whereKey(
                        $solicitud->id
                    )
                        ->lockForUpdate()
                        ->firstOrFail();

                if (
                    $solicitudBloqueada->estado !==
                    'pendiente'
                ) {
                    abort(
                        409,
                        'Esta solicitud ya fue revisada.'
                    );
                }

                $profesional =
                    $solicitudBloqueada
                        ->profesional()
                        ->lockForUpdate()
                        ->firstOrFail();

                $profesional->update([
                    'estado_aprobacion' =>
                        'aprobado'
                ]);

                $solicitudBloqueada->update([
                    'estado' =>
                        'aprobada',

                    'revisado_por' =>
                        auth()->id(),

                    'motivo_rechazo' =>
                        null,

                    'revisado_at' =>
                        now()
                ]);
            }
        );

        return redirect()
            ->route(
                'admin.aprobaciones.mostrar',
                $solicitud
            )
            ->with(
                'success',
                'El profesional fue aprobado correctamente.'
            );
    }

    public function rechazar(
        Request $request,
        SolicitudAprobacionProfesional $solicitud
    ) {
        $datos = $request->validate(
            [
                'motivo_rechazo' => [
                    'required',
                    'string'
                ]
            ],
            [
                'motivo_rechazo.required' =>
                    'Debes indicar el motivo del rechazo.'
            ]
        );

        DB::transaction(
            function () use (
                $solicitud,
                $datos
            ) {

                $solicitudBloqueada =
                    SolicitudAprobacionProfesional::whereKey(
                        $solicitud->id
                    )
                        ->lockForUpdate()
                        ->firstOrFail();

                if (
                    $solicitudBloqueada->estado !==
                    'pendiente'
                ) {
                    abort(
                        409,
                        'Esta solicitud ya fue revisada.'
                    );
                }

                $profesional =
                    $solicitudBloqueada
                        ->profesional()
                        ->lockForUpdate()
                        ->firstOrFail();

                $profesional->update([
                    'estado_aprobacion' =>
                        'rechazado'
                ]);

                $solicitudBloqueada->update([
                    'estado' =>
                        'rechazada',

                    'revisado_por' =>
                        auth()->id(),

                    'motivo_rechazo' =>
                        $datos['motivo_rechazo'],

                    'revisado_at' =>
                        now()
                ]);
            }
        );

        return redirect()
            ->route(
                'admin.aprobaciones.mostrar',
                $solicitud
            )
            ->with(
                'success',
                'La solicitud fue rechazada correctamente.'
            );
    }
}