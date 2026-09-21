<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Proveedor;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistroProveedorController extends Controller
{
    public function mostrar()
    {
        $usuarioId = session('registro_usuario_id');

        if (!$usuarioId) return redirect()->route('registro')->withErrors(['registro' => 'Debes iniciar el registro nuevamente.']);

        $usuario = Usuario::with('rol')->findOrFail($usuarioId);

        if ($usuario->rol->nombre !== 'proveedor') abort(403);

        $materiales = Material::orderBy('nombre')->get();

        return view('registerprov', compact('usuario', 'materiales'));
    }

    public function guardar(Request $request)
    {
        $usuarioId = session('registro_usuario_id');

        if (!$usuarioId) return redirect()->route('registro')->withErrors(['registro' => 'Debes iniciar el registro nuevamente.']);

        $usuario = Usuario::with('rol')->findOrFail($usuarioId);

        if ($usuario->rol->nombre !== 'proveedor') abort(403);

        $datos = $request->validate([
            'descripcion' => 'nullable|string|max:500',
            'zona_trabajo' => 'required|string|max:200',
            'materiales' => 'required|array|min:1',
            'materiales.*' => 'required|exists:materiales,id',
        ], [
            'zona_trabajo.required' => 'Debes indicar tu zona de trabajo.',
            'materiales.required' => 'Debes seleccionar al menos un material.',
            'materiales.min' => 'Debes seleccionar al menos un material.',
            'materiales.*.exists' => 'Uno de los materiales seleccionados no es válido.',
            'descripcion.max' => 'La descripción no puede superar los 500 caracteres.',
        ]);

        DB::transaction(function () use ($usuario, $datos) {
            $proveedor = Proveedor::create([
                'usuario_id' => $usuario->id,
                'descripcion' => $datos['descripcion'] ?? null,
                'zona_trabajo' => $datos['zona_trabajo'],
            ]);

            $proveedor->materiales()->sync($datos['materiales']);
        });

        session()->forget('registro_usuario_id');

        return redirect()->route('login')->with('success', 'Tu registro como proveedor se completó correctamente.');
    }
}