@extends('admin.layout')

@section('titulo', 'Aprobaciones')

@section('contenido')
<section class="admin-titulo-pagina">
    <span class="admin-mini-titulo">
        Administración
    </span>

    <h1>
        Aprobaciones profesionales
    </h1>

    <p>
        Revisa las solicitudes de profesionales cuyas
        especialidades requieren validación administrativa.
    </p>
</section>

<section class="admin-estadisticas">
    <article class="admin-estadistica">
        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-clipboard-list"></i>
        </div>

        <div>
            <span>
                Total
            </span>

            <strong>
                {{ $totalSolicitudes }}
            </strong>
        </div>
    </article>

    <article class="admin-estadistica">
        <div class="admin-estadistica-icono neutral">
            <i class="fa-solid fa-clock"></i>
        </div>

        <div>
            <span>
                Pendientes
            </span>

            <strong>
                {{ $totalPendientes }}
            </strong>
        </div>
    </article>

    <article class="admin-estadistica">
        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <div>
            <span>
                Aprobadas
            </span>

            <strong>
                {{ $totalAprobadas }}
            </strong>
        </div>
    </article>

    <article class="admin-estadistica">
        <div class="admin-estadistica-icono alerta">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>

        <div>
            <span>
                Rechazadas
            </span>

            <strong>
                {{ $totalRechazadas }}
            </strong>
        </div>
    </article>
</section>

<section class="admin-seccion">
    <form
        action="{{ route('admin.aprobaciones.index') }}"
        method="GET"
        class="admin-filtros aprobaciones-filtros"
    >
        <div class="admin-buscador">
            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                placeholder="Buscar profesional..."
            >
        </div>

        <select name="estado">
            <option value="">
                Todos los estados
            </option>

            @foreach($estados as $valor => $texto)
                <option
                    value="{{ $valor }}"
                    {{ $estado === $valor ? 'selected' : '' }}
                >
                    {{ $texto }}
                </option>
            @endforeach
        </select>

        <button
            type="submit"
            class="admin-boton admin-boton-principal"
        >
            <i class="fa-solid fa-filter"></i>
            Buscar
        </button>

        @if($buscar !== '' || $estado)
            <a
                href="{{ route('admin.aprobaciones.index') }}"
                class="admin-boton admin-boton-secundario"
            >
                Limpiar
            </a>
        @endif
    </form>

    <div class="admin-tabla-contenedor">
        <table class="admin-tabla">
            <thead>
                <tr>
                    <th>
                        Profesional
                    </th>

                    <th>
                        Profesión
                    </th>

                    <th>
                        Especialidades a revisar
                    </th>

                    <th>
                        Estado
                    </th>

                    <th>
                        Solicitud
                    </th>

                    <th>
                        Revisada
                    </th>

                    <th>
                        Acción
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse($solicitudes as $solicitud)
                    @php
                        $profesional = $solicitud->profesional;
                        $usuario = $profesional->usuario;

                        $idsRevision = collect(
                            $solicitud->especialidades_requieren_aprobacion ?? []
                        )->map(fn ($id) => (int) $id);

                        $especialidadesRevision = $profesional
                            ->especialidades
                            ->whereIn('id', $idsRevision);
                    @endphp

                    <tr>
                        <td>
                            <div class="tabla-usuario">
                                <div class="tabla-avatar">
                                    @if($usuario->foto_perfil)
                                        <img
                                            src="{{ asset('storage/' . $usuario->foto_perfil) }}"
                                            alt="Foto de perfil"
                                        >
                                    @else
                                        <i class="fa-solid fa-user-tie"></i>
                                    @endif
                                </div>

                                <div class="tabla-usuario-datos">
                                    <strong>
                                        {{ $usuario->nombre }}
                                        {{ $usuario->apellido_paterno }}
                                    </strong>

                                    <span>
                                        {{ $usuario->correo }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td>
                            @if($profesional->profesiones->isNotEmpty())
                                {{
                                    $profesional
                                        ->profesiones
                                        ->pluck('nombre')
                                        ->join(', ')
                                }}
                            @else
                                <span class="texto-muted">
                                    Sin profesión
                                </span>
                            @endif
                        </td>

                        <td>
                            <div class="aprobacion-especialidades-tabla">
                                @forelse($especialidadesRevision as $especialidad)
                                    <span class="admin-etiqueta">
                                        {{ $especialidad->nombre }}
                                    </span>
                                @empty
                                    <span class="texto-muted">
                                        Sin información
                                    </span>
                                @endforelse
                            </div>
                        </td>

                        <td>
                            <span class="admin-estado aprobacion-estado-{{ $solicitud->estado }}">
                                @switch($solicitud->estado)
                                    @case('pendiente')
                                        Pendiente
                                        @break

                                    @case('aprobada')
                                        Aprobada
                                        @break

                                    @case('rechazada')
                                        Rechazada
                                        @break

                                    @default
                                        Sin estado
                                @endswitch
                            </span>
                        </td>

                        <td>
                            {{
                                $solicitud
                                    ->created_at
                                    ->format('d/m/Y')
                            }}
                        </td>

                        <td>
                            @if($solicitud->revisado_at)
                                {{
                                    $solicitud
                                        ->revisado_at
                                        ->format('d/m/Y')
                                }}
                            @else
                                <span class="texto-muted">
                                    Pendiente
                                </span>
                            @endif
                        </td>

                        <td>
                            <a
                                href="{{ route('admin.aprobaciones.mostrar', $solicitud) }}"
                                class="admin-accion-tabla"
                            >
                                Revisar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="7"
                            class="admin-tabla-vacia"
                        >
                            No se encontraron solicitudes de aprobación.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($solicitudes->hasPages())
        <div class="admin-paginacion">
            @if($solicitudes->onFirstPage())
                <span class="admin-pagina-boton deshabilitado">
                    <i class="fa-solid fa-chevron-left"></i>
                </span>
            @else
                <a
                    href="{{ $solicitudes->previousPageUrl() }}"
                    class="admin-pagina-boton"
                >
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            <span class="admin-pagina-texto">
                Página {{ $solicitudes->currentPage() }}
                de {{ $solicitudes->lastPage() }}
            </span>

            @if($solicitudes->hasMorePages())
                <a
                    href="{{ $solicitudes->nextPageUrl() }}"
                    class="admin-pagina-boton"
                >
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="admin-pagina-boton deshabilitado">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
            @endif
        </div>
    @endif
</section>
@endsection