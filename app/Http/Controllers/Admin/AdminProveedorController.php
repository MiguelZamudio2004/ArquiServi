<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class AdminProveedorController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim(
            (string) $request->query(
                'buscar',
                ''
            )
        );

        $materialId = $request->query(
            'material'
        );

        $estadoCuenta = $request->query(
            'estado_cuenta'
        );

        $estadosCuenta = [
            'activo' => 'Activo',
            'inactivo' => 'Inactivo',
            'suspendido' => 'Suspendido'
        ];

        $query = Proveedor::query()
            ->with([
                'usuario.rol',
                'materiales'
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
                        ->orWhere(
                            'descripcion',
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
                }
            );
        }

        if (
            $materialId !== null &&
            $materialId !== ''
        ) {
            $query->whereHas(
                'materiales',
                function ($query) use ($materialId) {
                    $query->where(
                        'materiales.id',
                        $materialId
                    );
                }
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

        $proveedores = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $materiales = Material::where(
            'activo',
            true
        )
            ->orderBy('nombre')
            ->get();

        $totalProveedores = Proveedor::count();

        $totalActivos = Proveedor::whereHas(
            'usuario',
            function ($query) {
                $query->where(
                    'estado',
                    'activo'
                );
            }
        )->count();

        $totalInactivos = Proveedor::whereHas(
            'usuario',
            function ($query) {
                $query->where(
                    'estado',
                    'inactivo'
                );
            }
        )->count();

        $totalSuspendidos = Proveedor::whereHas(
            'usuario',
            function ($query) {
                $query->where(
                    'estado',
                    'suspendido'
                );
            }
        )->count();

        return view(
            'admin.proveedores.index',
            compact(
                'proveedores',
                'materiales',
                'buscar',
                'materialId',
                'estadoCuenta',
                'estadosCuenta',
                'totalProveedores',
                'totalActivos',
                'totalInactivos',
                'totalSuspendidos'
            )
        );
    }

    public function mostrar(
        Proveedor $proveedor
    ) {
        $proveedor->load([
            'usuario.rol',
            'materiales'
        ]);

        return view(
            'admin.proveedores.mostrar',
            compact(
                'proveedor'
            )
        );
    }
}