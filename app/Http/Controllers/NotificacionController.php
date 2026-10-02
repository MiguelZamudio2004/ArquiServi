<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function marcarLeida(
        Request $request,
        string $id
    ) {
        $usuario = $request->user();

        $notificacion = $usuario
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        if (!$notificacion->read_at) {
            $notificacion->markAsRead();
        }

        return response()->json([
            'success' => true,
            'redirect_url' =>
                $notificacion->data['url']
                ?? null,
            'unread_count' =>
                $usuario
                    ->unreadNotifications()
                    ->count(),
        ]);
    }
}