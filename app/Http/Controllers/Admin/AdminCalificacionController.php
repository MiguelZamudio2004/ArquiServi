<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCalificacionController extends Controller
{
    private const TIPOS = [
        'profesional' => 'Profesional',
        'proveedor' => 'Proveedor',
        'cliente' => 'Cliente',
    ];

    private const PROMEDIOS = [
        '5.0' => '5.0',
        '4.5' => '4.5',
        '4.0' => '4.0',
        '3.5' => '3.5',
        '3.0' => '3.0',
        '2.5' => '2.5',
        '2.0' => '2.0',
        '1.5' => '1.5',
        '1.0' => '1.0',
        '0.5' => '0.5',
    ];

    private const CRITERIOS = [
        'profesional' => [
            'calidad_trabajo' => 'Calidad del trabajo',
            'puntualidad' => 'Puntualidad',
            'comunicacion' => 'Comunicación',
            'profesionalismo' => 'Profesionalismo',
            'cumplimiento' => 'Cumplimiento',
        ],
        'proveedor' => [
            'calidad_materiales' => 'Calidad de los materiales',
            'cumplimiento_entrega' => 'Cumplimiento de entrega',
            'comunicacion' => 'Comunicación',
            'atencion' => 'Atención',
            'cumplimiento' => 'Cumplimiento',
        ],
        'cliente' => [
            'claridad_requerimientos' => 'Claridad de requerimientos',
            'comunicacion' => 'Comunicación',
            'trato' => 'Trato',
            'responsabilidad' => 'Responsabilidad',
            'cumplimiento' => 'Cumplimiento',
        ],
    ];

    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $tipo = $request->query('tipo');
        $promedio = $request->query('promedio');

        $query = Calificacion::query()->with([
            'evaluador.rol',
            'evaluado.rol',
            'solicitud.servicio',
            'solicitud.materiales',
        ]);

        if ($buscar !== '') {
            $this->aplicarBusqueda($query, $buscar);
        }

        if (is_string($tipo) && isset(self::TIPOS[$tipo])) {
            $query->where('tipo_evaluado', $tipo);
        }

        if (is_string($promedio) && isset(self::PROMEDIOS[$promedio])) {
            $query->where('promedio', (float) $promedio);
        }

        $calificaciones = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $estadisticas = $this->obtenerEstadisticas();

        return view('admin.calificaciones.index', [
            'calificaciones' => $calificaciones,
            'buscar' => $buscar,
            'tipo' => $tipo,
            'promedio' => $promedio,
            'tipos' => self::TIPOS,
            'promedios' => self::PROMEDIOS,
            'totalCalificaciones' => (int) $estadisticas->total,
            'promedioGeneral' => (float) $estadisticas->promedio_general,
            'totalProfesionales' => (int) $estadisticas->profesionales,
            'totalProveedores' => (int) $estadisticas->proveedores,
        ]);
    }

    public function mostrar(Calificacion $calificacion): View
    {
        $calificacion->load([
            'evaluador.rol',
            'evaluado.rol',
            'solicitud.solicitante.rol',
            'solicitud.destinatario.rol',
            'solicitud.servicio',
            'solicitud.materiales',
        ]);

        $criteriosEtiquetas = self::CRITERIOS[$calificacion->tipo_evaluado] ?? [];

        return view('admin.calificaciones.mostrar', compact(
            'calificacion',
            'criteriosEtiquetas'
        ));
    }

    private function aplicarBusqueda(Builder $query, string $buscar): void
    {
        $query->where(function (Builder $query) use ($buscar) {
            $query->where('comentario', 'like', "%{$buscar}%")
                ->orWhereHas('evaluador', fn (Builder $usuario) =>
                    $this->aplicarBusquedaUsuario($usuario, $buscar)
                )
                ->orWhereHas('evaluado', fn (Builder $usuario) =>
                    $this->aplicarBusquedaUsuario($usuario, $buscar)
                );
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

    private function obtenerEstadisticas(): Calificacion
    {
        return Calificacion::query()
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw('COALESCE(ROUND(AVG(promedio), 1), 0) AS promedio_general')
            ->selectRaw("
                SUM(CASE WHEN tipo_evaluado = 'profesional' THEN 1 ELSE 0 END) AS profesionales
            ")
            ->selectRaw("
                SUM(CASE WHEN tipo_evaluado = 'proveedor' THEN 1 ELSE 0 END) AS proveedores
            ")
            ->firstOrFail();
    }
}