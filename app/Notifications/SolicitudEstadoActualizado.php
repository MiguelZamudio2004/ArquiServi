<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SolicitudEstadoActualizado extends Notification
{
    use Queueable;

    public function __construct(public $solicitud)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $accion = match ($this->solicitud->estado) {
            'aceptada' => 'aceptó',
            'rechazada' => 'rechazó',
            default => 'actualizó'
        };

        return [
            'titulo' => 'Solicitud actualizada',
            'mensaje' => $this->solicitud->profesional->usuario->nombre .
                ' ' . $accion .
                ' tu solicitud de "' .
                $this->solicitud->servicio->nombre . '".'
        ];
    }
}