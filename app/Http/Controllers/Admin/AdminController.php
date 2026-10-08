<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\View\View;

class AdminController extends Controller
{
    private const ROL_USUARIO = 'usuario';
    private const ROL_PROFESIONAL = 'profesional';
    private const ROL_PROVEEDOR = 'proveedor';
    private const ROL_ADMINISTRADOR = 'administrador';

    public function index(): View
    {
        $estadisticas = Usuario::query()
            ->join('roles', 'roles.id', '=', 'usuarios.rol_id')
            ->selectRaw("
                COUNT(CASE WHEN roles.nombre = ? THEN 1 END) AS usuarios,
                COUNT(CASE WHEN roles.nombre = ? THEN 1 END) AS profesionales,
                COUNT(CASE WHEN roles.nombre = ? THEN 1 END) AS proveedores,
                COUNT(
                    CASE
                        WHEN usuarios.estado IN ('inactivo', 'suspendido')
                        AND roles.nombre != ?
                        THEN 1
                    END
                ) AS cuentas_no_activas
            ", [
                self::ROL_USUARIO,
                self::ROL_PROFESIONAL,
                self::ROL_PROVEEDOR,
                self::ROL_ADMINISTRADOR,
            ])
            ->first();

        $usuarios = (int) $estadisticas->usuarios;
        $profesionales = (int) $estadisticas->profesionales;
        $proveedores = (int) $estadisticas->proveedores;
        $cuentasNoActivas = (int) $estadisticas->cuentas_no_activas;
        $totalUsuarios = $usuarios + $profesionales + $proveedores;

        $usuariosRecientes = Usuario::with('rol')
            ->whereRelation('rol', 'nombre', '!=', self::ROL_ADMINISTRADOR)
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact(
            'usuarios',
            'profesionales',
            'proveedores',
            'cuentasNoActivas',
            'totalUsuarios',
            'usuariosRecientes'
        ));
    }
}