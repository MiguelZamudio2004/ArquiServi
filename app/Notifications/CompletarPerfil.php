<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CompletarPerfil extends Notification
{
    use Queueable;

    private const TIPO = 'completar_perfil';
    private const ROL_PROFESIONAL = 'profesional';
    private const ROL_PROVEEDOR = 'proveedor';

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $notifiable->loadMissing('rol');

        [$titulo, $mensaje] = match ($notifiable->rol?->nombre) {
            self::ROL_PROFESIONAL => [
                'Completa tu perfil profesional',
                'Agrega los servicios que ofreces para que los usuarios puedan encontrarte y solicitar tus servicios.',
            ],
            self::ROL_PROVEEDOR => [
                'Completa tu perfil de proveedor',
                'Agrega los materiales o productos que ofreces para que los usuarios puedan encontrarte y realizar solicitudes.',
            ],
            default => [
                'Completa tu perfil',
                'Completa la información de tu perfil.',
            ],
        };

        return [
            'tipo' => self::TIPO,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'url' => route('perfil.editar'),
        ];
    }
}