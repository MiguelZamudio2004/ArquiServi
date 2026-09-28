<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Profesional;
use App\Models\Solicitud;
use App\Notifications\SolicitudEstadoActualizado;
use App\Notifications\SolicitudRecibida;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function crear(Profesional $profesional)
    {
        $usuario = auth()->user();

        abort_if(
            !$usuario ||
            $usuario->rol->nombre !== 'usuario',
            403
        );

        $profesional->load(
            'usuario',
            'servicios'
        );

        abort_if(
            $profesional->servicios->isEmpty(),
            404
        );

        return view(
            'solicitudes.crear',
            compact('profesional')
        );
    }

    public function guardar(Request $request)
    {
        $usuario = $request->user();

        abort_if(
            $usuario->rol->nombre !== 'usuario',
            403
        );

        $datos = $request->validate([
            'profesional_id' => 'required|integer|exists:profesionales,id',
            'servicio_id' => 'required|integer|exists:servicios,id',
            'descripcion' => 'required|string|min:10|max:1000'
        ]);

        $profesional = Profesional::with([
            'usuario',
            'servicios'
        ])->findOrFail(
            $datos['profesional_id']
        );

        if (
            !$profesional->servicios->contains(
                'id',
                (int) $datos['servicio_id']
            )
        ) {
            return back()
                ->withErrors([
                    'servicio_id' => 'El profesional no ofrece el servicio seleccionado.'
                ])
                ->withInput();
        }

        $solicitud = Solicitud::create([
            'usuario_id' => $usuario->id,
            'profesional_id' => $profesional->id,
            'servicio_id' => $datos['servicio_id'],
            'descripcion' => $datos['descripcion'],
            'estado' => 'pendiente'
        ]);

        $solicitud->load(
            'usuario',
            'servicio'
        );

        $profesional->usuario->notify(
            new SolicitudRecibida($solicitud)
        );

        return redirect()
            ->route('solicitudes.mias')
            ->with(
                'exito',
                'Solicitud enviada correctamente.'
            );
    }

    public function mias()
    {
        $usuario = auth()->user();

        abort_if(
            $usuario->rol->nombre !== 'usuario',
            403
        );

        $solicitudes = Solicitud::with(
            'usuario',
            'profesional.usuario',
            'servicio',
            'calificaciones'
        )
        ->where(
            'usuario_id',
            $usuario->id
        )
        ->latest()
        ->get();

        return view(
            'solicitudes.mias',
            compact('solicitudes')
        );
    }

    public function recibidas()
    {
        $usuario = auth()->user();

        abort_if(
            $usuario->rol->nombre !== 'profesional' ||
            !$usuario->profesional,
            403
        );

        $solicitudes = Solicitud::with(
            'usuario',
            'profesional.usuario',
            'servicio',
            'calificaciones'
        )
        ->where(
            'profesional_id',
            $usuario->profesional->id
        )
        ->latest()
        ->get();

        return view(
            'solicitudes.recibidas',
            compact('solicitudes')
        );
    }

    public function mostrar(Solicitud $solicitud)
    {
        $usuario = auth()->user();

        $esCliente =
            $solicitud->usuario_id === $usuario->id;

        $esProfesional =
            $usuario->profesional &&
            $solicitud->profesional_id ===
            $usuario->profesional->id;

        abort_if(
            !$esCliente &&
            !$esProfesional,
            403
        );

        $solicitud->load(
            'usuario',
            'profesional.usuario',
            'servicio',
            'calificaciones'
        );

        return view(
            'solicitudes.mostrar',
            compact(
                'solicitud',
                'esCliente',
                'esProfesional'
            )
        );
    }

    public function calificar(Solicitud $solicitud)
    {
        $usuario = auth()->user();

        abort_if(
            $solicitud->estado !== 'terminada',
            403
        );

        $solicitud->load(
            'usuario',
            'profesional.usuario',
            'servicio'
        );

        $esCliente =
            $solicitud->usuario_id === $usuario->id;

        $esProfesional =
            $usuario->profesional &&
            $solicitud->profesional_id ===
            $usuario->profesional->id;

        abort_if(
            !$esCliente &&
            !$esProfesional,
            403
        );

        $yaCalifico = Calificacion::where(
            'solicitud_id',
            $solicitud->id
        )
        ->where(
            'evaluador_id',
            $usuario->id
        )
        ->exists();

        abort_if(
            $yaCalifico,
            409,
            'Ya calificaste esta solicitud.'
        );

        if ($esCliente) {
            $evaluado =
                $solicitud->profesional->usuario;

            $tipo = 'profesional';
        } else {
            $evaluado =
                $solicitud->usuario;

            $tipo = 'cliente';
        }

        return view(
            'calificacion',
            compact(
                'evaluado',
                'tipo',
                'solicitud'
            )
        );
    }

    public function guardarCalificacion(
        Request $request,
        Solicitud $solicitud
    ) {
        $usuario = $request->user();

        if ($solicitud->estado !== 'terminada') {
            return response()->json([
                'message' => 'Solo puedes calificar trabajos terminados.'
            ], 403);
        }

        $solicitud->load(
            'usuario',
            'profesional.usuario'
        );

        $esCliente =
            $solicitud->usuario_id === $usuario->id;

        $esProfesional =
            $usuario->profesional &&
            $solicitud->profesional_id ===
            $usuario->profesional->id;

        if (!$esCliente && !$esProfesional) {
            return response()->json([
                'message' => 'No tienes permiso para realizar esta calificación.'
            ], 403);
        }

        $yaCalifico = Calificacion::where(
            'solicitud_id',
            $solicitud->id
        )
        ->where(
            'evaluador_id',
            $usuario->id
        )
        ->exists();

        if ($yaCalifico) {
            return response()->json([
                'message' => 'Ya calificaste esta solicitud.'
            ], 409);
        }

        if ($esCliente) {
            $evaluado =
                $solicitud->profesional->usuario;

            $tipo = 'profesional';

            $criteriosEsperados = [
                'calidad_trabajo',
                'puntualidad',
                'comunicacion',
                'profesionalismo',
                'cumplimiento'
            ];
        } else {
            $evaluado =
                $solicitud->usuario;

            $tipo = 'cliente';

            $criteriosEsperados = [
                'claridad_requerimientos',
                'comunicacion',
                'trato',
                'responsabilidad',
                'cumplimiento'
            ];
        }

        $datos = $request->validate([
            'criterios' => [
                'required',
                'array'
            ],
            'criterios.*' => [
                'required',
                'numeric',
                function (
                    $attribute,
                    $value,
                    $fail
                ) {
                    $valor = (float) $value;

                    if (
                        $valor < 0.5 ||
                        $valor > 5
                    ) {
                        $fail(
                            'Cada calificación debe estar entre 0.5 y 5.'
                        );

                        return;
                    }

                    $doble = $valor * 2;

                    if (
                        abs(
                            $doble -
                            round($doble)
                        ) > 0.00001
                    ) {
                        $fail(
                            'Las calificaciones deben avanzar en intervalos de 0.5.'
                        );
                    }
                }
            ],
            'comentario' => [
                'nullable',
                'string',
                'max:500'
            ]
        ]);

        $criterios =
            $datos['criterios'];

        $clavesRecibidas =
            array_keys($criterios);

        sort($clavesRecibidas);
        sort($criteriosEsperados);

        if (
            $clavesRecibidas !==
            $criteriosEsperados
        ) {
            return response()->json([
                'message' => 'Los aspectos enviados no corresponden al tipo de evaluación.'
            ], 422);
        }

        $criterios = collect(
            $criterios
        )
        ->map(function ($valor) {
            return (float) $valor;
        })
        ->all();

        $promedio = round(
            array_sum($criterios) /
            count($criterios),
            1
        );

        Calificacion::create([
            'solicitud_id' =>
                $solicitud->id,

            'evaluador_id' =>
                $usuario->id,

            'evaluado_id' =>
                $evaluado->id,

            'tipo_evaluado' =>
                $tipo,

            'criterios' =>
                $criterios,

            'promedio' =>
                $promedio,

            'comentario' =>
                $datos['comentario'] ?? null
        ]);

        return response()->json([
            'message' => 'Calificación publicada correctamente.',
            'promedio' => $promedio
        ]);
    }

    public function aceptar(Solicitud $solicitud)
    {
        $usuario = auth()->user();

        abort_if(
            !$usuario->profesional ||
            $solicitud->profesional_id !==
            $usuario->profesional->id,
            403
        );

        if (
            $solicitud->estado !== 'pendiente'
        ) {
            return back()->withErrors([
                'estado' => 'Esta solicitud ya fue respondida.'
            ]);
        }

        $solicitud->update([
            'estado' => 'aceptada'
        ]);

        $solicitud->load(
            'usuario',
            'profesional.usuario',
            'servicio'
        );

        $solicitud->usuario->notify(
            new SolicitudEstadoActualizado(
                $solicitud
            )
        );

        return back()->with(
            'exito',
            'Solicitud aceptada.'
        );
    }

    public function rechazar(Solicitud $solicitud)
    {
        $usuario = auth()->user();

        abort_if(
            !$usuario->profesional ||
            $solicitud->profesional_id !==
            $usuario->profesional->id,
            403
        );

        if (
            $solicitud->estado !== 'pendiente'
        ) {
            return back()->withErrors([
                'estado' => 'Esta solicitud ya fue respondida.'
            ]);
        }

        $solicitud->update([
            'estado' => 'rechazada'
        ]);

        $solicitud->load(
            'usuario',
            'profesional.usuario',
            'servicio'
        );

        $solicitud->usuario->notify(
            new SolicitudEstadoActualizado(
                $solicitud
            )
        );

        return back()->with(
            'exito',
            'Solicitud rechazada.'
        );
    }

    public function cancelar(Solicitud $solicitud)
    {
        $usuario = auth()->user();

        abort_if(
            $solicitud->usuario_id !==
            $usuario->id,
            403
        );

        if (
            $solicitud->estado !== 'pendiente'
        ) {
            return back()->withErrors([
                'estado' => 'Solo puedes cancelar solicitudes pendientes.'
            ]);
        }

        $solicitud->update([
            'estado' => 'cancelada'
        ]);

        return back()->with(
            'exito',
            'Solicitud cancelada.'
        );
    }

    public function terminar(Solicitud $solicitud)
    {
        $usuario = auth()->user();

        abort_if(
            !$usuario->profesional ||
            $solicitud->profesional_id !==
            $usuario->profesional->id,
            403
        );

        if (
            $solicitud->estado !== 'aceptada'
        ) {
            return back()->withErrors([
                'estado' => 'Solo puedes terminar un trabajo aceptado.'
            ]);
        }

        $solicitud->update([
            'estado' => 'terminada'
        ]);

        return back()->with(
            'exito',
            'El trabajo ha sido marcado como terminado.'
        );
    }
}