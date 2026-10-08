<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profesional;
use App\Models\Profesion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProfesionalController extends Controller
{
    private const ESTADOS_APROBACION = [
        'no_requerida' => 'No requiere aprobación',
        'pendiente' => 'Pendiente',
        'aprobado' => 'Aprobado',
        'rechazado' => 'Rechazado',
    ];

    private const ESTADOS_CUENTA = [
        'activo' => 'Activo',
        'inactivo' => 'Inactivo',
        'suspendido' => 'Suspendido',
    ];

    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $profesionId = $request->query('profesion');
        $estadoAprobacion = $request->query('estado_aprobacion');
        $estadoCuenta = $request->query('estado_cuenta');

        $query = Profesional::query()->with([
            'usuario.rol',
            'profesiones',
            'especialidades',
        ]);

        if ($buscar !== '') {
            $this->aplicarBusqueda($query, $buscar);
        }

        if ($profesionId !== null && $profesionId !== '') {
            $query->whereHas('profesiones', fn (Builder $query) =>
                $query->where('profesiones.id', $profesionId)
            );
        }

        if (is_string($estadoAprobacion) && isset(self::ESTADOS_APROBACION[$estadoAprobacion])) {
            $query->where('estado_aprobacion', $estadoAprobacion);
        }

        if (is_string($estadoCuenta) && isset(self::ESTADOS_CUENTA[$estadoCuenta])) {
            $query->whereRelation('usuario', 'estado', $estadoCuenta);
        }

        $profesionales = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $profesiones = Profesion::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $estadisticas = $this->obtenerEstadisticas();

        return view('admin.profesionales.index', [
            'profesionales' => $profesionales,
            'profesiones' => $profesiones,
            'buscar' => $buscar,
            'profesionId' => $profesionId,
            'estadoAprobacion' => $estadoAprobacion,
            'estadoCuenta' => $estadoCuenta,
            'estadosAprobacion' => self::ESTADOS_APROBACION,
            'estadosCuenta' => self::ESTADOS_CUENTA,
            'totalProfesionales' => (int) $estadisticas->total,
            'totalPublicos' => (int) $estadisticas->publicos,
            'totalPendientes' => (int) $estadisticas->pendientes,
            'totalRechazados' => (int) $estadisticas->rechazados,
        ]);
    }

    public function mostrar(Profesional $profesional): View
    {
        $profesional->load([
            'usuario.rol',
            'profesiones',
            'especialidades.profesion',
            'servicios',
        ]);

        return view('admin.profesionales.mostrar', [
            'profesional' => $profesional,
            'estadosAprobacion' => self::ESTADOS_APROBACION,
        ]);
    }

    private function aplicarBusqueda(Builder $query, string $buscar): void
    {
        $query->where(function (Builder $query) use ($buscar) {
            $query->where('zona_trabajo', 'like', "%{$buscar}%")
                ->orWhereHas('usuario', function (Builder $usuario) use ($buscar) {
                    $usuario->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                        ->orWhere('apellido_materno', 'like', "%{$buscar}%")
                        ->orWhere('correo', 'like', "%{$buscar}%")
                        ->orWhere('telefono', 'like', "%{$buscar}%");
                });
        });
    }

    private function obtenerEstadisticas(): Profesional
    {
        return Profesional::query()
            ->leftJoin('usuarios', 'usuarios.id', '=', 'profesionales.usuario_id')
            ->selectRaw('COUNT(profesionales.id) AS total')
            ->selectRaw("
                SUM(
                    CASE
                        WHEN profesionales.estado_aprobacion IN ('no_requerida', 'aprobado')
                        AND usuarios.estado = 'activo'
                        THEN 1 ELSE 0
                    END
                ) AS publicos
            ")
            ->selectRaw("
                SUM(
                    CASE
                        WHEN profesionales.estado_aprobacion = 'pendiente'
                        THEN 1 ELSE 0
                    END
                ) AS pendientes
            ")
            ->selectRaw("
                SUM(
                    CASE
                        WHEN profesionales.estado_aprobacion = 'rechazado'
                        THEN 1 ELSE 0
                    END
                ) AS rechazados
            ")
            ->firstOrFail();
    }
}