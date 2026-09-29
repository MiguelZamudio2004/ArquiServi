<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use App\Notifications\BienvenidoArquiservi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistroController extends Controller
{
    public function create()
    {
        return view('register');
    }

    public function store(Request $request)
    {
        $datos = $request->validate(
            [
                'nombre' => ['required', 'string', 'max:100'],
                'apellido_paterno' => ['required', 'string', 'max:100'],
                'apellido_materno' => ['required', 'string', 'max:100'],
                'correo' => ['required', 'email', 'max:150', 'unique:usuarios,correo'],
                'telefono' => ['required', 'string', 'max:20'],
                'rol' => ['required', 'in:usuario,profesional,proveedor'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ],
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
                'apellido_materno.required' => 'El apellido materno es obligatorio.',
                'correo.required' => 'El correo electrónico es obligatorio.',
                'correo.email' => 'El correo electrónico no es válido.',
                'correo.unique' => 'Este correo electrónico ya está registrado.',
                'telefono.required' => 'El número de teléfono es obligatorio.',
                'rol.required' => 'Debes seleccionar un rol.',
                'rol.in' => 'El rol seleccionado no es válido.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
            ]
        );

        $rol = Rol::where('nombre', $datos['rol'])->first();

        if (!$rol) {
            return back()
                ->withErrors([
                    'rol' => 'El rol seleccionado no existe.'
                ])
                ->withInput();
        }

        $usuario = Usuario::create([
            'rol_id' => $rol->id,
            'nombre' => $datos['nombre'],
            'apellido_paterno' => $datos['apellido_paterno'],
            'apellido_materno' => $datos['apellido_materno'],
            'correo' => $datos['correo'],
            'telefono' => $datos['telefono'],
            'password' => Hash::make($datos['password']),
            'estado' => 'activo',
        ]);

        $usuario->notify(new BienvenidoArquiservi());

        if ($datos['rol'] === 'profesional') {
            session([
                'registro_usuario_id' => $usuario->id
            ]);

            return redirect()->route('registro.profesional');
        }

        if ($datos['rol'] === 'proveedor') {
            session([
                'registro_usuario_id' => $usuario->id
            ]);

            return redirect()->route('registro.proveedor');
        }

        return redirect()
            ->route('login')
            ->with('success', 'Tu cuenta se creó correctamente.');
    }
}