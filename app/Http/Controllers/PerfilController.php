<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerfilController extends Controller {
    public function mostrar (Request $request) {
        $usuario = $request -> user(); 
        return view('perfil', compact('usuario'));
    }

    public function editar(Request $request) {
        $usuario = $request->user();
        return view ('perfil-editar', compact('usuario'));
    }
}
