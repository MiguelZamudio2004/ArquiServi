<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Profesional;
use App\Models\Profesion;
use App\Models\SolicitudAprobacionProfesional;
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

        $usuario = Usuario::with('rol')
            ->findOrFail($usuarioId);

        if ($usuario->rol->nombre !== 'profesional') {
            abort(403);
        }

        $profesiones = Profesion::where(
            'activo',
            true
        )
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
            compact(
                'usuario',
                'profesiones'
            )
        );
    }

    public function guardar(Request $request)
    {
        $usuarioId = session(
            'registro_usuario_id'
        );

        if (!$usuarioId) {
            return redirect()
                ->route('register')
                ->withErrors([
                    'registro' =>
                        'Debes iniciar el registro nuevamente.'
                ]);
        }

        $usuario = Usuario::with('rol')
            ->findOrFail($usuarioId);

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

                'especialidades' => [
                    'required',
                    'array',
                    'min:1'
                ],

                'especialidades.*' => [
                    'required',
                    'integer',
                    'distinct',
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
                'profesion_id.required' =>
                    'Debes seleccionar una profesión.',

                'profesion_id.exists' =>
                    'La profesión seleccionada no es válida.',

                'especialidades.required' =>
                    'Debes seleccionar al menos una especialidad.',

                'especialidades.array' =>
                    'Las especialidades seleccionadas no son válidas.',

                'especialidades.min' =>
                    'Debes seleccionar al menos una especialidad.',

                'especialidades.*.exists' =>
                    'Una de las especialidades seleccionadas no es válida.',

                'especialidades.*.distinct' =>
                    'No puedes seleccionar la misma especialidad más de una vez.',

                'anios_experiencia.required' =>
                    'Debes indicar tus años de experiencia.',

                'anios_experiencia.integer' =>
                    'Los años de experiencia deben ser un número entero.',

                'anios_experiencia.min' =>
                    'Los años de experiencia no pueden ser negativos.',

                'anios_experiencia.max' =>
                    'Los años de experiencia no pueden superar 80.',

                'descripcion.required' =>
                    'Debes agregar una descripción profesional.',

                'descripcion.max' =>
                    'La descripción no puede superar los 500 caracteres.',

                'portafolio_url.url' =>
                    'El enlace del portafolio no es válido.',

                'portafolio_url.max' =>
                    'El enlace del portafolio es demasiado largo.',

                'zona_trabajo.required' =>
                    'Debes indicar tu zona de trabajo.',

                'zona_trabajo.max' =>
                    'La zona de trabajo no puede superar los 200 caracteres.',
            ]
        );

        $profesionValida = Profesion::where(
            'id',
            $datos['profesion_id']
        )
            ->where(
                'activo',
                true
            )
            ->exists();

        if (!$profesionValida) {
            return back()
                ->withErrors([
                    'profesion_id' =>
                        'La profesión seleccionada no está disponible.'
                ])
                ->withInput();
        }

        $especialidadesIds = collect(
            $datos['especialidades']
        )
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $especialidades = Especialidad::whereIn(
            'id',
            $especialidadesIds
        )
            ->where(
                'profesion_id',
                $datos['profesion_id']
            )
            ->where(
                'activo',
                true
            )
            ->get();

        if (
            $especialidades->count() !==
            $especialidadesIds->count()
        ) {
            return back()
                ->withErrors([
                    'especialidades' =>
                        'Una o más especialidades no corresponden a la profesión seleccionada.'
                ])
                ->withInput();
        }

        $especialidadesRequierenAprobacion =
            $especialidades
                ->where(
                    'requiere_aprobacion',
                    true
                )
                ->values();

        $requiereAprobacion =
            $especialidadesRequierenAprobacion
                ->isNotEmpty();

        DB::transaction(
            function () use (
                $usuario,
                $datos,
                $especialidadesIds,
                $especialidadesRequierenAprobacion,
                $requiereAprobacion
            ) {
                $profesional = Profesional::updateOrCreate(
                    [
                        'usuario_id' =>
                            $usuario->id
                    ],
                    [
                        'anios_experiencia' =>
                            $datos['anios_experiencia'],

                        'descripcion' =>
                            $datos['descripcion'],

                        'portafolio_url' =>
                            $datos['portafolio_url']
                            ?? null,

                        'zona_trabajo' =>
                            $datos['zona_trabajo'],

                        'estado_aprobacion' =>
                            $requiereAprobacion
                                ? 'pendiente'
                                : 'no_requerida'
                    ]
                );

                $profesional
                    ->profesiones()
                    ->sync([
                        $datos['profesion_id']
                    ]);

                $profesional
                    ->especialidades()
                    ->sync(
                        $especialidadesIds->all()
                    );

                SolicitudAprobacionProfesional::where(
                    'profesional_id',
                    $profesional->id
                )
                    ->where(
                        'estado',
                        'pendiente'
                    )
                    ->delete();

                if ($requiereAprobacion) {
                    SolicitudAprobacionProfesional::create([
                        'profesional_id' =>
                            $profesional->id,

                        'especialidades_requieren_aprobacion' =>
                            $especialidadesRequierenAprobacion
                                ->pluck('id')
                                ->values()
                                ->all(),

                        'estado' =>
                            'pendiente'
                    ]);
                }
            }
        );

        session()->forget(
            'registro_usuario_id'
        );

        if ($requiereAprobacion) {
            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Tu registro fue recibido. Tu perfil profesional permanecerá pendiente hasta que un administrador revise y apruebe tu solicitud.'
                );
        }

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Tu registro como profesional se completó correctamente.'
            );
    }
}