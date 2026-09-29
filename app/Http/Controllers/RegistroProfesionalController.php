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

        if (!$usuarioId) {
            return redirect()
                ->route('register')
                ->withErrors([
                    'registro' => 'Debes iniciar el registro nuevamente.'
                ]);
        }

        $usuario = Usuario::with('rol')->findOrFail($usuarioId);

        if ($usuario->rol->nombre !== 'profesional') {
            abort(403);
        }

        $profesiones = Profesion::where('activo', true)
            ->with([
                'especialidades' => function ($query) {
                    $query
                        ->where('activo', true)
                        ->orderBy('nombre');
                }
            ])
            ->orderBy('nombre')
            ->get();

        return view(
            'registerprof',
            compact('usuario', 'profesiones')
        );
    }

    public function guardar(Request $request)
    {
        $usuarioId = session('registro_usuario_id');

        if (!$usuarioId) {
            return redirect()
                ->route('register')
                ->withErrors([
                    'registro' => 'Debes iniciar el registro nuevamente.'
                ]);
        }

        $usuario = Usuario::with('rol')->findOrFail($usuarioId);

        if ($usuario->rol->nombre !== 'profesional') {
            abort(403);
        }

        $datos = $request->validate(
            [
                'profesion_id' => [
                    'required',
                    'integer',
                    'exists:profesiones,id'
                ],
                'especialidad_id' => [
                    'required',
                    'integer',
                    'exists:especialidades,id'
                ],
                'anios_experiencia' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:80'
                ],
                'descripcion' => [
                    'required',
                    'string',
                    'max:500'
                ],
                'portafolio_url' => [
                    'nullable',
                    'url',
                    'max:500'
                ],
                'zona_trabajo' => [
                    'required',
                    'string',
                    'max:200'
                ],
            ],
            [
                'profesion_id.required' => 'Debes seleccionar una profesión.',
                'profesion_id.exists' => 'La profesión seleccionada no es válida.',
                'especialidad_id.required' => 'Debes seleccionar una especialidad.',
                'especialidad_id.exists' => 'La especialidad seleccionada no es válida.',
                'anios_experiencia.required' => 'Debes indicar tus años de experiencia.',
                'anios_experiencia.integer' => 'Los años de experiencia deben ser un número entero.',
                'anios_experiencia.min' => 'Los años de experiencia no pueden ser negativos.',
                'anios_experiencia.max' => 'Los años de experiencia no pueden superar 80.',
                'descripcion.required' => 'Debes agregar una descripción profesional.',
                'descripcion.max' => 'La descripción no puede superar los 500 caracteres.',
                'portafolio_url.url' => 'El enlace del portafolio no es válido.',
                'portafolio_url.max' => 'El enlace del portafolio es demasiado largo.',
                'zona_trabajo.required' => 'Debes indicar tu zona de trabajo.',
                'zona_trabajo.max' => 'La zona de trabajo no puede superar los 200 caracteres.',
            ]
        );

        $profesionValida = Profesion::where('id', $datos['profesion_id'])
            ->where('activo', true)
            ->exists();

        if (!$profesionValida) {
            return back()
                ->withErrors([
                    'profesion_id' => 'La profesión seleccionada no está disponible.'
                ])
                ->withInput();
        }

        $especialidadValida = Especialidad::where('id', $datos['especialidad_id'])
            ->where('profesion_id', $datos['profesion_id'])
            ->where('activo', true)
            ->exists();

        if (!$especialidadValida) {
            return back()
                ->withErrors([
                    'especialidad_id' => 'La especialidad seleccionada no corresponde a la profesión.'
                ])
                ->withInput();
        }

        DB::transaction(function () use ($usuario, $datos) {
            $profesional = Profesional::updateOrCreate(
                [
                    'usuario_id' => $usuario->id
                ],
                [
                    'anios_experiencia' => $datos['anios_experiencia'],
                    'descripcion' => $datos['descripcion'],
                    'portafolio_url' => $datos['portafolio_url'] ?? null,
                    'zona_trabajo' => $datos['zona_trabajo'],
                ]
            );

            $profesional->profesiones()->sync([
                $datos['profesion_id']
            ]);

            $profesional->especialidades()->sync([
                $datos['especialidad_id']
            ]);
        });

        session()->forget('registro_usuario_id');

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Tu registro como profesional se completó correctamente.'
            );
    }
}