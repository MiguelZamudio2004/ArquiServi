<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProveedorController extends Controller
{
    private const ESTADOS_CUENTA = [
        'activo' => 'Activo',
        'inactivo' => 'Inactivo',
        'suspendido' => 'Suspendido',
    ];

    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $materialId = $request->query('material');
        $estadoCuenta = $request->query('estado_cuenta');

        $query = Proveedor::query()->with([
            'usuario.rol',
            'materiales',
        ]);

        if ($buscar !== '') {
            $this->aplicarBusqueda($query, $buscar);
        }

        if ($materialId !== null && $materialId !== '') {
            $query->whereHas('materiales', fn (Builder $query) =>
                $query->where('materiales.id', $materialId)
            );
        }

        if (is_string($estadoCuenta) && isset(self::ESTADOS_CUENTA[$estadoCuenta])) {
            $query->whereRelation('usuario', 'estado', $estadoCuenta);
        }

        $proveedores = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $materiales = Material::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $estadisticas = $this->obtenerEstadisticas();

        return view('admin.proveedores.index', [
            'proveedores' => $proveedores,
            'materiales' => $materiales,
            'buscar' => $buscar,
            'materialId' => $materialId,
            'estadoCuenta' => $estadoCuenta,
            'estadosCuenta' => self::ESTADOS_CUENTA,
            'totalProveedores' => (int) $estadisticas->total,
            'totalActivos' => (int) $estadisticas->activos,
            'totalInactivos' => (int) $estadisticas->inactivos,
            'totalSuspendidos' => (int) $estadisticas->suspendidos,
        ]);
    }

    public function mostrar(Proveedor $proveedor): View
    {
        $proveedor->load([
            'usuario.rol',
            'materiales',
        ]);

        return view('admin.proveedores.mostrar', compact('proveedor'));
    }

    private function aplicarBusqueda(Builder $query, string $buscar): void
    {
        $query->where(function (Builder $query) use ($buscar) {
            $query->where('zona_trabajo', 'like', "%{$buscar}%")
                ->orWhere('descripcion', 'like', "%{$buscar}%")
                ->orWhereHas('usuario', function (Builder $usuario) use ($buscar) {
                    $usuario->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                        ->orWhere('apellido_materno', 'like', "%{$buscar}%")
                        ->orWhere('correo', 'like', "%{$buscar}%")
                        ->orWhere('telefono', 'like', "%{$buscar}%");
                })
                ->orWhereHas('materiales', fn (Builder $materiales) =>
                    $materiales->where('materiales.nombre', 'like', "%{$buscar}%")
                );
        });
    }

    private function obtenerEstadisticas(): Proveedor
    {
        return Proveedor::query()
            ->join('usuarios', 'usuarios.id', '=', 'proveedores.usuario_id')
            ->selectRaw('COUNT(proveedores.id) AS total')
            ->selectRaw("
                SUM(CASE WHEN usuarios.estado = 'activo' THEN 1 ELSE 0 END) AS activos
            ")
            ->selectRaw("
                SUM(CASE WHEN usuarios.estado = 'inactivo' THEN 1 ELSE 0 END) AS inactivos
            ")
            ->selectRaw("
                SUM(CASE WHEN usuarios.estado = 'suspendido' THEN 1 ELSE 0 END) AS suspendidos
            ")
            ->firstOrFail();
    }
}