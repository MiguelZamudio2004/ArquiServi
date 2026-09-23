<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Material;
use App\Models\Profesional;
use App\Models\Profesion;
use App\Models\Proveedor;
use App\Models\Usuario;
use App\Notifications\PerfilActualizado;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    public function mostrar(Request $request)
    {
        $usuario = $request->user()->load(
            'rol',
            'proveedor.materiales',
            'profesional.profesiones',
            'profesional.especialidades',
            'profesional.servicios'
        );

        return view('perfil', compact('usuario'));
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
        $profesiones = Profesion::where('activo', true)
            ->with(['especialidades' => function ($query) {
                $query->where('activo', true)->orderBy('nombre');
            }])
            ->orderBy('nombre')
            ->get();

        $servicios = Servicio::where('activo', true)->orderBy('nombre')->get();
    }

    if ($usuario->rol->nombre === 'proveedor') {
        $materiales = Material::where('activo', true)->orderBy('nombre')->get();
    }

    return view('perfil-editar', compact('usuario', 'profesiones', 'materiales', 'servicios'));
}

    public function actualizar(Request $request)
    {
        $usuario = $request->user()->load('rol');

        $reglas = [
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'ubicacion' => 'nullable|string|max:200',
            'descripcion_usuario' => 'nullable|string|max:500',
            'foto_perfil' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        if ($usuario->rol->nombre === 'profesional') {
            $reglas += [
                'profesion_id' => 'required|exists:profesiones,id',
                'especialidad_id' => 'required|exists:especialidades,id',
                'anios_experiencia' => 'required|integer|min:0|max:80',
                'descripcion_profesional' => 'required|string|max:500',
                'portafolio_url' => 'nullable|url|max:500',
                'zona_trabajo_profesional' => 'required|string|max:200',
                'servicios' => 'nullable|array',
                'servicios.*' => 'integer|distinct|exists:servicios,id',
            ];
        }

        if ($usuario->rol->nombre === 'proveedor') {
            $reglas += [
                'descripcion_proveedor' => 'nullable|string|max:500',
                'zona_trabajo_proveedor' => 'required|string|max:200',
                'materiales' => 'required|array|min:1',
                'materiales.*' => 'integer|exists:materiales,id',
            ];
        }

        $datos = $request->validate($reglas);

        if ($usuario->rol->nombre === 'profesional') {
            $especialidadValida = Especialidad::where('id', $datos['especialidad_id'])
                ->where('profesion_id', $datos['profesion_id'])
                ->where('activo', true)
                ->exists();

            if (!$especialidadValida) {
                return back()->withErrors([
                    'especialidad_id' => 'La especialidad no corresponde a la profesión seleccionada.'
                ])->withInput();
            }
        }

        if ($request->hasFile('foto_perfil')) {
            if ($usuario->foto_perfil) {
                Storage::disk('public')->delete($usuario->foto_perfil);
            }

            $fotoPerfil = $request->file('foto_perfil')->store('perfiles', 'public');
        } else {
            $fotoPerfil = $usuario->foto_perfil;
        }

        DB::transaction(function () use ($usuario, $datos, $fotoPerfil) {
            $usuario->update([
                'nombre' => $datos['nombre'],
                'apellido_paterno' => $datos['apellido_paterno'],
                'apellido_materno' => $datos['apellido_materno'] ?? null,
                'telefono' => $datos['telefono'] ?? null,
                'ubicacion' => $datos['ubicacion'] ?? null,
                'descripcion' => $datos['descripcion_usuario'] ?? null,
                'foto_perfil' => $fotoPerfil,
            ]);

            if ($usuario->rol->nombre === 'profesional') {
                $profesional = Profesional::updateOrCreate(
                    ['usuario_id' => $usuario->id],
                    [
                        'anios_experiencia' => $datos['anios_experiencia'],
                        'descripcion' => $datos['descripcion_profesional'],
                        'portafolio_url' => $datos['portafolio_url'] ?? null,
                        'zona_trabajo' => $datos['zona_trabajo_profesional'],
                    ]
                );

                $profesional->profesiones()->sync([$datos['profesion_id']]);
                $profesional->especialidades()->sync([$datos['especialidad_id']]);
                $profesional->servicios()->sync($datos['servicios'] ?? []);
            }

            if ($usuario->rol->nombre === 'proveedor') {
                $proveedor = Proveedor::updateOrCreate(
                    ['usuario_id' => $usuario->id],
                    [
                        'descripcion' => $datos['descripcion_proveedor'] ?? null,
                        'zona_trabajo' => $datos['zona_trabajo_proveedor'],
                    ]
                );

                $proveedor->materiales()->sync($datos['materiales']);
            }
        });

        $usuario->notify(new PerfilActualizado());

        return redirect()->route('perfil')->with('success', 'Perfil actualizado correctamente.');
    }

    public function buscar(Request $request)
{
    $busqueda = trim($request->input('buscar', ''));
    $tipo = $request->input('tipo', '');
    $profesionId = $request->input('profesion_id');
    $especialidadId = $request->input('especialidad_id');

    $profesiones = Profesion::where('activo', true)
        ->with(['especialidades' => function ($query) {
            $query->where('activo', true)->orderBy('nombre');
        }])
        ->orderBy('nombre')
        ->get();

    $usuarios = Usuario::with(
        'rol',
        'profesional.profesiones',
        'profesional.especialidades',
        'proveedor.materiales',
        'profesional.servicios'
    )
        ->where('estado', 'activo')
        ->whereHas('rol', function ($query) {
            $query->where('nombre', '!=', 'administrador');
        })
        ->when($busqueda, function ($query) use ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                    ->orWhere('apellido_paterno', 'like', "%{$busqueda}%")
                    ->orWhere('apellido_materno', 'like', "%{$busqueda}%")
                    ->orWhere('ubicacion', 'like', "%{$busqueda}%")
                    ->orWhere('descripcion', 'like', "%{$busqueda}%")
                    ->orWhereHas('rol', function ($rol) use ($busqueda) {
                        $rol->where('nombre', 'like', "%{$busqueda}%");
                    })
                    ->orWhereHas('profesional.profesiones', function ($profesion) use ($busqueda) {
                        $profesion->where('nombre', 'like', "%{$busqueda}%");
                    })
                    ->orWhereHas('profesional.especialidades', function ($especialidad) use ($busqueda) {
                        $especialidad->where('nombre', 'like', "%{$busqueda}%");
                    })
                    ->orWhereHas('proveedor.materiales', function ($material) use ($busqueda) {
                        $material->where('nombre', 'like', "%{$busqueda}%");
                    });
            });
        })
        ->when($tipo, function ($query) use ($tipo) {
            $query->whereHas('rol', function ($rol) use ($tipo) {
                $rol->where('nombre', $tipo);
            });
        })
        ->when($profesionId, function ($query) use ($profesionId) {
            $query->whereHas('profesional.profesiones', function ($profesion) use ($profesionId) {
                $profesion->where('profesiones.id', $profesionId);
            });
        })
        ->when($especialidadId, function ($query) use ($especialidadId) {
            $query->whereHas('profesional.especialidades', function ($especialidad) use ($especialidadId) {
                $especialidad->where('especialidades.id', $especialidadId);
            });
        })
        ->orderBy('nombre')
        ->paginate(12)
        ->withQueryString();

    return view('usuarios', compact(
        'usuarios',
        'busqueda',
        'tipo',
        'profesionId',
        'especialidadId',
        'profesiones'
    ));
}

public function publico(Usuario $usuario)
{
    if ($usuario->estado !== 'activo') abort(404);

    $usuario->load(
        'rol',
        'proveedor.materiales',
        'profesional.profesiones',
        'profesional.especialidades',
        'profesional.servicios'
    );

    return view('perfil-publico', compact('usuario'));
}
}