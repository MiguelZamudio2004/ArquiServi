<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class AdminUsuarioController extends Controller
{
    private const ROLES = [
        'usuario' => 'Usuario',
        'profesional' => 'Profesional',
        'proveedor' => 'Proveedor',
    ];

    private const ESTADOS = [
        'activo' => 'Activo',
        'inactivo' => 'Inactivo',
        'suspendido' => 'Suspendido',
    ];

    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $rol = $request->query('rol');
        $estado = $request->query('estado');

        $query = Usuario::query()
            ->with('rol')
            ->whereHas('rol', fn (Builder $query) =>
                $query->whereIn('nombre', array_keys(self::ROLES))
            );

        if ($buscar !== '') {
            $this->aplicarBusqueda($query, $buscar);
        }

        if (is_string($rol) && isset(self::ROLES[$rol])) {
            $query->whereRelation('rol', 'nombre', $rol);
        }

        if (is_string($estado) && isset(self::ESTADOS[$estado])) {
            $query->where('estado', $estado);
        }

        $usuarios = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $estadisticas = $this->obtenerEstadisticas();

        return view('admin.usuarios.index', [
            'usuarios' => $usuarios,
            'buscar' => $buscar,
            'rol' => $rol,
            'estado' => $estado,
            'roles' => self::ROLES,
            'estados' => self::ESTADOS,
            'totalUsuarios' => (int) $estadisticas->total,
            'totalActivos' => (int) $estadisticas->activos,
            'totalInactivos' => (int) $estadisticas->inactivos,
            'totalSuspendidos' => (int) $estadisticas->suspendidos,
        ]);
    }

    public function mostrar(Usuario $usuario): View
    {
        $this->validarUsuarioAdministrable($usuario);

        $usuario->load([
            'rol',
            'profesional.profesiones',
            'profesional.especialidades',
            'profesional.servicios',
            'proveedor.materiales',
        ]);

        return view('admin.usuarios.mostrar', [
            'usuario' => $usuario,
            'estadosCuenta' => self::ESTADOS,
        ]);
    }

    public function actualizarEstado(Request $request, Usuario $usuario): RedirectResponse
    {
        $this->validarUsuarioAdministrable($usuario);

        $estadoNuevo = $request->validate(
            [
                'estado' => ['required', Rule::in(array_keys(self::ESTADOS))],
            ],
            [
                'estado.required' => 'Debes seleccionar un estado válido.',
                'estado.in' => 'El estado seleccionado no es válido.',
            ]
        )['estado'];

        if ($usuario->estado === $estadoNuevo) {
            return back()->with('success', 'La cuenta ya se encuentra en ese estado.');
        }

        $usuario->update(['estado' => $estadoNuevo]);

        return back()->with('success', 'El estado de la cuenta se actualizó correctamente.');
    }

    public function eliminar(Usuario $usuario): RedirectResponse
    {
        $this->validarUsuarioAdministrable($usuario);

        if (auth()->id() === $usuario->id) {
            return back()->withErrors([
                'eliminar' => 'No puedes eliminar tu propia cuenta desde el panel administrativo.',
            ]);
        }

        $archivos = $this->obtenerArchivosUsuario($usuario);

        try {
            DB::transaction(fn () => $usuario->delete());
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'eliminar' => 'No fue posible eliminar la cuenta. Verifica que no existan relaciones que impidan su eliminación.',
            ]);
        }

        if ($archivos !== []) {
            Storage::disk('public')->delete($archivos);
        }

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'La cuenta y su información asociada fueron eliminadas correctamente.');
    }

    private function aplicarBusqueda(Builder $query, string $buscar): void
    {
        $query->where(function (Builder $query) use ($buscar) {
            $query->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                ->orWhere('apellido_materno', 'like', "%{$buscar}%")
                ->orWhere('correo', 'like', "%{$buscar}%")
                ->orWhere('telefono', 'like', "%{$buscar}%");
        });
    }

    private function obtenerEstadisticas(): Usuario
    {
        return Usuario::query()
            ->join('roles', 'roles.id', '=', 'usuarios.rol_id')
            ->whereIn('roles.nombre', array_keys(self::ROLES))
            ->selectRaw('COUNT(usuarios.id) AS total')
            ->selectRaw("
                SUM(CASE WHEN usuarios.estado = 'activo' THEN 1 ELSE 0 END) AS activos
            ")
            ->selectRaw("
                SUM(CASE WHEN usuarios.estado = 'inactivo' THEN 1 ELSE 0 END) AS inactivos
            ")
            ->selectRaw("
                SUM(CASE WHEN usuarios.estado = 'suspendido' THEN 1 ELSE 0 END) AS suspendidos
            ")
            ->firstOrFail();
    }

    private function obtenerArchivosUsuario(Usuario $usuario): array
    {
        $portafolio = $usuario->portafolio_fotos ?? [];

        if (is_string($portafolio)) {
            $portafolio = json_decode($portafolio, true) ?? [];
        }

        if (!is_array($portafolio)) {
            $portafolio = [];
        }

        return collect([$usuario->foto_perfil, ...$portafolio])
            ->filter(fn ($archivo) => is_string($archivo) && $archivo !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function validarUsuarioAdministrable(Usuario $usuario): void
    {
        $usuario->loadMissing('rol');

        abort_if(!$usuario->rol, 404);
        abort_unless(isset(self::ROLES[$usuario->rol->nombre]), 403);
    }
}