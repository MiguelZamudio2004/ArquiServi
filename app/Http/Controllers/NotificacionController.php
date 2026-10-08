<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function marcarLeida(Request $request, string $id): JsonResponse
    {
        $usuario = $request->user();

        $notificacion = $usuario
            ->notifications()
            ->findOrFail($id);

        $notificacion->markAsRead();

        return response()->json([
            'success' => true,
            'redirect_url' => $notificacion->data['url'] ?? null,
            'unread_count' => $usuario->unreadNotifications()->count(),
        ]);
    }
}