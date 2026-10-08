<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Material;
use App\Models\Profesional;
use App\Models\Profesion;
use App\Models\Proveedor;
use App\Models\Servicio;
use App\Models\SolicitudAprobacionProfesional;
use App\Models\Usuario;
use App\Notifications\PerfilActualizado;
use App\Services\PerfilNotificacionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PerfilController extends Controller
{
    private const ROL_PROFESIONAL = 'profesional';
    private const ROL_PROVEEDOR = 'proveedor';
    private const ROL_ADMINISTRADOR = 'administrador';

    private const APROBACION_NO_REQUERIDA = 'no_requerida';
    private const APROBACION_PENDIENTE = 'pendiente';
    private const APROBACION_APROBADO = 'aprobado';

    private const RELACIONES_PERFIL = [
        'rol',
        'proveedor.materiales',
        'profesional.profesiones',
        'profesional.especialidades',
        'profesional.servicios',
    ];

    public function mostrar(
        Request $request,
        PerfilNotificacionService $perfilNotificacionService
    ): View {
        $usuario = $request->user()->load(self::RELACIONES_PERFIL);

        $perfilNotificacionService->sincronizar($usuario);

        return view('perfil', compact('usuario'));
    }

    public function editar(Request $request): View
    {
        $usuario = $request->user()->load(self::RELACIONES_PERFIL);
        $rol = $usuario->rol->nombre;

        $profesiones = collect();
        $materiales = collect();
        $servicios = collect();

        if ($rol === self::ROL_PROFESIONAL) {
            $profesiones = $this->obtenerProfesionesActivas();

            $servicios = Servicio::query()
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();
        } elseif ($rol === self::ROL_PROVEEDOR) {
            $materiales = Material::query()
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();
        }

        return view('perfil-editar', compact(
            'usuario',
            'profesiones',
            'materiales',
            'servicios'
        ));
    }

    public function actualizar(
        Request $request,
        PerfilNotificacionService $perfilNotificacionService
    ): RedirectResponse {
        $usuario = $request->user()->load([
            'rol',
            'profesional.especialidades',
        ]);

        $rol = $usuario->rol->nombre;

        $datos = $request->validate(
            $this->obtenerReglasActualizacion($rol),
            $this->obtenerMensajesValidacion()
        );

        $especialidadesIds = collect();
        $especialidadesAprobacionIds = collect();
        $requiereAprobacion = false;
        $estadoAprobacion = null;

        if ($rol === self::ROL_PROFESIONAL) {
            [
                $especialidadesIds,
                $especialidadesAprobacionIds,
                $requiereAprobacion,
                $estadoAprobacion,
            ] = $this->prepararDatosProfesional($usuario, $datos);
        }

        [$fotoPerfil, $fotoNueva, $fotoAnterior] = $this->prepararFotoPerfil(
            $request,
            $usuario
        );

        [$portafolioFotos, $portafolioNuevas, $portafolioAnteriores] =
            $this->prepararPortafolio($request, $usuario);

        $archivosNuevos = array_merge($fotoNueva, $portafolioNuevas);
        $archivosAnteriores = array_merge($fotoAnterior, $portafolioAnteriores);

        try {
            DB::transaction(function () use (
                $usuario,
                $rol,
                $datos,
                $fotoPerfil,
                $portafolioFotos,
                $especialidadesIds,
                $especialidadesAprobacionIds,
                $requiereAprobacion,
                $estadoAprobacion
            ) {
                $this->actualizarUsuario(
                    $usuario,
                    $datos,
                    $fotoPerfil,
                    $portafolioFotos
                );

                if ($rol === self::ROL_PROFESIONAL) {
                    $this->actualizarProfesional(
                        $usuario,
                        $datos,
                        $especialidadesIds,
                        $especialidadesAprobacionIds,
                        $requiereAprobacion,
                        $estadoAprobacion
                    );
                } elseif ($rol === self::ROL_PROVEEDOR) {
                    $this->actualizarProveedor($usuario, $datos);
                }
            });
        } catch (Throwable $exception) {
            $this->eliminarArchivos($archivosNuevos);

            throw $exception;
        }

        $this->eliminarArchivos($archivosAnteriores);

        $usuario->refresh()->load([
            'rol',
            'profesional.servicios',
            'proveedor.materiales',
        ]);

        $perfilNotificacionService->sincronizar($usuario);
        $usuario->notify(new PerfilActualizado());

        $mensaje = $rol === self::ROL_PROFESIONAL
            && $estadoAprobacion === self::APROBACION_PENDIENTE
                ? 'Perfil actualizado correctamente. La especialidad que requiere autorización permanecerá pendiente de revisión, pero tu perfil seguirá visible en el catálogo.'
                : 'Perfil actualizado correctamente.';

        return redirect()
            ->route('perfil')
            ->with('success', $mensaje);
    }

    public function buscar(Request $request): View
    {
        $busqueda = trim((string) $request->input('buscar', ''));
        $tipo = $request->input('tipo', '');
        $profesionId = $request->input('profesion_id');
        $especialidadId = $request->input('especialidad_id');

        $profesiones = $this->obtenerProfesionesActivas();

        $query = Usuario::query()
            ->with(self::RELACIONES_PERFIL)
            ->where('estado', 'activo')
            ->whereRelation(
                'rol',
                'nombre',
                '!=',
                self::ROL_ADMINISTRADOR
            );

        $this->aplicarVisibilidadPublica($query);

        if ($busqueda) {
            $this->aplicarBusqueda($query, $busqueda);
        }

        if ($tipo) {
            $query->whereRelation('rol', 'nombre', $tipo);
        }

        if ($profesionId) {
            $query->whereHas(
                'profesional.profesiones',
                fn ($profesion) =>
                    $profesion->where('profesiones.id', $profesionId)
            );
        }

        if ($especialidadId) {
            $requiereAprobacion = (bool) Especialidad::query()
                ->whereKey($especialidadId)
                ->value('requiere_aprobacion');

            $query->whereHas(
                'profesional',
                function ($profesional) use (
                    $especialidadId,
                    $requiereAprobacion
                ) {
                    $profesional->whereHas(
                        'especialidades',
                        fn ($especialidad) =>
                            $especialidad->where(
                                'especialidades.id',
                                $especialidadId
                            )
                    );

                    if ($requiereAprobacion) {
                        $profesional->where(
                            'estado_aprobacion',
                            self::APROBACION_APROBADO
                        );
                    }
                }
            );
        }

        $usuarios = $query
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        $usuarios->getCollection()->each(function (Usuario $usuario): void {
            $this->prepararEspecialidadesPublicas($usuario);
        });

        return view('usuarios', compact(
            'usuarios',
            'busqueda',
            'tipo',
            'profesionId',
            'especialidadId',
            'profesiones'
        ));
    }

    public function publico(Usuario $usuario): View
    {
        abort_if($usuario->estado !== 'activo', 404);

        $usuario->load(self::RELACIONES_PERFIL);

        if ($usuario->rol->nombre === self::ROL_PROFESIONAL) {
            abort_if(!$usuario->profesional, 404);
        }

        $this->prepararEspecialidadesPublicas($usuario);

        return view('perfil-publico', compact('usuario'));
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

    private function obtenerReglasActualizacion(string $rol): array
    {
        $reglas = [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'ubicacion' => ['nullable', 'string', 'max:200'],
            'descripcion_usuario' => ['nullable', 'string', 'max:500'],
            'foto_perfil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];

        $reglasPortafolio = [
            'portafolio_fotos' => ['nullable', 'array', 'max:3'],
            'portafolio_fotos.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],
        ];

        if ($rol === self::ROL_PROFESIONAL) {
            return $reglas + [
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
                'descripcion_profesional' => [
                    'required',
                    'string',
                    'max:500',
                ],
                'portafolio_url' => [
                    'nullable',
                    'url',
                    'max:500',
                ],
                'zona_trabajo_profesional' => [
                    'required',
                    'string',
                    'max:200',
                ],
                'servicios' => [
                    'nullable',
                    'array',
                ],
                'servicios.*' => [
                    'integer',
                    'distinct',
                    'exists:servicios,id',
                ],
            ] + $reglasPortafolio;
        }

        if ($rol === self::ROL_PROVEEDOR) {
            return $reglas + [
                'descripcion_proveedor' => [
                    'nullable',
                    'string',
                    'max:500',
                ],
                'zona_trabajo_proveedor' => [
                    'required',
                    'string',
                    'max:200',
                ],
                'materiales' => [
                    'required',
                    'array',
                    'min:1',
                ],
                'materiales.*' => [
                    'integer',
                    'distinct',
                    'exists:materiales,id',
                ],
            ] + $reglasPortafolio;
        }

        return $reglas;
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

            'descripcion_profesional.required' => 'Debes agregar una descripción profesional.',
            'descripcion_profesional.max' => 'La descripción profesional no puede superar los 500 caracteres.',

            'portafolio_url.url' => 'El enlace del portafolio no es válido.',
            'portafolio_url.max' => 'El enlace del portafolio es demasiado largo.',

            'zona_trabajo_profesional.required' => 'Debes indicar tu zona de trabajo.',
            'zona_trabajo_profesional.max' => 'La zona de trabajo no puede superar los 200 caracteres.',

            'servicios.*.exists' => 'Uno de los servicios seleccionados no es válido.',
            'servicios.*.distinct' => 'No puedes seleccionar el mismo servicio más de una vez.',

            'materiales.required' => 'Debes seleccionar al menos un material o producto.',
            'materiales.min' => 'Debes seleccionar al menos un material o producto.',
            'materiales.*.exists' => 'Uno de los materiales seleccionados no es válido.',
            'materiales.*.distinct' => 'No puedes seleccionar el mismo material más de una vez.',

            'portafolio_fotos.max' => 'Solo puedes subir un máximo de 3 fotos al portafolio.',
            'portafolio_fotos.*.image' => 'Cada archivo del portafolio debe ser una imagen.',
            'portafolio_fotos.*.mimes' => 'Las fotos del portafolio deben ser JPG, JPEG, PNG o WEBP.',
            'portafolio_fotos.*.max' => 'Cada foto del portafolio puede pesar como máximo 10 MB.',

            'foto_perfil.image' => 'La foto de perfil debe ser una imagen.',
            'foto_perfil.mimes' => 'La foto de perfil debe ser JPG, JPEG, PNG o WEBP.',
            'foto_perfil.max' => 'La foto de perfil puede pesar como máximo 2 MB.',
        ];
    }

    private function prepararDatosProfesional(
        Usuario $usuario,
        array $datos
    ): array {
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
            ->sort()
            ->values();

        $requiereAprobacion = $especialidadesAprobacionIds->isNotEmpty();

        $estadoAprobacion = $this->determinarEstadoAprobacion(
            $usuario,
            $especialidadesAprobacionIds
        );

        return [
            $especialidadesIds,
            $especialidadesAprobacionIds,
            $requiereAprobacion,
            $estadoAprobacion,
        ];
    }

    private function determinarEstadoAprobacion(
        Usuario $usuario,
        Collection $especialidadesAprobacionIds
    ): string {
        if ($especialidadesAprobacionIds->isEmpty()) {
            return self::APROBACION_NO_REQUERIDA;
        }

        $profesional = $usuario->profesional;

        if (
            !$profesional ||
            $profesional->estado_aprobacion !== self::APROBACION_APROBADO
        ) {
            return self::APROBACION_PENDIENTE;
        }

        $especialidadesAprobadasActuales = $profesional
            ->especialidades
            ->where('requiere_aprobacion', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values();

        return $especialidadesAprobadasActuales->all() === $especialidadesAprobacionIds->all()
            ? self::APROBACION_APROBADO
            : self::APROBACION_PENDIENTE;
    }

    private function prepararFotoPerfil(
        Request $request,
        Usuario $usuario
    ): array {
        if (!$request->hasFile('foto_perfil')) {
            return [$usuario->foto_perfil, [], []];
        }

        $fotoNueva = $request
            ->file('foto_perfil')
            ->store('perfiles', 'public');

        $fotoAnterior = $usuario->foto_perfil
            ? [$usuario->foto_perfil]
            : [];

        return [$fotoNueva, [$fotoNueva], $fotoAnterior];
    }

    private function prepararPortafolio(
        Request $request,
        Usuario $usuario
    ): array {
        $fotosActuales = $this->normalizarPortafolio(
            $usuario->portafolio_fotos
        );

        if (!$request->hasFile('portafolio_fotos')) {
            return [$fotosActuales, [], []];
        }

        $fotosNuevas = [];

        try {
            foreach ($request->file('portafolio_fotos') as $foto) {
                $fotosNuevas[] = $foto->store('portafolios', 'public');
            }
        } catch (Throwable $exception) {
            $this->eliminarArchivos($fotosNuevas);

            throw $exception;
        }

        return [$fotosNuevas, $fotosNuevas, $fotosActuales];
    }

    private function normalizarPortafolio(mixed $portafolio): array
    {
        if (is_string($portafolio)) {
            $portafolio = json_decode($portafolio, true) ?? [];
        }

        if ($portafolio instanceof Collection) {
            $portafolio = $portafolio->all();
        }

        return is_array($portafolio) ? $portafolio : [];
    }

    private function actualizarUsuario(
        Usuario $usuario,
        array $datos,
        ?string $fotoPerfil,
        array $portafolioFotos
    ): void {
        $usuario->update([
            'nombre' => $datos['nombre'],
            'apellido_paterno' => $datos['apellido_paterno'],
            'apellido_materno' => $datos['apellido_materno'] ?? null,
            'telefono' => $datos['telefono'] ?? null,
            'ubicacion' => $datos['ubicacion'] ?? null,
            'descripcion' => $datos['descripcion_usuario'] ?? null,
            'foto_perfil' => $fotoPerfil,
            'portafolio_fotos' => $portafolioFotos,
        ]);
    }

    private function actualizarProfesional(
        Usuario $usuario,
        array $datos,
        Collection $especialidadesIds,
        Collection $especialidadesAprobacionIds,
        bool $requiereAprobacion,
        ?string $estadoAprobacion
    ): void {
        $profesional = Profesional::updateOrCreate(
            ['usuario_id' => $usuario->id],
            [
                'anios_experiencia' => $datos['anios_experiencia'],
                'descripcion' => $datos['descripcion_profesional'],
                'portafolio_url' => $datos['portafolio_url'] ?? null,
                'zona_trabajo' => $datos['zona_trabajo_profesional'],
                'estado_aprobacion' => $estadoAprobacion,
            ]
        );

        $profesional->profesiones()->sync([$datos['profesion_id']]);
        $profesional->especialidades()->sync($especialidadesIds->all());
        $profesional->servicios()->sync($datos['servicios'] ?? []);

        $this->sincronizarSolicitudAprobacion(
            $profesional,
            $especialidadesAprobacionIds,
            $requiereAprobacion,
            $estadoAprobacion
        );
    }

    private function sincronizarSolicitudAprobacion(
        Profesional $profesional,
        Collection $especialidadesAprobacionIds,
        bool $requiereAprobacion,
        ?string $estadoAprobacion
    ): void {
        if (
            $requiereAprobacion &&
            $estadoAprobacion === self::APROBACION_PENDIENTE
        ) {
            SolicitudAprobacionProfesional::updateOrCreate(
                [
                    'profesional_id' => $profesional->id,
                    'estado' => self::APROBACION_PENDIENTE,
                ],
                [
                    'especialidades_requieren_aprobacion' => $especialidadesAprobacionIds->all(),
                    'revisado_por' => null,
                    'motivo_rechazo' => null,
                    'revisado_at' => null,
                ]
            );

            return;
        }

        SolicitudAprobacionProfesional::query()
            ->where('profesional_id', $profesional->id)
            ->where('estado', self::APROBACION_PENDIENTE)
            ->delete();
    }

    private function actualizarProveedor(
        Usuario $usuario,
        array $datos
    ): void {
        $proveedor = Proveedor::updateOrCreate(
            ['usuario_id' => $usuario->id],
            [
                'descripcion' => $datos['descripcion_proveedor'] ?? null,
                'zona_trabajo' => $datos['zona_trabajo_proveedor'],
            ]
        );

        $proveedor->materiales()->sync($datos['materiales']);
    }

    private function eliminarArchivos(array $archivos): void
    {
        $archivos = collect($archivos)
            ->filter(fn ($archivo) => is_string($archivo) && $archivo !== '')
            ->unique()
            ->values()
            ->all();

        if ($archivos !== []) {
            Storage::disk('public')->delete($archivos);
        }
    }

    private function aplicarVisibilidadPublica(Builder $query): void
    {
        $query->where(function ($query) {
            $query
                ->whereHas(
                    'rol',
                    fn ($rol) =>
                        $rol->where('nombre', '!=', self::ROL_PROFESIONAL)
                )
                ->orWhere(function ($query) {
                    $query
                        ->whereHas(
                            'rol',
                            fn ($rol) =>
                                $rol->where('nombre', self::ROL_PROFESIONAL)
                        )
                        ->whereHas('profesional');
                });
        });
    }

    private function prepararEspecialidadesPublicas(Usuario $usuario): void
    {
        if (
            $usuario->rol->nombre !== self::ROL_PROFESIONAL ||
            !$usuario->profesional
        ) {
            return;
        }

        $profesional = $usuario->profesional;

        $profesional->setRelation(
            'especialidades',
            $profesional->especialidadesVisibles()
        );
    }

    private function aplicarBusqueda(
        Builder $query,
        string $busqueda
    ): void {
        $query->where(function ($query) use ($busqueda) {
            $query
                ->where('nombre', 'like', "%{$busqueda}%")
                ->orWhere('apellido_paterno', 'like', "%{$busqueda}%")
                ->orWhere('apellido_materno', 'like', "%{$busqueda}%")
                ->orWhere('ubicacion', 'like', "%{$busqueda}%")
                ->orWhere('descripcion', 'like', "%{$busqueda}%")
                ->orWhereHas(
                    'rol',
                    fn ($rol) =>
                        $rol->where('nombre', 'like', "%{$busqueda}%")
                )
                ->orWhereHas(
                    'profesional.profesiones',
                    fn ($profesion) =>
                        $profesion->where('nombre', 'like', "%{$busqueda}%")
                )
                ->orWhereHas(
                    'profesional',
                    function ($profesional) use ($busqueda) {
                        $profesional->where(function ($query) use ($busqueda) {
                            $query
                                ->whereHas(
                                    'especialidades',
                                    fn ($especialidad) =>
                                        $especialidad
                                            ->where(
                                                'especialidades.nombre',
                                                'like',
                                                "%{$busqueda}%"
                                            )
                                            ->where(
                                                'especialidades.requiere_aprobacion',
                                                false
                                            )
                                )
                                ->orWhere(function ($query) use ($busqueda) {
                                    $query
                                        ->where(
                                            'estado_aprobacion',
                                            self::APROBACION_APROBADO
                                        )
                                        ->whereHas(
                                            'especialidades',
                                            fn ($especialidad) =>
                                                $especialidad->where(
                                                    'especialidades.nombre',
                                                    'like',
                                                    "%{$busqueda}%"
                                                )
                                        );
                                });
                        });
                    }
                )
                ->orWhereHas(
                    'proveedor.materiales',
                    fn ($material) =>
                        $material->where('nombre', 'like', "%{$busqueda}%")
                );
        });
    }
}