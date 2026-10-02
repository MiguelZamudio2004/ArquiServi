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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    public function mostrar(
        Request $request,
        PerfilNotificacionService $perfilNotificacionService
    ) {
        $usuario = $request->user()->load(
            'rol',
            'proveedor.materiales',
            'profesional.profesiones',
            'profesional.especialidades',
            'profesional.servicios'
        );

        $perfilNotificacionService->sincronizar(
            $usuario
        );

        return view(
            'perfil',
            compact('usuario')
        );
    }

    public function editar(Request $request)
    {
        $usuario = $request->user()->load(
            'rol',
            'proveedor.materiales',
            'profesional.profesiones',
            'profesional.especialidades',
            'profesional.servicios'
        );

        $profesiones = collect();
        $materiales = collect();
        $servicios = collect();

        if ($usuario->rol->nombre === 'profesional') {
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

            $servicios = Servicio::where(
                'activo',
                true
            )
                ->orderBy('nombre')
                ->get();
        }

        if ($usuario->rol->nombre === 'proveedor') {
            $materiales = Material::where(
                'activo',
                true
            )
                ->orderBy('nombre')
                ->get();
        }

        return view(
            'perfil-editar',
            compact(
                'usuario',
                'profesiones',
                'materiales',
                'servicios'
            )
        );
    }

    public function actualizar(
        Request $request,
        PerfilNotificacionService $perfilNotificacionService
    ) {
        $usuario = $request->user()->load(
            'rol',
            'profesional.especialidades'
        );

        $reglas = [
            'nombre' => [
                'required',
                'string',
                'max:100'
            ],

            'apellido_paterno' => [
                'required',
                'string',
                'max:100'
            ],

            'apellido_materno' => [
                'nullable',
                'string',
                'max:100'
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20'
            ],

            'ubicacion' => [
                'nullable',
                'string',
                'max:200'
            ],

            'descripcion_usuario' => [
                'nullable',
                'string',
                'max:500'
            ],

            'foto_perfil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],
        ];

        if ($usuario->rol->nombre === 'profesional') {
            $reglas += [
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

                'descripcion_profesional' => [
                    'required',
                    'string',
                    'max:500'
                ],

                'portafolio_url' => [
                    'nullable',
                    'url',
                    'max:500'
                ],

                'zona_trabajo_profesional' => [
                    'required',
                    'string',
                    'max:200'
                ],

                'servicios' => [
                    'nullable',
                    'array'
                ],

                'servicios.*' => [
                    'integer',
                    'distinct',
                    'exists:servicios,id'
                ],

                'portafolio_fotos' => [
                    'nullable',
                    'array',
                    'max:3'
                ],

                'portafolio_fotos.*' => [
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:10240'
                ],
            ];
        }

        if ($usuario->rol->nombre === 'proveedor') {
            $reglas += [
                'descripcion_proveedor' => [
                    'nullable',
                    'string',
                    'max:500'
                ],

                'zona_trabajo_proveedor' => [
                    'required',
                    'string',
                    'max:200'
                ],

                'materiales' => [
                    'required',
                    'array',
                    'min:1'
                ],

                'materiales.*' => [
                    'integer',
                    'distinct',
                    'exists:materiales,id'
                ],

                'portafolio_fotos' => [
                    'nullable',
                    'array',
                    'max:3'
                ],

                'portafolio_fotos.*' => [
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:10240'
                ],
            ];
        }

        $datos = $request->validate(
            $reglas,
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

                'descripcion_profesional.required' =>
                    'Debes agregar una descripción profesional.',

                'descripcion_profesional.max' =>
                    'La descripción profesional no puede superar los 500 caracteres.',

                'portafolio_url.url' =>
                    'El enlace del portafolio no es válido.',

                'portafolio_url.max' =>
                    'El enlace del portafolio es demasiado largo.',

                'zona_trabajo_profesional.required' =>
                    'Debes indicar tu zona de trabajo.',

                'zona_trabajo_profesional.max' =>
                    'La zona de trabajo no puede superar los 200 caracteres.',

                'servicios.*.exists' =>
                    'Uno de los servicios seleccionados no es válido.',

                'servicios.*.distinct' =>
                    'No puedes seleccionar el mismo servicio más de una vez.',

                'materiales.required' =>
                    'Debes seleccionar al menos un material o producto.',

                'materiales.min' =>
                    'Debes seleccionar al menos un material o producto.',

                'materiales.*.exists' =>
                    'Uno de los materiales seleccionados no es válido.',

                'materiales.*.distinct' =>
                    'No puedes seleccionar el mismo material más de una vez.',

                'portafolio_fotos.max' =>
                    'Solo puedes subir un máximo de 3 fotos al portafolio.',

                'portafolio_fotos.*.image' =>
                    'Cada archivo del portafolio debe ser una imagen.',

                'portafolio_fotos.*.mimes' =>
                    'Las fotos del portafolio deben ser JPG, JPEG, PNG o WEBP.',

                'portafolio_fotos.*.max' =>
                    'Cada foto del portafolio puede pesar como máximo 10 MB.',

                'foto_perfil.image' =>
                    'La foto de perfil debe ser una imagen.',

                'foto_perfil.mimes' =>
                    'La foto de perfil debe ser JPG, JPEG, PNG o WEBP.',

                'foto_perfil.max' =>
                    'La foto de perfil puede pesar como máximo 2 MB.',
            ]
        );

        $especialidadesIds = collect();

        $especialidadesAprobacionIds = collect();

        $requiereAprobacion = false;

        $estadoAprobacion = null;

        if ($usuario->rol->nombre === 'profesional') {
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
                ->map(
                    fn ($id) => (int) $id
                )
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

            $especialidadesAprobacionIds =
                $especialidades
                    ->where(
                        'requiere_aprobacion',
                        true
                    )
                    ->pluck('id')
                    ->map(
                        fn ($id) => (int) $id
                    )
                    ->sort()
                    ->values();

            $requiereAprobacion =
                $especialidadesAprobacionIds
                    ->isNotEmpty();

            $profesionalActual =
                $usuario->profesional;

            $especialidadesAprobadasActuales =
                collect();

            if ($profesionalActual) {
                $especialidadesAprobadasActuales =
                    $profesionalActual
                        ->especialidades
                        ->where(
                            'requiere_aprobacion',
                            true
                        )
                        ->pluck('id')
                        ->map(
                            fn ($id) => (int) $id
                        )
                        ->sort()
                        ->values();
            }

            if (!$requiereAprobacion) {
                $estadoAprobacion =
                    'no_requerida';
            } elseif (
                $profesionalActual &&
                $profesionalActual
                    ->estado_aprobacion ===
                    'aprobado' &&
                $especialidadesAprobadasActuales
                    ->all() ===
                $especialidadesAprobacionIds
                    ->all()
            ) {
                $estadoAprobacion =
                    'aprobado';
            } else {
                $estadoAprobacion =
                    'pendiente';
            }
        }

        if ($request->hasFile('foto_perfil')) {
            if ($usuario->foto_perfil) {
                Storage::disk('public')->delete(
                    $usuario->foto_perfil
                );
            }

            $fotoPerfil = $request
                ->file('foto_perfil')
                ->store(
                    'perfiles',
                    'public'
                );
        } else {
            $fotoPerfil =
                $usuario->foto_perfil;
        }

        $portafolioFotos =
            $usuario->portafolio_fotos ?? [];

        if (is_string($portafolioFotos)) {
            $portafolioFotos = json_decode(
                $portafolioFotos,
                true
            ) ?? [];
        }

        if (
            $request->hasFile(
                'portafolio_fotos'
            )
        ) {
            foreach (
                $portafolioFotos
                as $fotoAnterior
            ) {
                Storage::disk('public')->delete(
                    $fotoAnterior
                );
            }

            $portafolioFotos = [];

            foreach (
                $request->file(
                    'portafolio_fotos'
                )
                as $foto
            ) {
                $portafolioFotos[] =
                    $foto->store(
                        'portafolios',
                        'public'
                    );
            }
        }

        DB::transaction(
            function () use (
                $usuario,
                $datos,
                $fotoPerfil,
                $portafolioFotos,
                $especialidadesIds,
                $especialidadesAprobacionIds,
                $requiereAprobacion,
                $estadoAprobacion
            ) {
                $usuario->update([
                    'nombre' =>
                        $datos['nombre'],

                    'apellido_paterno' =>
                        $datos['apellido_paterno'],

                    'apellido_materno' =>
                        $datos['apellido_materno']
                        ?? null,

                    'telefono' =>
                        $datos['telefono']
                        ?? null,

                    'ubicacion' =>
                        $datos['ubicacion']
                        ?? null,

                    'descripcion' =>
                        $datos['descripcion_usuario']
                        ?? null,

                    'foto_perfil' =>
                        $fotoPerfil,

                    'portafolio_fotos' =>
                        $portafolioFotos,
                ]);

                if (
                    $usuario->rol->nombre ===
                    'profesional'
                ) {
                    $profesional =
                        Profesional::updateOrCreate(
                            [
                                'usuario_id' =>
                                    $usuario->id
                            ],
                            [
                                'anios_experiencia' =>
                                    $datos[
                                        'anios_experiencia'
                                    ],

                                'descripcion' =>
                                    $datos[
                                        'descripcion_profesional'
                                    ],

                                'portafolio_url' =>
                                    $datos[
                                        'portafolio_url'
                                    ] ?? null,

                                'zona_trabajo' =>
                                    $datos[
                                        'zona_trabajo_profesional'
                                    ],

                                'estado_aprobacion' =>
                                    $estadoAprobacion,
                            ]
                        );

                    $profesional
                        ->profesiones()
                        ->sync([
                            $datos[
                                'profesion_id'
                            ]
                        ]);

                    $profesional
                        ->especialidades()
                        ->sync(
                            $especialidadesIds->all()
                        );

                    $profesional
                        ->servicios()
                        ->sync(
                            $datos['servicios']
                            ?? []
                        );

                    if (
                        $requiereAprobacion &&
                        $estadoAprobacion ===
                        'pendiente'
                    ) {
                        SolicitudAprobacionProfesional::updateOrCreate(
                            [
                                'profesional_id' =>
                                    $profesional->id,

                                'estado' =>
                                    'pendiente'
                            ],
                            [
                                'especialidades_requieren_aprobacion' =>
                                    $especialidadesAprobacionIds
                                        ->all(),

                                'revisado_por' =>
                                    null,

                                'motivo_rechazo' =>
                                    null,

                                'revisado_at' =>
                                    null,
                            ]
                        );
                    } else {
                        SolicitudAprobacionProfesional::where(
                            'profesional_id',
                            $profesional->id
                        )
                            ->where(
                                'estado',
                                'pendiente'
                            )
                            ->delete();
                    }
                }

                if (
                    $usuario->rol->nombre ===
                    'proveedor'
                ) {
                    $proveedor =
                        Proveedor::updateOrCreate(
                            [
                                'usuario_id' =>
                                    $usuario->id
                            ],
                            [
                                'descripcion' =>
                                    $datos[
                                        'descripcion_proveedor'
                                    ] ?? null,

                                'zona_trabajo' =>
                                    $datos[
                                        'zona_trabajo_proveedor'
                                    ],
                            ]
                        );

                    $proveedor
                        ->materiales()
                        ->sync(
                            $datos['materiales']
                        );
                }
            }
        );

        $usuario->refresh();

        $usuario->load(
            'rol',
            'profesional.servicios',
            'proveedor.materiales'
        );

        $perfilNotificacionService
            ->sincronizar(
                $usuario
            );

        $usuario->notify(
            new PerfilActualizado()
        );

        if (
            $usuario->rol->nombre ===
            'profesional' &&
            $estadoAprobacion ===
            'pendiente'
        ) {
            return redirect()
                ->route('perfil')
                ->with(
                    'success',
                    'Perfil actualizado. Tu perfil profesional permanecerá pendiente hasta que un administrador revise y apruebe la especialidad seleccionada.'
                );
        }

        return redirect()
            ->route('perfil')
            ->with(
                'success',
                'Perfil actualizado correctamente.'
            );
    }

    public function buscar(Request $request)
    {
        $busqueda = trim(
            $request->input(
                'buscar',
                ''
            )
        );

        $tipo = $request->input(
            'tipo',
            ''
        );

        $profesionId =
            $request->input(
                'profesion_id'
            );

        $especialidadId =
            $request->input(
                'especialidad_id'
            );

        $profesiones = Profesion::where(
            'activo',
            true
        )
            ->with([
                'especialidades' =>
                    function ($query) {
                        $query
                            ->where(
                                'activo',
                                true
                            )
                            ->orderBy(
                                'nombre'
                            );
                    }
            ])
            ->orderBy('nombre')
            ->get();

        $usuarios = Usuario::with(
            'rol',
            'profesional.profesiones',
            'profesional.especialidades',
            'proveedor.materiales',
            'profesional.servicios'
        )
            ->where(
                'estado',
                'activo'
            )
            ->whereHas(
                'rol',
                function ($query) {
                    $query->where(
                        'nombre',
                        '!=',
                        'administrador'
                    );
                }
            )
            ->where(
                function ($query) {
                    $query
                        ->whereHas(
                            'rol',
                            function ($rol) {
                                $rol->where(
                                    'nombre',
                                    '!=',
                                    'profesional'
                                );
                            }
                        )
                        ->orWhere(
                            function ($profesionalQuery) {
                                $profesionalQuery
                                    ->whereHas(
                                        'rol',
                                        function ($rol) {
                                            $rol->where(
                                                'nombre',
                                                'profesional'
                                            );
                                        }
                                    )
                                    ->whereHas(
                                        'profesional',
                                        function ($profesional) {
                                            $profesional
                                                ->whereIn(
                                                    'estado_aprobacion',
                                                    [
                                                        'no_requerida',
                                                        'aprobado'
                                                    ]
                                                );
                                        }
                                    );
                            }
                        );
                }
            )
            ->when(
                $busqueda,
                function ($query) use (
                    $busqueda
                ) {
                    $query->where(
                        function ($q) use (
                            $busqueda
                        ) {
                            $q
                                ->where(
                                    'nombre',
                                    'like',
                                    "%{$busqueda}%"
                                )
                                ->orWhere(
                                    'apellido_paterno',
                                    'like',
                                    "%{$busqueda}%"
                                )
                                ->orWhere(
                                    'apellido_materno',
                                    'like',
                                    "%{$busqueda}%"
                                )
                                ->orWhere(
                                    'ubicacion',
                                    'like',
                                    "%{$busqueda}%"
                                )
                                ->orWhere(
                                    'descripcion',
                                    'like',
                                    "%{$busqueda}%"
                                )
                                ->orWhereHas(
                                    'rol',
                                    function ($rol) use (
                                        $busqueda
                                    ) {
                                        $rol->where(
                                            'nombre',
                                            'like',
                                            "%{$busqueda}%"
                                        );
                                    }
                                )
                                ->orWhereHas(
                                    'profesional.profesiones',
                                    function ($profesion) use (
                                        $busqueda
                                    ) {
                                        $profesion->where(
                                            'nombre',
                                            'like',
                                            "%{$busqueda}%"
                                        );
                                    }
                                )
                                ->orWhereHas(
                                    'profesional.especialidades',
                                    function ($especialidad) use (
                                        $busqueda
                                    ) {
                                        $especialidad->where(
                                            'nombre',
                                            'like',
                                            "%{$busqueda}%"
                                        );
                                    }
                                )
                                ->orWhereHas(
                                    'proveedor.materiales',
                                    function ($material) use (
                                        $busqueda
                                    ) {
                                        $material->where(
                                            'nombre',
                                            'like',
                                            "%{$busqueda}%"
                                        );
                                    }
                                );
                        }
                    );
                }
            )
            ->when(
                $tipo,
                function ($query) use (
                    $tipo
                ) {
                    $query->whereHas(
                        'rol',
                        function ($rol) use (
                            $tipo
                        ) {
                            $rol->where(
                                'nombre',
                                $tipo
                            );
                        }
                    );
                }
            )
            ->when(
                $profesionId,
                function ($query) use (
                    $profesionId
                ) {
                    $query->whereHas(
                        'profesional.profesiones',
                        function ($profesion) use (
                            $profesionId
                        ) {
                            $profesion->where(
                                'profesiones.id',
                                $profesionId
                            );
                        }
                    );
                }
            )
            ->when(
                $especialidadId,
                function ($query) use (
                    $especialidadId
                ) {
                    $query->whereHas(
                        'profesional.especialidades',
                        function ($especialidad) use (
                            $especialidadId
                        ) {
                            $especialidad->where(
                                'especialidades.id',
                                $especialidadId
                            );
                        }
                    );
                }
            )
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        return view(
            'usuarios',
            compact(
                'usuarios',
                'busqueda',
                'tipo',
                'profesionId',
                'especialidadId',
                'profesiones'
            )
        );
    }

    public function publico(Usuario $usuario)
    {
        if (
            $usuario->estado !==
            'activo'
        ) {
            abort(404);
        }

        $usuario->load(
            'rol',
            'proveedor.materiales',
            'profesional.profesiones',
            'profesional.especialidades',
            'profesional.servicios'
        );

        if (
            $usuario->rol->nombre ===
            'profesional'
        ) {
            if (!$usuario->profesional) {
                abort(404);
            }

            if (
                !in_array(
                    $usuario
                        ->profesional
                        ->estado_aprobacion,
                    [
                        'no_requerida',
                        'aprobado'
                    ],
                    true
                )
            ) {
                abort(404);
            }
        }

        return view(
            'perfil-publico',
            compact('usuario')
        );
    }
}