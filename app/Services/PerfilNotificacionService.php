<?php

namespace App\Services;

use App\Models\Usuario;
use App\Notifications\CompletarPerfil;

class PerfilNotificacionService
{
    private const ROL_PROFESIONAL = 'profesional';
    private const ROL_PROVEEDOR = 'proveedor';

    public function sincronizar(Usuario $usuario): void
    {
        $usuario->loadMissing('rol');

        $rol = $usuario->rol?->nombre;

        if (!in_array($rol, [self::ROL_PROFESIONAL, self::ROL_PROVEEDOR], true)) {
            $this->eliminarNotificacion($usuario);
            return;
        }

        $usuario->loadMissing(
            $rol === self::ROL_PROFESIONAL
                ? 'profesional.servicios'
                : 'proveedor.materiales'
        );

        if (!$this->perfilIncompleto($usuario, $rol)) {
            $this->eliminarNotificacion($usuario);
            return;
        }

        $this->sincronizarNotificacion($usuario);
    }

    private function perfilIncompleto(Usuario $usuario, string $rol): bool
    {
        return match ($rol) {
            self::ROL_PROFESIONAL =>
                !$usuario->profesional ||
                $usuario->profesional->servicios->isEmpty(),

            self::ROL_PROVEEDOR =>
                !$usuario->proveedor ||
                $usuario->proveedor->materiales->isEmpty(),

            default => false,
        };
    }

    private function sincronizarNotificacion(Usuario $usuario): void
    {
        $notificacion = $usuario
            ->notifications()
            ->where('type', CompletarPerfil::class)
            ->latest()
            ->first();

        if (!$notificacion) {
            $usuario->notify(new CompletarPerfil());
            return;
        }

        $usuario
            ->notifications()
            ->where('type', CompletarPerfil::class)
            ->where('id', '!=', $notificacion->id)
            ->delete();
    }

    private function eliminarNotificacion(Usuario $usuario): void
    {
        $usuario
            ->notifications()
            ->where('type', CompletarPerfil::class)
            ->delete();
    }
}