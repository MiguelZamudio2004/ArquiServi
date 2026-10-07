<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;

class AdminController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::whereHas(
            'rol',
            function ($query) {
                $query->where(
                    'nombre',
                    'usuario'
                );
            }
        )->count();

        $profesionales = Usuario::whereHas(
            'rol',
            function ($query) {
                $query->where(
                    'nombre',
                    'profesional'
                );
            }
        )->count();

        $proveedores = Usuario::whereHas(
            'rol',
            function ($query) {
                $query->where(
                    'nombre',
                    'proveedor'
                );
            }
        )->count();

        $cuentasNoActivas = Usuario::whereIn(
            'estado',
            [
                'inactivo',
                'suspendido'
            ]
        )
            ->whereHas(
                'rol',
                function ($query) {
                    $query->where(
                        'nombre',
                        '!=',
                        'administrador'
                    );
                }
            )
            ->count();

        $totalUsuarios =
            $usuarios +
            $profesionales +
            $proveedores;

        $usuariosRecientes = Usuario::with('rol')
            ->whereHas(
                'rol',
                function ($query) {
                    $query->where(
                        'nombre',
                        '!=',
                        'administrador'
                    );
                }
            )
            ->latest()
            ->take(6)
            ->get();

        return view(
            'admin.dashboard',
            compact(
                'usuarios',
                'profesionales',
                'proveedores',
                'cuentasNoActivas',
                'totalUsuarios',
                'usuariosRecientes'
            )
        );
    }
}