<?php

namespace App\Http\Controllers;

use App\Services\PerfilNotificacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('login');
    }

    public function login(
        Request $request,
        PerfilNotificacionService $perfilNotificacionService
    ) {
        $datos = $request->validate(
            [
                'correo' => [
                    'required',
                    'email'
                ],
                'password' => [
                    'required',
                    'string'
                ],
            ],
            [
                'correo.required' =>
                    'El correo electrónico es obligatorio.',

                'correo.email' =>
                    'El correo electrónico no es válido.',

                'password.required' =>
                    'La contraseña es obligatoria.',
            ]
        );

        $credenciales = [
            'correo' => $datos['correo'],
            'password' => $datos['password'],
            'estado' => 'activo'
        ];

        if (Auth::attempt($credenciales)) {
            $request
                ->session()
                ->regenerate();

            $usuario = $request->user();

            $perfilNotificacionService
                ->sincronizar(
                    $usuario
                );

            return redirect()
                ->route('menu');
        }

        return back()
            ->withErrors([
                'correo' =>
                    '⚠ Las credenciales proporcionadas no son correctas o el usuario no está activo. ⚠'
            ])
            ->onlyInput('correo');
    }

    public function logout(
        Request $request
    ) {
        Auth::logout();

        $request
            ->session()
            ->invalidate();

        $request
            ->session()
            ->regenerateToken();

        return redirect()
            ->route('login');
    }
}