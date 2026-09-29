<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function marcarLeida(Request $request, string $id)
    {
        $notificacion = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        if (!$notificacion->read_at) {
            $notificacion->markAsRead();
        }

        $redirectUrl = null;

        if (!empty($notificacion->data['solicitud_id'])) {
            $redirectUrl = route(
                'solicitudes.mostrar',
                $notificacion->data['solicitud_id']
            );
        }

        return response()->json([
            'success' => true,
            'no_leidas' => $request->user()->unreadNotifications()->count(),
            'redirect_url' => $redirectUrl,
        ]);
    }
}