<?php

namespace App\Notifications;

use App\Models\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SolicitudRecibida extends Notification
{
    use Queueable;

    public function __construct(
        public Solicitud $solicitud
    ) {
    }

    public function via($notifiable): array
    {
        return [
            'database'
        ];
    }

    public function toArray($notifiable): array
    {
        $concepto =
            $this->solicitud->servicio?->nombre
            ?? $this->solicitud->material?->nombre
            ?? 'una solicitud';

        return [
            'titulo' =>
                'Nueva solicitud',

            'mensaje' =>
                $this->solicitud->solicitante->nombre
                . ' te envió una solicitud por '
                . $concepto
                . '.',

            'solicitud_id' =>
                $this->solicitud->id
        ];
    }
}