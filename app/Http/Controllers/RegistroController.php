<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Notifications\BienvenidoArquiServi;

class RegistroController extends Controller
{
    public function create()
    {
        return view('register');
    }

    public function store(Request $request)
    {

        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'correo' => 'required|email|max:150|unique:usuarios,correo',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'rol' => 'required|in:usuario,profesional,proveedor',
        ],
        [
            'correo.unique' => ' ⚠ El correo electrónico ya está registrado. ⚠ ',
            'password.confirmed' => ' ⚠ La confirmación de la contraseña no coincide. ⚠ ',
            'correo.email' => ' ⚠ El correo electrónico no es válido. ⚠ ',
            'password.min' => ' ⚠ La contraseña debe tener al menos 8 caracteres ⚠ '
        ]
        );

        $rol = Rol::where('nombre', $datos['rol'])->firstOrFail();

        $usuario=Usuario::create([
            'rol_id' => $rol->id,
            'nombre' => $datos['nombre'],
            'apellido_paterno' => $datos['apellido_paterno'],
            'apellido_materno' => $datos['apellido_materno'] ?? null,
            'correo' => $datos['correo'],
            'telefono' => $datos['telefono'] ?? null,
            'password' => $datos['password'],
            'estado' => 'activo',
        ]);

        $usuario->notify(new BienvenidoArquiServi());

    

        return redirect()
            ->route('login')
            ->with('success', 'Usuario registrado exitosamente.');
    }
}