<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class AdminUsuarioController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim(
            (string) $request->query(
                'buscar',
                ''
            )
        );

        $rol = $request->query(
            'rol'
        );

        $estado = $request->query(
            'estado'
        );

        $rolesPermitidos = [
            'usuario',
            'profesional',
            'proveedor'
        ];

        $estadosPermitidos = [
            'activo',
            'inactivo',
            'suspendido'
        ];

        $roles = [
            'usuario' => 'Usuario',
            'profesional' => 'Profesional',
            'proveedor' => 'Proveedor'
        ];

        $estados = [
            'activo' => 'Activo',
            'inactivo' => 'Inactivo',
            'suspendido' => 'Suspendido'
        ];

        $query = Usuario::query()
            ->with('rol')
            ->whereHas(
                'rol',
                function ($query) use ($rolesPermitidos) {
                    $query->whereIn(
                        'nombre',
                        $rolesPermitidos
                    );
                }
            );

        if ($buscar !== '') {
            $query->where(
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

        if (
            $rol &&
            in_array(
                $rol,
                $rolesPermitidos,
                true
            )
        ) {
            $query->whereHas(
                'rol',
                function ($query) use ($rol) {
                    $query->where(
                        'nombre',
                        $rol
                    );
                }
            );
        }

        if (
            $estado &&
            in_array(
                $estado,
                $estadosPermitidos,
                true
            )
        ) {
            $query->where(
                'estado',
                $estado
            );
        }

        $usuarios = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $baseConteo = Usuario::query()
            ->whereHas(
                'rol',
                function ($query) use ($rolesPermitidos) {
                    $query->whereIn(
                        'nombre',
                        $rolesPermitidos
                    );
                }
            );

        $totalUsuarios =
            (clone $baseConteo)->count();

        $totalActivos =
            (clone $baseConteo)
                ->where(
                    'estado',
                    'activo'
                )
                ->count();

        $totalInactivos =
            (clone $baseConteo)
                ->where(
                    'estado',
                    'inactivo'
                )
                ->count();

        $totalSuspendidos =
            (clone $baseConteo)
                ->where(
                    'estado',
                    'suspendido'
                )
                ->count();

        return view(
            'admin.usuarios.index',
            compact(
                'usuarios',
                'buscar',
                'rol',
                'estado',
                'roles',
                'estados',
                'totalUsuarios',
                'totalActivos',
                'totalInactivos',
                'totalSuspendidos'
            )
        );
    }

    public function mostrar(
        Usuario $usuario
    ) {
        $this->validarUsuarioAdministrable(
            $usuario
        );

        $usuario->load([
            'rol',
            'profesional.profesiones',
            'profesional.especialidades',
            'profesional.servicios',
            'proveedor.materiales'
        ]);

        $estadosCuenta = [
            'activo' => 'Activo',
            'inactivo' => 'Inactivo',
            'suspendido' => 'Suspendido'
        ];

        return view(
            'admin.usuarios.mostrar',
            compact(
                'usuario',
                'estadosCuenta'
            )
        );
    }

    public function actualizarEstado(
        Request $request,
        Usuario $usuario
    ) {
        $this->validarUsuarioAdministrable(
            $usuario
        );

        $datos = $request->validate(
            [
                'estado' => [
                    'required',
                    Rule::in([
                        'activo',
                        'inactivo',
                        'suspendido'
                    ])
                ]
            ],
            [
                'estado.required' =>
                    'Debes seleccionar un estado válido.',

                'estado.in' =>
                    'El estado seleccionado no es válido.'
            ]
        );

        if (
            $usuario->estado ===
            $datos['estado']
        ) {
            return back()->with(
                'success',
                'La cuenta ya se encuentra en ese estado.'
            );
        }

        $usuario->update([
            'estado' => $datos['estado']
        ]);

        return back()->with(
            'success',
            'El estado de la cuenta se actualizó correctamente.'
        );
    }

    public function eliminar(
        Usuario $usuario
    ) {
        $this->validarUsuarioAdministrable(
            $usuario
        );

        if (
            auth()->id() ===
            $usuario->id
        ) {
            return back()->withErrors([
                'eliminar' =>
                    'No puedes eliminar tu propia cuenta desde el panel administrativo.'
            ]);
        }

        $fotoPerfil =
            $usuario->foto_perfil;

        $portafolioFotos =
            $usuario->portafolio_fotos ?? [];

        if (
            is_string(
                $portafolioFotos
            )
        ) {
            $portafolioFotos =
                json_decode(
                    $portafolioFotos,
                    true
                ) ?? [];
        }

        if (
            !is_array(
                $portafolioFotos
            )
        ) {
            $portafolioFotos = [];
        }

        try {
            DB::transaction(
                function () use ($usuario) {
                    $usuario->delete();
                }
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'eliminar' =>
                    'No fue posible eliminar la cuenta. Verifica que no existan relaciones que impidan su eliminación.'
            ]);
        }

        if ($fotoPerfil) {
            Storage::disk('public')->delete(
                $fotoPerfil
            );
        }

        foreach (
            $portafolioFotos as $foto
        ) {
            if (
                is_string($foto) &&
                $foto !== ''
            ) {
                Storage::disk('public')->delete(
                    $foto
                );
            }
        }

        return redirect()
            ->route(
                'admin.usuarios.index'
            )
            ->with(
                'success',
                'La cuenta y su información asociada fueron eliminadas correctamente.'
            );
    }

    private function validarUsuarioAdministrable(
        Usuario $usuario
    ): void {
        $usuario->loadMissing(
            'rol'
        );

        abort_if(
            !$usuario->rol,
            404
        );

        abort_if(
            !in_array(
                $usuario->rol->nombre,
                [
                    'usuario',
                    'profesional',
                    'proveedor'
                ],
                true
            ),
            403
        );
    }
}