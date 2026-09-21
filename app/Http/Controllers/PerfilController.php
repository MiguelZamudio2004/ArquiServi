<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Notifications\PerfilActualizado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    public function mostrar(Request $request)
    {
        $usuario = $request->user()->load(
            'rol',
            'proveedor.materiales',
            'profesional.profesiones',
            'profesional.especialidades'
        );

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
            if ($usuario->foto_perfil) {
                Storage::disk('public')->delete($usuario->foto_perfil);
            }

            $datos['foto_perfil'] = $request->file('foto_perfil')->store('perfiles', 'public');
        }

        $usuario->update($datos);
        $usuario->notify(new PerfilActualizado());

        return redirect()->route('perfil')->with('success', 'Perfil actualizado correctamente.');
    }

    public function publico(Usuario $usuario)
    {
        if ($usuario->estado !== 'activo') abort(404);

        $usuario->load(
            'rol',
            'proveedor.materiales',
            'profesional.profesiones',
            'profesional.especialidades'
        );

        return view('perfil-publico', compact('usuario'));
    }

    public function buscar(Request $request)
    {
        $busqueda = trim($request->input('buscar', ''));

        $usuarios = Usuario::with('rol')
            ->where('estado', 'activo')
            ->whereHas('rol', function ($query) {
                $query->where('nombre', '!=', 'administrador');
            })
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('apellido_paterno', 'like', "%{$busqueda}%")
                        ->orWhere('apellido_materno', 'like', "%{$busqueda}%")
                        ->orWhere('ubicacion', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion', 'like', "%{$busqueda}%")
                        ->orWhereHas('rol', function ($rol) use ($busqueda) {
                            $rol->where('nombre', 'like', "%{$busqueda}%");
                        });
                });
            })
            ->orderBy('nombre')
            ->paginate(12);

        return view('usuarios', compact('usuarios', 'busqueda'));
    }
}