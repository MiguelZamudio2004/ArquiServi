<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function marcarLeida(Request $request, string $id)
    {
        $notificacion = $request->user()->notifications()->where('id', $id)->firstOrFail();

        if (!$notificacion->read_at) {
            $notificacion->markAsRead();
        }

        return response()->json([
            'success' => true,
            'no_leidas' => $request->user()->unreadNotifications()->count(),
        ]);
    }
}