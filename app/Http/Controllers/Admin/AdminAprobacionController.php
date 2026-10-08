<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use App\Models\SolicitudAprobacionProfesional;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAprobacionController extends Controller
{
    private const ESTADOS = [
        'pendiente' => 'Pendiente',
        'aprobada' => 'Aprobada',
        'rechazada' => 'Rechazada',
    ];

    private const ESTADO_PENDIENTE = 'pendiente';
    private const ESTADO_APROBADA = 'aprobada';
    private const ESTADO_RECHAZADA = 'rechazada';

    private const PROFESIONAL_APROBADO = 'aprobado';
    private const PROFESIONAL_RECHAZADO = 'rechazado';

    public function index(Request $request)
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $estado = $request->query('estado');

        $query = SolicitudAprobacionProfesional::query()
            ->with([
                'profesional.usuario.rol',
                'profesional.profesiones',
                'profesional.especialidades',
                'revisor',
            ]);

        if ($buscar !== '') {
            $this->aplicarBusqueda($query, $buscar);
        }

        if (is_string($estado) && isset(self::ESTADOS[$estado])) {
            $query->where('estado', $estado);
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

        $estadisticas = $this->obtenerEstadisticas();

        return view('admin.aprobaciones.index', [
            'solicitudes' => $solicitudes,
            'buscar' => $buscar,
            'estado' => $estado,
            'estados' => self::ESTADOS,
            'totalSolicitudes' => $estadisticas->total,
            'totalPendientes' => $estadisticas->pendientes,
            'totalAprobadas' => $estadisticas->aprobadas,
            'totalRechazadas' => $estadisticas->rechazadas,
        ]);
    }

    public function mostrar(SolicitudAprobacionProfesional $solicitud)
    {
        $solicitud->load([
            'profesional.usuario.rol',
            'profesional.profesiones',
            'profesional.especialidades.profesion',
            'revisor',
        ]);

        $especialidadesIds = collect(
            $solicitud->especialidades_requieren_aprobacion ?? []
        )
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $especialidadesRevision = Especialidad::query()
            ->with('profesion')
            ->whereIn('id', $especialidadesIds)
            ->orderBy('nombre')
            ->get();

        return view('admin.aprobaciones.mostrar', compact(
            'solicitud',
            'especialidadesRevision'
        ));
    }

    public function aprobar(SolicitudAprobacionProfesional $solicitud)
    {
        $this->revisarSolicitud(
            $solicitud,
            self::ESTADO_APROBADA,
            self::PROFESIONAL_APROBADO
        );

        return redirect()
            ->route('admin.aprobaciones.mostrar', $solicitud)
            ->with(
                'success',
                'La solicitud de aprobación de especialidad fue aprobada correctamente.'
            );
    }

    public function rechazar(
        Request $request,
        SolicitudAprobacionProfesional $solicitud
    ) {
        $datos = $request->validate(
            [
                'motivo_rechazo' => ['required', 'string'],
            ],
            [
                'motivo_rechazo.required' => 'Debes indicar el motivo del rechazo.',
            ]
        );

        $this->revisarSolicitud(
            $solicitud,
            self::ESTADO_RECHAZADA,
            self::PROFESIONAL_RECHAZADO,
            $datos['motivo_rechazo']
        );

        return redirect()
            ->route('admin.aprobaciones.mostrar', $solicitud)
            ->with(
                'success',
                'La solicitud de especialidad fue rechazada. El perfil profesional continuará visible en el catálogo.'
            );
    }

    private function aplicarBusqueda(Builder $query, string $buscar): void
    {
        $query->whereHas(
            'profesional.usuario',
            function (Builder $query) use ($buscar) {
                $query
                    ->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                    ->orWhere('apellido_materno', 'like', "%{$buscar}%")
                    ->orWhere('correo', 'like', "%{$buscar}%");
            }
        );
    }

    private function obtenerEstadisticas(): object
    {
        return SolicitudAprobacionProfesional::query()
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw("
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) AS pendientes
            ")
            ->selectRaw("
                SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) AS aprobadas
            ")
            ->selectRaw("
                SUM(CASE WHEN estado = 'rechazada' THEN 1 ELSE 0 END) AS rechazadas
            ")
            ->first();
    }

    private function revisarSolicitud(
        SolicitudAprobacionProfesional $solicitud,
        string $estadoSolicitud,
        string $estadoProfesional,
        ?string $motivoRechazo = null
    ): void {
        DB::transaction(function () use (
            $solicitud,
            $estadoSolicitud,
            $estadoProfesional,
            $motivoRechazo
        ) {
            $solicitudBloqueada = SolicitudAprobacionProfesional::query()
                ->whereKey($solicitud->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if(
                $solicitudBloqueada->estado !== self::ESTADO_PENDIENTE,
                409,
                'Esta solicitud ya fue revisada.'
            );

            $profesional = $solicitudBloqueada
                ->profesional()
                ->lockForUpdate()
                ->firstOrFail();

            $profesional->update([
                'estado_aprobacion' => $estadoProfesional,
            ]);

            $solicitudBloqueada->update([
                'estado' => $estadoSolicitud,
                'revisado_por' => auth()->id(),
                'motivo_rechazo' => $motivoRechazo,
                'revisado_at' => now(),
            ]);
        });
    }
}