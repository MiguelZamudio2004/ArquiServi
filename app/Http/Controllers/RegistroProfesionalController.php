<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Profesional;
use App\Models\Profesion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistroProfesionalController extends Controller
{
    public function mostrar()
    {
        $usuarioId = session('registro_usuario_id');

        if (!$usuarioId) return redirect()->route('registro')->withErrors(['registro' => 'Debes iniciar el registro nuevamente.']);

        $usuario = Usuario::with('rol')->findOrFail($usuarioId);

        if ($usuario->rol->nombre !== 'profesional') abort(403);

        $profesiones = Profesion::where('activo', true)
            ->with(['especialidades' => function ($query) {
                $query->where('activo', true)->orderBy('nombre');
            }])
            ->orderBy('nombre')
            ->get();

        return view('registerprof', compact('usuario', 'profesiones'));
    }

    public function guardar(Request $request)
    {
        $usuarioId = session('registro_usuario_id');

        if (!$usuarioId) return redirect()->route('registro')->withErrors(['registro' => 'Debes iniciar el registro nuevamente.']);

        $usuario = Usuario::with('rol')->findOrFail($usuarioId);

        if ($usuario->rol->nombre !== 'profesional') abort(403);

        $datos = $request->validate([
            'profesion_id' => 'required|exists:profesiones,id',
            'especialidad_id' => 'required|exists:especialidades,id',
            'anios_experiencia' => 'required|integer|min:0|max:80',
            'descripcion' => 'required|string|max:500',
            'portafolio_url' => 'nullable|url|max:500',
            'zona_trabajo' => 'required|string|max:200',
        ]);

        $especialidadValida = Especialidad::where('id', $datos['especialidad_id'])
            ->where('profesion_id', $datos['profesion_id'])
            ->where('activo', true)
            ->exists();

        if (!$especialidadValida) {
            return back()->withErrors([
                'especialidad_id' => 'La especialidad seleccionada no corresponde a la profesión.'
            ])->withInput();
        }

        DB::transaction(function () use ($usuario, $datos) {
            $profesional = Profesional::updateOrCreate(
                ['usuario_id' => $usuario->id],
                [
                    'anios_experiencia' => $datos['anios_experiencia'],
                    'descripcion' => $datos['descripcion'],
                    'portafolio_url' => $datos['portafolio_url'] ?? null,
                    'zona_trabajo' => $datos['zona_trabajo'],
                ]
            );

            $profesional->profesiones()->sync([$datos['profesion_id']]);
            $profesional->especialidades()->sync([$datos['especialidad_id']]);
        });

        session()->forget('registro_usuario_id');

        return redirect()->route('login')->with('success', 'Tu registro como profesional se completó correctamente.');
    }
}