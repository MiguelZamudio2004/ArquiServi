<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Proveedor;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistroProveedorController extends Controller
{
    private const ROL_PROVEEDOR = 'proveedor';
    private const SESION_USUARIO = 'registro_usuario_id';

    public function mostrar(): View|RedirectResponse
    {
        $usuario = $this->obtenerUsuarioRegistro();

        if (!$usuario) {
            return $this->redireccionRegistroExpirado();
        }

        $materiales = Material::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('registerprov', compact(
            'usuario',
            'materiales'
        ));
    }

    public function guardar(Request $request): RedirectResponse
    {
        $usuario = $this->obtenerUsuarioRegistro();

        if (!$usuario) {
            return $this->redireccionRegistroExpirado();
        }

        $datos = $request->validate(
            [
                'descripcion' => ['nullable', 'string', 'max:500'],
                'zona_trabajo' => ['required', 'string', 'max:200'],
                'materiales' => ['required', 'array', 'min:1'],
                'materiales.*' => ['required', 'exists:materiales,id'],
            ],
            [
                'zona_trabajo.required' => 'Debes indicar tu zona de trabajo.',
                'materiales.required' => 'Debes seleccionar al menos un material.',
                'materiales.min' => 'Debes seleccionar al menos un material.',
                'materiales.*.exists' => 'Uno de los materiales seleccionados no es válido.',
                'descripcion.max' => 'La descripción no puede superar los 500 caracteres.',
            ]
        );

        DB::transaction(function () use ($usuario, $datos) {
            $proveedor = Proveedor::create([
                'usuario_id' => $usuario->id,
                'descripcion' => $datos['descripcion'] ?? null,
                'zona_trabajo' => $datos['zona_trabajo'],
            ]);

            $proveedor->materiales()->sync(
                $this->prepararMateriales($datos['materiales'])
            );
        });

        session()->forget(self::SESION_USUARIO);

        return redirect()
            ->route('login')
            ->with('success', 'Tu registro como proveedor se completó correctamente.');
    }

    private function obtenerUsuarioRegistro(): ?Usuario
    {
        $usuarioId = session(self::SESION_USUARIO);

        if (!$usuarioId) {
            return null;
        }

        $usuario = Usuario::with('rol')->findOrFail($usuarioId);

        abort_unless(
            $usuario->rol?->nombre === self::ROL_PROVEEDOR,
            403
        );

        return $usuario;
    }

    private function prepararMateriales(array $materialesIds): array
    {
        return collect($materialesIds)
            ->mapWithKeys(fn ($materialId) => [
                $materialId => ['disponible' => true],
            ])
            ->all();
    }

    private function redireccionRegistroExpirado(): RedirectResponse
    {
        return redirect()
            ->route('register')
            ->withErrors([
                'registro' => 'Debes iniciar el registro nuevamente.',
            ]);
    }
}