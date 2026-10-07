@extends('admin.layout')

@section('titulo', 'Profesionales')

@section('contenido')

<section class="admin-titulo-pagina">

    <span class="admin-mini-titulo">
        Administración
    </span>

    <h1>
        Gestión de profesionales
    </h1>

    <p>
        Consulta los profesionales registrados,
        su profesión, estado de aprobación y
        disponibilidad dentro de ArquiServi.
    </p>

</section>

<section class="admin-estadisticas">

    <article class="admin-estadistica">

        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-user-tie"></i>
        </div>

        <div>
            <span>Total</span>

            <strong>
                {{ $totalProfesionales }}
            </strong>
        </div>

    </article>

    <article class="admin-estadistica">

        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-eye"></i>
        </div>

        <div>
            <span>
                Disponibles públicamente
            </span>

            <strong>
                {{ $totalPublicos }}
            </strong>
        </div>

    </article>

    <article class="admin-estadistica">

        <div class="admin-estadistica-icono neutral">
            <i class="fa-solid fa-clock"></i>
        </div>

        <div>
            <span>Pendientes</span>

            <strong>
                {{ $totalPendientes }}
            </strong>
        </div>

    </article>

    <article class="admin-estadistica">

        <div class="admin-estadistica-icono alerta">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>

        <div>
            <span>Rechazados</span>

            <strong>
                {{ $totalRechazados }}
            </strong>
        </div>

    </article>

</section>

<section class="admin-seccion">

    <form
        action="{{ route('admin.profesionales.index') }}"
        method="GET"
        class="admin-filtros profesionales-filtros"
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

        <select name="profesion">

            <option value="">
                Todas las profesiones
            </option>

            @foreach($profesiones as $profesion)

                <option
                    value="{{ $profesion->id }}"
                    {{ (string) $profesionId === (string) $profesion->id ? 'selected' : '' }}
                >
                    {{ $profesion->nombre }}
                </option>

            @endforeach

        </select>

        <select name="estado_aprobacion">

            <option value="">
                Todas las aprobaciones
            </option>

            @foreach($estadosAprobacion as $valor => $texto)

                <option
                    value="{{ $valor }}"
                    {{ $estadoAprobacion === $valor ? 'selected' : '' }}
                >
                    {{ $texto }}
                </option>

            @endforeach

        </select>

        <select name="estado_cuenta">

            <option value="">
                Todas las cuentas
            </option>

            @foreach($estadosCuenta as $valor => $texto)

                <option
                    value="{{ $valor }}"
                    {{ $estadoCuenta === $valor ? 'selected' : '' }}
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

        @if(
            $buscar !== '' ||
            $profesionId ||
            $estadoAprobacion ||
            $estadoCuenta
        )

            <a
                href="{{ route('admin.profesionales.index') }}"
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
                    <th>Profesional</th>
                    <th>Profesión</th>
                    <th>Experiencia</th>
                    <th>Aprobación</th>
                    <th>Cuenta</th>
                    <th>Registro</th>
                    <th>Acción</th>
                </tr>

            </thead>

            <tbody>

                @forelse($profesionales as $profesional)

                    <tr>

                        <td>

                            <div class="tabla-usuario">

                                <div class="tabla-avatar">

                                    @if($profesional->usuario->foto_perfil)

                                        <img
                                            src="{{ asset('storage/' . $profesional->usuario->foto_perfil) }}"
                                            alt="Foto de perfil"
                                        >

                                    @else

                                        <i class="fa-solid fa-user-tie"></i>

                                    @endif

                                </div>

                                <div class="tabla-usuario-datos">

                                    <strong>
                                        {{ $profesional->usuario->nombre }}
                                        {{ $profesional->usuario->apellido_paterno }}
                                    </strong>

                                    <span>
                                        {{ $profesional->usuario->correo }}
                                    </span>

                                </div>

                            </div>

                        </td>

                        <td>

                            @if($profesional->profesiones->isNotEmpty())

                                {{ $profesional->profesiones->pluck('nombre')->join(', ') }}

                            @else

                                <span class="texto-muted">
                                    Sin profesión
                                </span>

                            @endif

                        </td>

                        <td>

                            {{ $profesional->anios_experiencia }}

                            {{ $profesional->anios_experiencia === 1 ? 'año' : 'años' }}

                        </td>

                        <td>

                            <span class="admin-estado estado-aprobacion-{{ $profesional->estado_aprobacion }}">

                                {{ $estadosAprobacion[$profesional->estado_aprobacion] ?? 'Sin estado' }}

                            </span>

                        </td>

                        <td>

                            <span class="admin-estado estado-{{ $profesional->usuario->estado }}">

                                {{ ucfirst($profesional->usuario->estado) }}

                            </span>

                        </td>

                        <td>
                            {{ $profesional->created_at->format('d/m/Y') }}
                        </td>

                        <td>

                            <a
                                href="{{ route('admin.profesionales.mostrar', $profesional) }}"
                                class="admin-accion-tabla"
                            >
                                Ver
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="admin-tabla-vacia"
                        >
                            No se encontraron profesionales.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    @if($profesionales->hasPages())

        <div class="admin-paginacion">

            @if($profesionales->onFirstPage())

                <span class="admin-pagina-boton deshabilitado">

                    <i class="fa-solid fa-chevron-left"></i>

                </span>

            @else

                <a
                    href="{{ $profesionales->previousPageUrl() }}"
                    class="admin-pagina-boton"
                >

                    <i class="fa-solid fa-chevron-left"></i>

                </a>

            @endif

            <span class="admin-pagina-texto">

                Página {{ $profesionales->currentPage() }}
                de {{ $profesionales->lastPage() }}

            </span>

            @if($profesionales->hasMorePages())

                <a
                    href="{{ $profesionales->nextPageUrl() }}"
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