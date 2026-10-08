<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Administrador
{
    private const ROL_ADMINISTRADOR = 'administrador';

    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $usuario->loadMissing('rol');

        abort_unless(
            $usuario->rol?->nombre === self::ROL_ADMINISTRADOR,
            403
        );

        return $next($request);
    }
}