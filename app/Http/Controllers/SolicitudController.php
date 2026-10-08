<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Notifications\SolicitudEstadoActualizado;
use App\Notifications\SolicitudRecibida;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SolicitudController extends Controller
{
    private const ROLES_SOLICITANTES = ['usuario', 'profesional', 'proveedor'];
    private const ROLES_DESTINATARIOS = ['profesional', 'proveedor'];

    private const ROL_PROFESIONAL = 'profesional';
    private const ROL_PROVEEDOR = 'proveedor';

    private const ESTADO_PENDIENTE = 'pendiente';
    private const ESTADO_ACEPTADA = 'aceptada';
    private const ESTADO_RECHAZADA = 'rechazada';
    private const ESTADO_CANCELADA = 'cancelada';
    private const ESTADO_TERMINADA = 'terminada';

    private const CRITERIOS = [
        'profesional' => [
            'calidad_trabajo',
            'puntualidad',
            'comunicacion',
            'profesionalismo',
            'cumplimiento',
        ],
        'proveedor' => [
            'calidad_materiales',
            'cumplimiento_entrega',
            'comunicacion',
            'atencion',
            'cumplimiento',
        ],
        'cliente' => [
            'claridad_requerimientos',
            'comunicacion',
            'trato',
            'responsabilidad',
            'cumplimiento',
        ],
    ];

    private const RELACIONES_LISTADO = [
        'solicitante.rol',
        'destinatario.rol',
        'servicio',
        'materiales',
        'calificaciones',
    ];

    public function crear(Usuario $destinatario): View
    {
        $solicitante = auth()->user();

        $this->validarRolUsuario($solicitante, self::ROLES_SOLICITANTES);

        abort_if($solicitante->id === $destinatario->id, 403);

        $destinatario->load([
            'rol',
            'profesional.servicios',
            'proveedor.materiales',
        ]);

        $this->validarDestinatarioDisponible($destinatario);

        return view('solicitudes.crear', compact('destinatario'));
    }

    public function guardar(Request $request): RedirectResponse
    {
        $solicitante = $request->user();

        $this->validarRolUsuario($solicitante, self::ROLES_SOLICITANTES);

        $datos = $request->validate([
            'destinatario_id' => ['required', 'integer', 'exists:usuarios,id'],
            'servicio_id' => ['nullable', 'integer', 'exists:servicios,id'],
            'materiales' => ['nullable', 'array', 'min:1'],
            'materiales.*' => ['integer', 'distinct', 'exists:materiales,id'],
            'descripcion' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $destinatario = Usuario::with([
            'rol',
            'profesional.servicios',
            'proveedor.materiales',
        ])->findOrFail($datos['destinatario_id']);

        [$servicioId, $materialesIds] = $this->prepararDestinoSolicitud(
            $solicitante,
            $destinatario,
            $datos
        );

        $solicitud = DB::transaction(function () use (
            $solicitante,
            $destinatario,
            $datos,
            $servicioId,
            $materialesIds
        ) {
            $solicitud = Solicitud::create([
                'solicitante_id' => $solicitante->id,
                'destinatario_id' => $destinatario->id,
                'servicio_id' => $servicioId,
                'descripcion' => $datos['descripcion'],
                'estado' => self::ESTADO_PENDIENTE,
            ]);

            if ($destinatario->rol->nombre === self::ROL_PROVEEDOR && $materialesIds !== []) {
                $solicitud->materiales()->sync($materialesIds);
            }

            return $solicitud;
        });

        $solicitud->load([
            'solicitante',
            'destinatario',
            'servicio',
            'materiales',
        ]);

        $destinatario->notify(new SolicitudRecibida($solicitud));

        return redirect()
            ->route('solicitudes.mias')
            ->with('exito', 'Solicitud enviada correctamente.');
    }

    public function mias(): View
    {
        $usuario = auth()->user();

        $this->validarRolUsuario($usuario, self::ROLES_SOLICITANTES);

        $solicitudes = Solicitud::with(self::RELACIONES_LISTADO)
            ->where('solicitante_id', $usuario->id)
            ->latest()
            ->get();

        return view('solicitudes.mias', compact('solicitudes'));
    }

    public function recibidas(): View
    {
        $usuario = auth()->user();

        $this->validarRolUsuario($usuario, self::ROLES_DESTINATARIOS);

        $solicitudes = Solicitud::with(self::RELACIONES_LISTADO)
            ->where('destinatario_id', $usuario->id)
            ->latest()
            ->get();

        return view('solicitudes.recibidas', compact('solicitudes'));
    }

    public function mostrar(Solicitud $solicitud): View
    {
        $usuario = auth()->user();

        [$esSolicitante, $esDestinatario] = $this->obtenerParticipacion(
            $solicitud,
            $usuario
        );

        abort_if(!$esSolicitante && !$esDestinatario, 403);

        $solicitud->load(self::RELACIONES_LISTADO);

        return view('solicitudes.mostrar', compact(
            'solicitud',
            'esSolicitante',
            'esDestinatario'
        ));
    }

    public function calificar(Solicitud $solicitud): View
    {
        $usuario = auth()->user();

        abort_if($solicitud->estado !== self::ESTADO_TERMINADA, 403);

        $solicitud->load([
            'solicitante.rol',
            'destinatario.rol',
            'servicio',
            'materiales',
        ]);

        [$esSolicitante, $esDestinatario] = $this->obtenerParticipacion(
            $solicitud,
            $usuario
        );

        abort_if(!$esSolicitante && !$esDestinatario, 403);

        abort_if(
            $this->usuarioYaCalifico($solicitud, $usuario),
            409,
            'Ya calificaste esta solicitud.'
        );

        [$evaluado, $tipo] = $this->obtenerContextoEvaluacion(
            $solicitud,
            $esSolicitante
        );

        return view('calificacion', compact(
            'evaluado',
            'tipo',
            'solicitud'
        ));
    }

    public function guardarCalificacion(Request $request, Solicitud $solicitud): JsonResponse
    {
        $usuario = $request->user();

        if ($solicitud->estado !== self::ESTADO_TERMINADA) {
            return response()->json([
                'message' => 'Solo puedes calificar solicitudes terminadas.',
            ], 403);
        }

        $solicitud->load([
            'solicitante.rol',
            'destinatario.rol',
        ]);

        [$esSolicitante, $esDestinatario] = $this->obtenerParticipacion(
            $solicitud,
            $usuario
        );

        if (!$esSolicitante && !$esDestinatario) {
            return response()->json([
                'message' => 'No tienes permiso para realizar esta calificación.',
            ], 403);
        }

        if ($this->usuarioYaCalifico($solicitud, $usuario)) {
            return response()->json([
                'message' => 'Ya calificaste esta solicitud.',
            ], 409);
        }

        [$evaluado, $tipo, $criteriosEsperados] = $this->obtenerContextoEvaluacion(
            $solicitud,
            $esSolicitante
        );

        $datos = $request->validate([
            'criterios' => ['required', 'array'],
            'criterios.*' => ['required', 'numeric', $this->reglaCalificacion()],
            'comentario' => ['nullable', 'string', 'max:500'],
        ]);

        $criterios = $datos['criterios'];

        if (!$this->criteriosCoinciden($criterios, $criteriosEsperados)) {
            return response()->json([
                'message' => 'Los aspectos enviados no corresponden al tipo de evaluación.',
            ], 422);
        }

        $criterios = collect($criterios)
            ->map(fn ($valor) => (float) $valor)
            ->all();

        $promedio = round(array_sum($criterios) / count($criterios), 1);

        Calificacion::create([
            'solicitud_id' => $solicitud->id,
            'evaluador_id' => $usuario->id,
            'evaluado_id' => $evaluado->id,
            'tipo_evaluado' => $tipo,
            'criterios' => $criterios,
            'promedio' => $promedio,
            'comentario' => $datos['comentario'] ?? null,
        ]);

        return response()->json([
            'message' => 'Calificación publicada correctamente.',
            'promedio' => $promedio,
        ]);
    }

    public function aceptar(Solicitud $solicitud): RedirectResponse
    {
        return $this->responderSolicitud(
            $solicitud,
            self::ESTADO_ACEPTADA,
            'Solicitud aceptada.'
        );
    }

    public function rechazar(Solicitud $solicitud): RedirectResponse
    {
        return $this->responderSolicitud(
            $solicitud,
            self::ESTADO_RECHAZADA,
            'Solicitud rechazada.'
        );
    }

    public function cancelar(Solicitud $solicitud): RedirectResponse
    {
        $usuario = auth()->user();

        abort_if($solicitud->solicitante_id !== $usuario->id, 403);

        if ($solicitud->estado !== self::ESTADO_PENDIENTE) {
            return back()->withErrors([
                'estado' => 'Solo puedes cancelar solicitudes pendientes.',
            ]);
        }

        $solicitud->update([
            'estado' => self::ESTADO_CANCELADA,
        ]);

        return back()->with('exito', 'Solicitud cancelada.');
    }

    public function terminar(Solicitud $solicitud): RedirectResponse
    {
        $usuario = auth()->user();

        abort_if($solicitud->destinatario_id !== $usuario->id, 403);

        if ($solicitud->estado !== self::ESTADO_ACEPTADA) {
            return back()->withErrors([
                'estado' => 'Solo puedes terminar una solicitud aceptada.',
            ]);
        }

        $solicitud->update([
            'estado' => self::ESTADO_TERMINADA,
        ]);

        return back()->with(
            'exito',
            'La solicitud ha sido marcada como terminada.'
        );
    }

    private function validarRolUsuario(Usuario $usuario, array $rolesPermitidos): void
    {
        $usuario->loadMissing('rol');

        abort_if(
            !$usuario->rol || !in_array($usuario->rol->nombre, $rolesPermitidos, true),
            403
        );
    }

    private function validarDestinatarioDisponible(Usuario $destinatario): void
    {
        abort_if(
            !$destinatario->rol ||
            !in_array($destinatario->rol->nombre, self::ROLES_DESTINATARIOS, true),
            404
        );

        if ($destinatario->rol->nombre === self::ROL_PROFESIONAL) {
            abort_if(
                !$destinatario->profesional ||
                $destinatario->profesional->servicios->isEmpty(),
                404
            );

            return;
        }

        abort_if(!$destinatario->proveedor, 404);

        $tieneMaterialesDisponibles = $destinatario->proveedor
            ->materiales()
            ->where('materiales.activo', true)
            ->wherePivot('disponible', true)
            ->exists();

        abort_if(!$tieneMaterialesDisponibles, 404);
    }

    private function prepararDestinoSolicitud(
        Usuario $solicitante,
        Usuario $destinatario,
        array $datos
    ): array {
        if ($destinatario->id === $solicitante->id) {
            throw ValidationException::withMessages([
                'destinatario_id' => 'No puedes enviarte una solicitud a ti mismo.',
            ]);
        }

        if (
            !$destinatario->rol ||
            !in_array($destinatario->rol->nombre, self::ROLES_DESTINATARIOS, true)
        ) {
            throw ValidationException::withMessages([
                'destinatario_id' => 'Este usuario no puede recibir solicitudes.',
            ]);
        }

        return $destinatario->rol->nombre === self::ROL_PROFESIONAL
            ? $this->prepararSolicitudProfesional($destinatario, $datos)
            : $this->prepararSolicitudProveedor($destinatario, $datos);
    }

    private function prepararSolicitudProfesional(Usuario $destinatario, array $datos): array
    {
        if (empty($datos['servicio_id'])) {
            throw ValidationException::withMessages([
                'servicio_id' => 'Selecciona un servicio.',
            ]);
        }

        if (!$destinatario->profesional) {
            throw ValidationException::withMessages([
                'destinatario_id' => 'El perfil profesional no está disponible.',
            ]);
        }

        $ofreceServicio = $destinatario->profesional
            ->servicios()
            ->where('servicios.id', $datos['servicio_id'])
            ->exists();

        if (!$ofreceServicio) {
            throw ValidationException::withMessages([
                'servicio_id' => 'El profesional no ofrece el servicio seleccionado.',
            ]);
        }

        return [$datos['servicio_id'], []];
    }

    private function prepararSolicitudProveedor(Usuario $destinatario, array $datos): array
    {
        if (empty($datos['materiales'])) {
            throw ValidationException::withMessages([
                'materiales' => 'Selecciona al menos un material o producto.',
            ]);
        }

        if (!$destinatario->proveedor) {
            throw ValidationException::withMessages([
                'destinatario_id' => 'El perfil del proveedor no está disponible.',
            ]);
        }

        $materialesIds = collect($datos['materiales'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $materialesDisponibles = $destinatario->proveedor
            ->materiales()
            ->whereIn('materiales.id', $materialesIds)
            ->where('materiales.activo', true)
            ->wherePivot('disponible', true)
            ->pluck('materiales.id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();

        if ($materialesIds !== $materialesDisponibles) {
            throw ValidationException::withMessages([
                'materiales' => 'Uno o más materiales seleccionados no están disponibles con este proveedor.',
            ]);
        }

        return [null, $materialesIds];
    }

    private function obtenerParticipacion(Solicitud $solicitud, Usuario $usuario): array
    {
        return [
            $solicitud->solicitante_id === $usuario->id,
            $solicitud->destinatario_id === $usuario->id,
        ];
    }

    private function usuarioYaCalifico(Solicitud $solicitud, Usuario $usuario): bool
    {
        return Calificacion::query()
            ->where('solicitud_id', $solicitud->id)
            ->where('evaluador_id', $usuario->id)
            ->exists();
    }

    private function obtenerContextoEvaluacion(
        Solicitud $solicitud,
        bool $esSolicitante
    ): array {
        if (!$esSolicitante) {
            return [
                $solicitud->solicitante,
                'cliente',
                self::CRITERIOS['cliente'],
            ];
        }

        $evaluado = $solicitud->destinatario;

        $tipo = $evaluado->rol->nombre === self::ROL_PROVEEDOR
            ? self::ROL_PROVEEDOR
            : self::ROL_PROFESIONAL;

        return [
            $evaluado,
            $tipo,
            self::CRITERIOS[$tipo],
        ];
    }

    private function reglaCalificacion(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $valor = (float) $value;

            if ($valor < 0.5 || $valor > 5) {
                $fail('Cada calificación debe estar entre 0.5 y 5.');
                return;
            }

            if (abs(($valor * 2) - round($valor * 2)) > 0.00001) {
                $fail('Las calificaciones deben avanzar en intervalos de 0.5.');
            }
        };
    }

    private function criteriosCoinciden(
        array $criterios,
        array $criteriosEsperados
    ): bool {
        $clavesRecibidas = array_keys($criterios);

        sort($clavesRecibidas);
        sort($criteriosEsperados);

        return $clavesRecibidas === $criteriosEsperados;
    }

    private function responderSolicitud(
        Solicitud $solicitud,
        string $nuevoEstado,
        string $mensaje
    ): RedirectResponse {
        $usuario = auth()->user();

        abort_if($solicitud->destinatario_id !== $usuario->id, 403);

        if ($solicitud->estado !== self::ESTADO_PENDIENTE) {
            return back()->withErrors([
                'estado' => 'Esta solicitud ya fue respondida.',
            ]);
        }

        $solicitud->update([
            'estado' => $nuevoEstado,
        ]);

        $solicitud->load([
            'solicitante',
            'destinatario',
            'servicio',
            'materiales',
        ]);

        $solicitud->solicitante->notify(
            new SolicitudEstadoActualizado($solicitud)
        );

        return back()->with('exito', $mensaje);
    }
}