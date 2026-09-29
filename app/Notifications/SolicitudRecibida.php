<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SolicitudRecibida extends Notification
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
        return [
            'titulo' => 'Nueva solicitud de servicio',
            'mensaje' => $this->solicitud->usuario->nombre .
                ' solicitó el servicio "' .
                $this->solicitud->servicio->nombre . '".'
        ];
    }
}