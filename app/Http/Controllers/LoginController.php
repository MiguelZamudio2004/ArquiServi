<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $datos = $request->validate([
            'correo' => 'required|email',
            'password' => 'required|string',
        ],
        [
            'correo.email' => 'El correo electrónico no es válido.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]
        );

        if (Auth::attempt(['correo' => $datos['correo'], 'password' => $datos['password'], 'estado' => 'activo'])) {
            $request->session()->regenerate();

            return redirect()->intended('/menu');
        }

        return back()->withErrors([
            'correo' => 'Las credenciales proporcionadas no son correctas o el usuario no está activo.',
        ])->onlyInput('correo');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
