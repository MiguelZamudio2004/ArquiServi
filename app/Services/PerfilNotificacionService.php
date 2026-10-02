<?php

namespace App\Services;

use App\Models\Usuario;
use App\Notifications\CompletarPerfil;

class PerfilNotificacionService
{
    public function sincronizar(Usuario $usuario): void
    {
        $usuario->loadMissing(
            'rol',
            'profesional.servicios',
            'proveedor.materiales'
        );

        $rol = $usuario->rol?->nombre;

        if (!in_array($rol, ['profesional', 'proveedor'], true)) {
            $this->eliminarNotificacion($usuario);

            return;
        }

        $perfilIncompleto = false;

        if ($rol === 'profesional') {
            $perfilIncompleto =
                !$usuario->profesional ||
                $usuario->profesional->servicios->isEmpty();
        }

        if ($rol === 'proveedor') {
            $perfilIncompleto =
                !$usuario->proveedor ||
                $usuario->proveedor->materiales->isEmpty();
        }

        if (!$perfilIncompleto) {
            $this->eliminarNotificacion($usuario);

            return;
        }

        $notificaciones = $usuario
            ->notifications()
            ->where(
                'type',
                CompletarPerfil::class
            )
            ->latest()
            ->get();

        if ($notificaciones->isEmpty()) {
            $usuario->notify(
                new CompletarPerfil()
            );

            return;
        }

        if ($notificaciones->count() > 1) {
            $notificaciones
                ->skip(1)
                ->each(
                    function ($notificacion) {
                        $notificacion->delete();
                    }
                );
        }
    }

    private function eliminarNotificacion(
        Usuario $usuario
    ): void {
        $usuario
            ->notifications()
            ->where(
                'type',
                CompletarPerfil::class
            )
            ->delete();
    }
}