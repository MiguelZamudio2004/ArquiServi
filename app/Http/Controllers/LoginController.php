<?php

namespace App\Http\Controllers;

use App\Services\PerfilNotificacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    private const ESTADO_ACTIVO = 'activo';
    private const ROL_ADMINISTRADOR = 'administrador';

    public function create(): View
    {
        return view('login');
    }

    public function login(
        Request $request,
        PerfilNotificacionService $perfilNotificacionService
    ): RedirectResponse {
        $datos = $request->validate(
            [
                'correo' => ['required', 'email'],
                'password' => ['required', 'string'],
            ],
            [
                'correo.required' => 'El correo electrónico es obligatorio.',
                'correo.email' => 'El correo electrónico no es válido.',
                'password.required' => 'La contraseña es obligatoria.',
            ]
        );

        $credenciales = [
            'correo' => $datos['correo'],
            'password' => $datos['password'],
            'estado' => self::ESTADO_ACTIVO,
        ];

        if (!Auth::attempt($credenciales)) {
            return back()
                ->withErrors([
                    'correo' => '⚠ Las credenciales proporcionadas no son correctas o el usuario no está activo. ⚠',
                ])
                ->onlyInput('correo');
        }

        $request->session()->regenerate();

        $usuario = $request->user();
        $usuario->loadMissing('rol');

        if ($usuario->rol?->nombre === self::ROL_ADMINISTRADOR) {
            return redirect()->route('admin.dashboard');
        }

        $perfilNotificacionService->sincronizar($usuario);

        return redirect()->route('menu');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}