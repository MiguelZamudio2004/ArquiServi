<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CompletarPerfil extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $notifiable->loadMissing('rol');

        if ($notifiable->rol->nombre === 'profesional') {
            return [
                'tipo' => 'completar_perfil',
                'titulo' => 'Completa tu perfil profesional',
                'mensaje' => 'Agrega los servicios que ofreces para que los usuarios puedan encontrarte y solicitar tus servicios.',
                'url' => route('perfil.editar'),
            ];
        }

        if ($notifiable->rol->nombre === 'proveedor') {
            return [
                'tipo' => 'completar_perfil',
                'titulo' => 'Completa tu perfil de proveedor',
                'mensaje' => 'Agrega los materiales o productos que ofreces para que los usuarios puedan encontrarte y realizar solicitudes.',
                'url' => route('perfil.editar'),
            ];
        }

        return [
            'tipo' => 'completar_perfil',
            'titulo' => 'Completa tu perfil',
            'mensaje' => 'Completa la información de tu perfil.',
            'url' => route('perfil.editar'),
        ];
    }
}