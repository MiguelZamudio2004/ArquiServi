<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Administrador
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $usuario = $request->user();

        if (!$usuario) {
            return redirect()
                ->route('login');
        }

        $usuario->loadMissing('rol');

        if (
            !$usuario->rol ||
            $usuario->rol->nombre !== 'administrador'
        ) {
            abort(403);
        }

        return $next($request);
    }
}