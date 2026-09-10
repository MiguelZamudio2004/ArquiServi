<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BienvenidoArquiServi extends Notification {
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via (object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable):
        array
        {
            return ['titulo' => 'Bienvenido a ArquiServi!', 'mensaje' => 'Tu cuenta ha sido creada correctamente. Ya puedes comenzar a explorar ArquiServi.',];
        }
}