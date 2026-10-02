<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Notifications\SolicitudEstadoActualizado;
use App\Notifications\SolicitudRecibida;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function crear(Usuario $destinatario)
    {
        $solicitante = auth()->user();

        $solicitante->loadMissing('rol');

        abort_if(
            !in_array(
                $solicitante->rol->nombre,
                ['usuario', 'profesional', 'proveedor']
            ),
            403
        );

        abort_if(
            $solicitante->id === $destinatario->id,
            403
        );

        $destinatario->load(
            'rol',
            'profesional.servicios',
            'proveedor.materiales'
        );

        abort_if(
            !in_array(
                $destinatario->rol->nombre,
                ['profesional', 'proveedor']
            ),
            404
        );

        if ($destinatario->rol->nombre === 'profesional') {
            abort_if(
                !$destinatario->profesional ||
                $destinatario->profesional->servicios->isEmpty(),
                404
            );
        }

        if ($destinatario->rol->nombre === 'proveedor') {
            abort_if(
                !$destinatario->proveedor,
                404
            );

            $tieneMaterialesDisponibles = $destinatario
                ->proveedor
                ->materiales()
                ->where('materiales.activo', true)
                ->wherePivot('disponible', true)
                ->exists();

            abort_if(
                !$tieneMaterialesDisponibles,
                404
            );
        }

        return view(
            'solicitudes.crear',
            compact('destinatario')
        );
    }

    public function guardar(Request $request)
    {
        $solicitante = $request->user();

        $solicitante->loadMissing('rol');

        abort_if(
            !in_array(
                $solicitante->rol->nombre,
                ['usuario', 'profesional', 'proveedor']
            ),
            403
        );

        $datos = $request->validate([
            'destinatario_id' => [
                'required',
                'integer',
                'exists:usuarios,id'
            ],

            'servicio_id' => [
                'nullable',
                'integer',
                'exists:servicios,id'
            ],

            'materiales' => [
                'nullable',
                'array',
                'min:1'
            ],

            'materiales.*' => [
                'integer',
                'distinct',
                'exists:materiales,id'
            ],

            'descripcion' => [
                'required',
                'string',
                'min:10',
                'max:1000'
            ]
        ]);

        $destinatario = Usuario::with(
            'rol',
            'profesional.servicios',
            'proveedor.materiales'
        )->findOrFail(
            $datos['destinatario_id']
        );

        if ($destinatario->id === $solicitante->id) {
            return back()
                ->withErrors([
                    'destinatario_id' =>
                        'No puedes enviarte una solicitud a ti mismo.'
                ])
                ->withInput();
        }

        if (
            !in_array(
                $destinatario->rol->nombre,
                ['profesional', 'proveedor']
            )
        ) {
            return back()
                ->withErrors([
                    'destinatario_id' =>
                        'Este usuario no puede recibir solicitudes.'
                ])
                ->withInput();
        }

        $servicioId = null;
        $materialesIds = [];

        if ($destinatario->rol->nombre === 'profesional') {
            if (empty($datos['servicio_id'])) {
                return back()
                    ->withErrors([
                        'servicio_id' =>
                            'Selecciona un servicio.'
                    ])
                    ->withInput();
            }

            if (!$destinatario->profesional) {
                return back()
                    ->withErrors([
                        'destinatario_id' =>
                            'El perfil profesional no está disponible.'
                    ])
                    ->withInput();
            }

            $ofreceServicio = $destinatario
                ->profesional
                ->servicios()
                ->where(
                    'servicios.id',
                    $datos['servicio_id']
                )
                ->exists();

            if (!$ofreceServicio) {
                return back()
                    ->withErrors([
                        'servicio_id' =>
                            'El profesional no ofrece el servicio seleccionado.'
                    ])
                    ->withInput();
            }

            $servicioId = $datos['servicio_id'];
        }

        if ($destinatario->rol->nombre === 'proveedor') {
            if (empty($datos['materiales'])) {
                return back()
                    ->withErrors([
                        'materiales' =>
                            'Selecciona al menos un material o producto.'
                    ])
                    ->withInput();
            }

            if (!$destinatario->proveedor) {
                return back()
                    ->withErrors([
                        'destinatario_id' =>
                            'El perfil del proveedor no está disponible.'
                    ])
                    ->withInput();
            }

            $materialesIds = collect($datos['materiales'])
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            $materialesDisponibles = $destinatario
                ->proveedor
                ->materiales()
                ->whereIn(
                    'materiales.id',
                    $materialesIds
                )
                ->where(
                    'materiales.activo',
                    true
                )
                ->wherePivot(
                    'disponible',
                    true
                )
                ->pluck('materiales.id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();

            sort($materialesIds);
            sort($materialesDisponibles);

            if ($materialesIds !== $materialesDisponibles) {
                return back()
                    ->withErrors([
                        'materiales' =>
                            'Uno o más materiales seleccionados no están disponibles con este proveedor.'
                    ])
                    ->withInput();
            }
        }

        $solicitud = Solicitud::create([
            'solicitante_id' => $solicitante->id,
            'destinatario_id' => $destinatario->id,
            'servicio_id' => $servicioId,
            'descripcion' => $datos['descripcion'],
            'estado' => 'pendiente'
        ]);

        if (
            $destinatario->rol->nombre === 'proveedor' &&
            !empty($materialesIds)
        ) {
            $solicitud
                ->materiales()
                ->sync($materialesIds);
        }

        $solicitud->load(
            'solicitante',
            'destinatario',
            'servicio',
            'materiales'
        );

        $destinatario->notify(
            new SolicitudRecibida(
                $solicitud
            )
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

        $usuario->loadMissing('rol');

        abort_if(
            !in_array(
                $usuario->rol->nombre,
                ['usuario', 'profesional', 'proveedor']
            ),
            403
        );

        $solicitudes = Solicitud::with(
            'solicitante.rol',
            'destinatario.rol',
            'servicio',
            'materiales',
            'calificaciones'
        )
            ->where(
                'solicitante_id',
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

        $usuario->loadMissing('rol');

        abort_if(
            !in_array(
                $usuario->rol->nombre,
                ['profesional', 'proveedor']
            ),
            403
        );

        $solicitudes = Solicitud::with(
            'solicitante.rol',
            'destinatario.rol',
            'servicio',
            'materiales',
            'calificaciones'
        )
            ->where(
                'destinatario_id',
                $usuario->id
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

        $esSolicitante =
            $solicitud->solicitante_id ===
            $usuario->id;

        $esDestinatario =
            $solicitud->destinatario_id ===
            $usuario->id;

        abort_if(
            !$esSolicitante &&
            !$esDestinatario,
            403
        );

        $solicitud->load(
            'solicitante.rol',
            'destinatario.rol',
            'servicio',
            'materiales',
            'calificaciones'
        );

        return view(
            'solicitudes.mostrar',
            compact(
                'solicitud',
                'esSolicitante',
                'esDestinatario'
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
            'solicitante.rol',
            'destinatario.rol',
            'servicio',
            'materiales'
        );

        $esSolicitante =
            $solicitud->solicitante_id ===
            $usuario->id;

        $esDestinatario =
            $solicitud->destinatario_id ===
            $usuario->id;

        abort_if(
            !$esSolicitante &&
            !$esDestinatario,
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

        if ($esSolicitante) {
            $evaluado = $solicitud->destinatario;

            $tipo =
                $solicitud
                    ->destinatario
                    ->rol
                    ->nombre === 'proveedor'
                    ? 'proveedor'
                    : 'profesional';
        } else {
            $evaluado = $solicitud->solicitante;
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
                'message' =>
                    'Solo puedes calificar solicitudes terminadas.'
            ], 403);
        }

        $solicitud->load(
            'solicitante.rol',
            'destinatario.rol'
        );

        $esSolicitante =
            $solicitud->solicitante_id ===
            $usuario->id;

        $esDestinatario =
            $solicitud->destinatario_id ===
            $usuario->id;

        if (
            !$esSolicitante &&
            !$esDestinatario
        ) {
            return response()->json([
                'message' =>
                    'No tienes permiso para realizar esta calificación.'
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
                'message' =>
                    'Ya calificaste esta solicitud.'
            ], 409);
        }

        if ($esSolicitante) {
            $evaluado = $solicitud->destinatario;

            if (
                $evaluado->rol->nombre ===
                'proveedor'
            ) {
                $tipo = 'proveedor';

                $criteriosEsperados = [
                    'calidad_materiales',
                    'cumplimiento_entrega',
                    'comunicacion',
                    'atencion',
                    'cumplimiento'
                ];
            } else {
                $tipo = 'profesional';

                $criteriosEsperados = [
                    'calidad_trabajo',
                    'puntualidad',
                    'comunicacion',
                    'profesionalismo',
                    'cumplimiento'
                ];
            }
        } else {
            $evaluado = $solicitud->solicitante;
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

        $criterios = $datos['criterios'];

        $clavesRecibidas = array_keys(
            $criterios
        );

        sort($clavesRecibidas);
        sort($criteriosEsperados);

        if (
            $clavesRecibidas !==
            $criteriosEsperados
        ) {
            return response()->json([
                'message' =>
                    'Los aspectos enviados no corresponden al tipo de evaluación.'
            ], 422);
        }

        $criterios = collect(
            $criterios
        )
            ->map(
                function ($valor) {
                    return (float) $valor;
                }
            )
            ->all();

        $promedio = round(
            array_sum($criterios) /
            count($criterios),
            1
        );

        Calificacion::create([
            'solicitud_id' => $solicitud->id,
            'evaluador_id' => $usuario->id,
            'evaluado_id' => $evaluado->id,
            'tipo_evaluado' => $tipo,
            'criterios' => $criterios,
            'promedio' => $promedio,
            'comentario' =>
                $datos['comentario'] ?? null
        ]);

        return response()->json([
            'message' =>
                'Calificación publicada correctamente.',
            'promedio' => $promedio
        ]);
    }

    public function aceptar(Solicitud $solicitud)
    {
        $usuario = auth()->user();

        abort_if(
            $solicitud->destinatario_id !==
            $usuario->id,
            403
        );

        if (
            $solicitud->estado !==
            'pendiente'
        ) {
            return back()
                ->withErrors([
                    'estado' =>
                        'Esta solicitud ya fue respondida.'
                ]);
        }

        $solicitud->update([
            'estado' => 'aceptada'
        ]);

        $solicitud->load(
            'solicitante',
            'destinatario',
            'servicio',
            'materiales'
        );

        $solicitud
            ->solicitante
            ->notify(
                new SolicitudEstadoActualizado(
                    $solicitud
                )
            );

        return back()
            ->with(
                'exito',
                'Solicitud aceptada.'
            );
    }

    public function rechazar(Solicitud $solicitud)
    {
        $usuario = auth()->user();

        abort_if(
            $solicitud->destinatario_id !==
            $usuario->id,
            403
        );

        if (
            $solicitud->estado !==
            'pendiente'
        ) {
            return back()
                ->withErrors([
                    'estado' =>
                        'Esta solicitud ya fue respondida.'
                ]);
        }

        $solicitud->update([
            'estado' => 'rechazada'
        ]);

        $solicitud->load(
            'solicitante',
            'destinatario',
            'servicio',
            'materiales'
        );

        $solicitud
            ->solicitante
            ->notify(
                new SolicitudEstadoActualizado(
                    $solicitud
                )
            );

        return back()
            ->with(
                'exito',
                'Solicitud rechazada.'
            );
    }

    public function cancelar(Solicitud $solicitud)
    {
        $usuario = auth()->user();

        abort_if(
            $solicitud->solicitante_id !==
            $usuario->id,
            403
        );

        if (
            $solicitud->estado !==
            'pendiente'
        ) {
            return back()
                ->withErrors([
                    'estado' =>
                        'Solo puedes cancelar solicitudes pendientes.'
                ]);
        }

        $solicitud->update([
            'estado' => 'cancelada'
        ]);

        return back()
            ->with(
                'exito',
                'Solicitud cancelada.'
            );
    }

    public function terminar(Solicitud $solicitud)
    {
        $usuario = auth()->user();

        abort_if(
            $solicitud->destinatario_id !==
            $usuario->id,
            403
        );

        if (
            $solicitud->estado !==
            'aceptada'
        ) {
            return back()
                ->withErrors([
                    'estado' =>
                        'Solo puedes terminar una solicitud aceptada.'
                ]);
        }

        $solicitud->update([
            'estado' => 'terminada'
        ]);

        return back()
            ->with(
                'exito',
                'La solicitud ha sido marcada como terminada.'
            );
    }
}