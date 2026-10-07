@extends('admin.layout')

@section('titulo', 'Calificaciones')

@section('contenido')

<section class="admin-titulo-pagina">

    <span class="admin-mini-titulo">
        Administración
    </span>

    <h1>
        Gestión de calificaciones
    </h1>

    <p>
        Supervisa las evaluaciones realizadas entre
        clientes, profesionales y proveedores dentro
        de ArquiServi.
    </p>

</section>

<section class="admin-estadisticas">

    <article class="admin-estadistica">

        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-star"></i>
        </div>

        <div>

            <span>
                Total
            </span>

            <strong>
                {{ $totalCalificaciones }}
            </strong>

        </div>

    </article>

    <article class="admin-estadistica">

        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-chart-line"></i>
        </div>

        <div>

            <span>
                Promedio general
            </span>

            <strong>
                {{ number_format($promedioGeneral, 1) }}
            </strong>

        </div>

    </article>

    <article class="admin-estadistica">

        <div class="admin-estadistica-icono neutral">
            <i class="fa-solid fa-user-tie"></i>
        </div>

        <div>

            <span>
                A profesionales
            </span>

            <strong>
                {{ $totalProfesionales }}
            </strong>

        </div>

    </article>

    <article class="admin-estadistica">

        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-store"></i>
        </div>

        <div>

            <span>
                A proveedores
            </span>

            <strong>
                {{ $totalProveedores }}
            </strong>

        </div>

    </article>

</section>

<section class="admin-seccion">

    <form
        action="{{ route('admin.calificaciones.index') }}"
        method="GET"
        class="admin-filtros calificaciones-filtros"
    >

        <div class="admin-buscador">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                placeholder="Buscar calificación..."
            >

        </div>

        <select name="tipo">

            <option value="">
                Todos los tipos
            </option>

            @foreach($tipos as $valor => $texto)

                <option
                    value="{{ $valor }}"
                    {{ $tipo === $valor ? 'selected' : '' }}
                >
                    {{ $texto }}
                </option>

            @endforeach

        </select>

        <select name="promedio">

            <option value="">
                Cualquier puntuación
            </option>

            @foreach($promedios as $valor => $texto)

                <option
                    value="{{ $valor }}"
                    {{ $promedio === $valor ? 'selected' : '' }}
                >
                    {{ $texto }} estrellas
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

        @if($buscar !== '' || $tipo || $promedio)

            <a
                href="{{ route('admin.calificaciones.index') }}"
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
                    <th>Evaluado</th>
                    <th>Evaluador</th>
                    <th>Tipo</th>
                    <th>Calificación</th>
                    <th>Comentario</th>
                    <th>Solicitud</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>

            </thead>

            <tbody>

                @forelse($calificaciones as $calificacion)

                    @php
                        $promedioValor =
                            (float) $calificacion->promedio;
                    @endphp

                    <tr>

                        <td>

                            <div class="tabla-usuario">

                                <div class="tabla-avatar">

                                    @if($calificacion->evaluado->foto_perfil)

                                        <img
                                            src="{{ asset('storage/' . $calificacion->evaluado->foto_perfil) }}"
                                            alt="Foto de perfil"
                                        >

                                    @else

                                        <i class="fa-solid fa-user"></i>

                                    @endif

                                </div>

                                <div class="tabla-usuario-datos">

                                    <strong>
                                        {{ $calificacion->evaluado->nombre }}
                                        {{ $calificacion->evaluado->apellido_paterno }}
                                    </strong>

                                    <span>
                                        {{ $calificacion->evaluado->correo }}
                                    </span>

                                </div>

                            </div>

                        </td>

                        <td>

                            <div class="tabla-usuario">

                                <div class="tabla-avatar">

                                    @if($calificacion->evaluador->foto_perfil)

                                        <img
                                            src="{{ asset('storage/' . $calificacion->evaluador->foto_perfil) }}"
                                            alt="Foto de perfil"
                                        >

                                    @else

                                        <i class="fa-solid fa-user"></i>

                                    @endif

                                </div>

                                <div class="tabla-usuario-datos">

                                    <strong>
                                        {{ $calificacion->evaluador->nombre }}
                                        {{ $calificacion->evaluador->apellido_paterno }}
                                    </strong>

                                    <span>
                                        {{ ucfirst($calificacion->evaluador->rol->nombre) }}
                                    </span>

                                </div>

                            </div>

                        </td>

                        <td>

                            <span class="admin-etiqueta">
                                {{ $tipos[$calificacion->tipo_evaluado] ?? ucfirst($calificacion->tipo_evaluado) }}
                            </span>

                        </td>

                        <td>

                            <div class="calificacion-tabla-puntuacion">

                                <div class="calificacion-estrellas">

                                    @for($i = 1; $i <= 5; $i++)

                                        @if($promedioValor >= $i)

                                            <i class="fa-solid fa-star"></i>

                                        @elseif($promedioValor >= ($i - 0.5))

                                            <i class="fa-solid fa-star-half-stroke"></i>

                                        @else

                                            <i class="fa-regular fa-star"></i>

                                        @endif

                                    @endfor

                                </div>

                                <strong>
                                    {{ number_format($promedioValor, 1) }}
                                </strong>

                            </div>

                        </td>

                        <td>

                            @if($calificacion->comentario)

                                <span class="calificacion-comentario-tabla">
                                    {{
                                        \Illuminate\Support\Str::limit(
                                            $calificacion->comentario,
                                            65
                                        )
                                    }}
                                </span>

                            @else

                                <span class="texto-muted">
                                    Sin comentario
                                </span>

                            @endif

                        </td>

                        <td>

                            <a
                                href="{{ route('admin.solicitudes.mostrar', $calificacion->solicitud) }}"
                                class="calificacion-solicitud-enlace"
                            >
                                #{{ $calificacion->solicitud_id }}
                            </a>

                        </td>

                        <td>
                            {{ $calificacion->created_at->format('d/m/Y') }}
                        </td>

                        <td>

                            <a
                                href="{{ route('admin.calificaciones.mostrar', $calificacion) }}"
                                class="admin-accion-tabla"
                            >
                                Ver
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="admin-tabla-vacia"
                        >
                            No se encontraron calificaciones.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    @if($calificaciones->hasPages())

        <div class="admin-paginacion">

            @if($calificaciones->onFirstPage())

                <span class="admin-pagina-boton deshabilitado">
                    <i class="fa-solid fa-chevron-left"></i>
                </span>

            @else

                <a
                    href="{{ $calificaciones->previousPageUrl() }}"
                    class="admin-pagina-boton"
                >
                    <i class="fa-solid fa-chevron-left"></i>
                </a>

            @endif

            <span class="admin-pagina-texto">
                Página {{ $calificaciones->currentPage() }}
                de {{ $calificaciones->lastPage() }}
            </span>

            @if($calificaciones->hasMorePages())

                <a
                    href="{{ $calificaciones->nextPageUrl() }}"
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