<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Profesional;
use App\Models\Profesion;
use App\Models\SolicitudAprobacionProfesional;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegistroProfesionalController extends Controller
{
    private const ROL_PROFESIONAL = 'profesional';

    private const ESTADO_PENDIENTE = 'pendiente';
    private const ESTADO_NO_REQUERIDA = 'no_requerida';

    private const SESION_USUARIO = 'registro_usuario_id';

    public function mostrar(): View|RedirectResponse
    {
        $usuario = $this->obtenerUsuarioRegistro();

        if (!$usuario) {
            return $this->redireccionRegistroExpirado();
        }

        $profesiones = $this->obtenerProfesionesActivas();

        return view('registerprof', compact(
            'usuario',
            'profesiones'
        ));
    }

    public function guardar(Request $request): RedirectResponse
    {
        $usuario = $this->obtenerUsuarioRegistro();

        if (!$usuario) {
            return $this->redireccionRegistroExpirado();
        }

        $datos = $request->validate(
            $this->obtenerReglasValidacion(),
            $this->obtenerMensajesValidacion()
        );

        [
            $especialidadesIds,
            $especialidadesAprobacionIds,
        ] = $this->validarSeleccionProfesional($datos);

        $requiereAprobacion = $especialidadesAprobacionIds->isNotEmpty();

        $estadoAprobacion = $requiereAprobacion
            ? self::ESTADO_PENDIENTE
            : self::ESTADO_NO_REQUERIDA;

        DB::transaction(function () use (
            $usuario,
            $datos,
            $especialidadesIds,
            $especialidadesAprobacionIds,
            $requiereAprobacion,
            $estadoAprobacion
        ) {
            $profesional = Profesional::updateOrCreate(
                ['usuario_id' => $usuario->id],
                [
                    'anios_experiencia' => $datos['anios_experiencia'],
                    'descripcion' => $datos['descripcion'],
                    'portafolio_url' => $datos['portafolio_url'] ?? null,
                    'zona_trabajo' => $datos['zona_trabajo'],
                    'estado_aprobacion' => $estadoAprobacion,
                ]
            );

            $profesional->profesiones()->sync([$datos['profesion_id']]);

            $profesional->especialidades()->sync(
                $especialidadesIds->all()
            );

            $this->sincronizarSolicitudAprobacion(
                $profesional,
                $especialidadesAprobacionIds,
                $requiereAprobacion
            );
        });

        session()->forget(self::SESION_USUARIO);

        $mensaje = $requiereAprobacion
            ? 'Tu registro profesional se completó correctamente. Tu perfil podrá aparecer en el catálogo, pero la especialidad Supervisor de obra permanecerá pendiente hasta que un administrador la apruebe.'
            : 'Tu registro como profesional se completó correctamente.';

        return redirect()
            ->route('login')
            ->with('success', $mensaje);
    }

    private function obtenerUsuarioRegistro(): ?Usuario
    {
        $usuarioId = session(self::SESION_USUARIO);

        if (!$usuarioId) {
            return null;
        }

        $usuario = Usuario::with('rol')->findOrFail($usuarioId);

        abort_unless(
            $usuario->rol?->nombre === self::ROL_PROFESIONAL,
            403
        );

        return $usuario;
    }

    private function obtenerProfesionesActivas(): Collection
    {
        return Profesion::query()
            ->where('activo', true)
            ->with([
                'especialidades' => fn ($query) =>
                    $query->where('activo', true)->orderBy('nombre'),
            ])
            ->orderBy('nombre')
            ->get();
    }

    private function obtenerReglasValidacion(): array
    {
        return [
            'profesion_id' => [
                'required',
                'integer',
                'exists:profesiones,id',
            ],
            'especialidades' => [
                'required',
                'array',
                'min:1',
            ],
            'especialidades.*' => [
                'required',
                'integer',
                'distinct',
                'exists:especialidades,id',
            ],
            'anios_experiencia' => [
                'required',
                'integer',
                'min:0',
                'max:80',
            ],
            'descripcion' => [
                'required',
                'string',
                'max:500',
            ],
            'portafolio_url' => [
                'nullable',
                'url',
                'max:500',
            ],
            'zona_trabajo' => [
                'required',
                'string',
                'max:200',
            ],
        ];
    }

    private function obtenerMensajesValidacion(): array
    {
        return [
            'profesion_id.required' => 'Debes seleccionar una profesión.',
            'profesion_id.exists' => 'La profesión seleccionada no es válida.',

            'especialidades.required' => 'Debes seleccionar al menos una especialidad.',
            'especialidades.array' => 'Las especialidades seleccionadas no son válidas.',
            'especialidades.min' => 'Debes seleccionar al menos una especialidad.',
            'especialidades.*.exists' => 'Una de las especialidades seleccionadas no es válida.',
            'especialidades.*.distinct' => 'No puedes seleccionar la misma especialidad más de una vez.',

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
        ];
    }

    private function validarSeleccionProfesional(array $datos): array
    {
        $profesionValida = Profesion::query()
            ->whereKey($datos['profesion_id'])
            ->where('activo', true)
            ->exists();

        if (!$profesionValida) {
            throw ValidationException::withMessages([
                'profesion_id' => 'La profesión seleccionada no está disponible.',
            ]);
        }

        $especialidadesIds = collect($datos['especialidades'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $especialidades = Especialidad::query()
            ->whereIn('id', $especialidadesIds)
            ->where('profesion_id', $datos['profesion_id'])
            ->where('activo', true)
            ->get();

        if ($especialidades->count() !== $especialidadesIds->count()) {
            throw ValidationException::withMessages([
                'especialidades' => 'Una o más especialidades no corresponden a la profesión seleccionada.',
            ]);
        }

        $especialidadesAprobacionIds = $especialidades
            ->where('requiere_aprobacion', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        return [
            $especialidadesIds,
            $especialidadesAprobacionIds,
        ];
    }

    private function sincronizarSolicitudAprobacion(
        Profesional $profesional,
        Collection $especialidadesAprobacionIds,
        bool $requiereAprobacion
    ): void {
        SolicitudAprobacionProfesional::query()
            ->where('profesional_id', $profesional->id)
            ->where('estado', self::ESTADO_PENDIENTE)
            ->delete();

        if (!$requiereAprobacion) {
            return;
        }

        SolicitudAprobacionProfesional::create([
            'profesional_id' => $profesional->id,
            'especialidades_requieren_aprobacion' => $especialidadesAprobacionIds->all(),
            'estado' => self::ESTADO_PENDIENTE,
        ]);
    }

    private function redireccionRegistroExpirado(): RedirectResponse
    {
        return redirect()
            ->route('register')
            ->withErrors([
                'registro' => 'Debes iniciar el registro nuevamente.',
            ]);
    }
}