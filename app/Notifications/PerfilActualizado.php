<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PerfilActualizado extends Notification {
    use Queueable;
    public function __contruct()
    {}
    public function via (object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable):
        array {
            return ['titulo' => 'Perfil actualizado', 'mensaje' => 'Los datos de tu perfil se actualizaron correctamente'];
        }
}