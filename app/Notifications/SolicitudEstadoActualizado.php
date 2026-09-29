<?php

namespace App\Notifications;

use App\Models\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SolicitudEstadoActualizado extends Notification
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
        $accion = match (
            $this->solicitud->estado
        ) {
            'aceptada' =>
                'aceptó',

            'rechazada' =>
                'rechazó',

            default =>
                'actualizó'
        };

        $concepto =
            $this->solicitud->servicio?->nombre
            ?? $this->solicitud->material?->nombre
            ?? 'tu solicitud';

        return [
            'titulo' =>
                'Solicitud actualizada',

            'mensaje' =>
                $this->solicitud->destinatario->nombre
                . ' '
                . $accion
                . ' tu solicitud de '
                . $concepto
                . '.',

            'solicitud_id' =>
                $this->solicitud->id
        ];
    }
}