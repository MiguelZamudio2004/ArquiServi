<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Notifications\PerfilActualizado;
use App\Models\Usuario;

class PerfilController extends Controller
{
    public function mostrar(Request $request)
    {
        $usuario = $request->user();
        return view('perfil', compact('usuario'));
    }

    public function editar(Request $request)
    {
        $usuario = $request->user();
        return view('perfil-editar', compact('usuario'));
    }

    public function actualizar(Request $request)
    {
        $usuario = $request->user();

        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'ubicacion' => 'nullable|string|max:200',
            'descripcion' => 'nullable|string|max:500',
            'foto_perfil' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('foto_perfil')) {
            if ($usuario->foto_perfil) Storage::disk('public')->delete($usuario->foto_perfil);
            $datos['foto_perfil'] = $request->file('foto_perfil')->store('perfiles', 'public');
        }

        $usuario->update($datos);
        $usuario->notify(new PerfilActualizado());

        return redirect()->route('perfil')->with('success', 'Perfil actualizado correctamente.');
    }
    public function publico(Usuario $usuario) {
        if ($usuario-> estado !== 'activo') abort(404);

        $usuario->load('rol');

        return view('perfil-publico',compact('usuario'));
    }

    }
