<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSolicitudController extends Controller
{
    private const ESTADOS = [
        'pendiente' => 'Pendiente',
        'aceptada' => 'Aceptada',
        'rechazada' => 'Rechazada',
        'cancelada' => 'Cancelada',
        'terminada' => 'Terminada',
    ];

    private const TIPOS = [
        'profesional' => 'Profesional',
        'proveedor' => 'Proveedor',
    ];

    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $estado = $request->query('estado');
        $tipo = $request->query('tipo');

        $query = Solicitud::query()->with([
            'solicitante.rol',
            'destinatario.rol',
            'servicio',
            'materiales',
        ]);

        if ($buscar !== '') {
            $this->aplicarBusqueda($query, $buscar);
        }

        if (is_string($estado) && isset(self::ESTADOS[$estado])) {
            $query->where('estado', $estado);
        }

        if (is_string($tipo) && isset(self::TIPOS[$tipo])) {
            $query->whereHas('destinatario.rol', function (Builder $query) use ($tipo) {
                $query->where('nombre', $tipo);
            });
        }

        $solicitudes = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $estadisticas = $this->obtenerEstadisticas();

        return view('admin.solicitudes.index', [
            'solicitudes' => $solicitudes,
            'buscar' => $buscar,
            'estado' => $estado,
            'tipo' => $tipo,
            'estados' => self::ESTADOS,
            'tipos' => self::TIPOS,
            'totalSolicitudes' => (int) $estadisticas->total,
            'totalPendientes' => (int) $estadisticas->pendientes,
            'totalAceptadas' => (int) $estadisticas->aceptadas,
            'totalTerminadas' => (int) $estadisticas->terminadas,
        ]);
    }

    public function mostrar(Solicitud $solicitud): View
    {
        $solicitud->load([
            'solicitante.rol',
            'destinatario.rol',
            'servicio',
            'materiales',
            'calificaciones',
        ]);

        return view('admin.solicitudes.mostrar', compact('solicitud'));
    }

    private function aplicarBusqueda(Builder $query, string $buscar): void
    {
        $query->where(function (Builder $query) use ($buscar) {
            $query->where('descripcion', 'like', "%{$buscar}%")
                ->orWhereHas('solicitante', function (Builder $usuario) use ($buscar) {
                    $this->aplicarBusquedaUsuario($usuario, $buscar);
                })
                ->orWhereHas('destinatario', function (Builder $usuario) use ($buscar) {
                    $this->aplicarBusquedaUsuario($usuario, $buscar);
                })
                ->orWhereHas('servicio', function (Builder $servicio) use ($buscar) {
                    $servicio->where('nombre', 'like', "%{$buscar}%");
                })
                ->orWhereHas('materiales', function (Builder $material) use ($buscar) {
                    $material->where('materiales.nombre', 'like', "%{$buscar}%");
                });
        });
    }

    private function aplicarBusquedaUsuario(Builder $query, string $buscar): void
    {
        $query->where(function (Builder $query) use ($buscar) {
            $query->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                ->orWhere('apellido_materno', 'like', "%{$buscar}%")
                ->orWhere('correo', 'like', "%{$buscar}%");
        });
    }

    private function obtenerEstadisticas(): Solicitud
    {
        return Solicitud::query()
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw("
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) AS pendientes
            ")
            ->selectRaw("
                SUM(CASE WHEN estado = 'aceptada' THEN 1 ELSE 0 END) AS aceptadas
            ")
            ->selectRaw("
                SUM(CASE WHEN estado = 'terminada' THEN 1 ELSE 0 END) AS terminadas
            ")
            ->firstOrFail();
    }
}