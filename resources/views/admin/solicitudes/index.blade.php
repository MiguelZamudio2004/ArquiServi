@extends('admin.layout')

@section('titulo', 'Solicitudes')

@section('contenido')
<section class="admin-titulo-pagina">
    <span class="admin-mini-titulo">
        Administración
    </span>

    <h1>
        Gestión de solicitudes
    </h1>

    <p>
        Supervisa las solicitudes realizadas entre
        usuarios, profesionales y proveedores dentro
        de ArquiServi.
    </p>
</section>

<section class="admin-estadisticas">
    <article class="admin-estadistica">
        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-envelope-open-text"></i>
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
            <i class="fa-solid fa-handshake"></i>
        </div>

        <div>
            <span>
                Aceptadas
            </span>

            <strong>
                {{ $totalAceptadas }}
            </strong>
        </div>
    </article>

    <article class="admin-estadistica">
        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <div>
            <span>
                Terminadas
            </span>

            <strong>
                {{ $totalTerminadas }}
            </strong>
        </div>
    </article>
</section>

<section class="admin-seccion">
    <form
        action="{{ route('admin.solicitudes.index') }}"
        method="GET"
        class="admin-filtros solicitudes-admin-filtros"
    >
        <div class="admin-buscador">
            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                placeholder="Buscar solicitud..."
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

        @if($buscar !== '' || $tipo || $estado)
            <a
                href="{{ route('admin.solicitudes.index') }}"
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
                        Solicitud
                    </th>

                    <th>
                        Solicitante
                    </th>

                    <th>
                        Destinatario
                    </th>

                    <th>
                        Tipo
                    </th>

                    <th>
                        Servicio / Material
                    </th>

                    <th>
                        Estado
                    </th>

                    <th>
                        Fecha
                    </th>

                    <th>
                        Acción
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse($solicitudes as $solicitud)
                    @php
                        $rolDestinatario =
                            $solicitud->destinatario->rol->nombre;

                        $esProveedor =
                            $rolDestinatario === 'proveedor';
                    @endphp

                    <tr>
                        <td>
                            <strong class="solicitud-admin-id">
                                #{{ $solicitud->id }}
                            </strong>
                        </td>

                        <td>
                            <div class="tabla-usuario">
                                <div class="tabla-avatar">
                                    @if($solicitud->solicitante->foto_perfil)
                                        <img
                                            src="{{ asset('storage/' . $solicitud->solicitante->foto_perfil) }}"
                                            alt="Foto de perfil"
                                        >
                                    @else
                                        <i class="fa-solid fa-user"></i>
                                    @endif
                                </div>

                                <div class="tabla-usuario-datos">
                                    <strong>
                                        {{ $solicitud->solicitante->nombre }}
                                        {{ $solicitud->solicitante->apellido_paterno }}
                                    </strong>

                                    <span>
                                        {{ $solicitud->solicitante->correo }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="tabla-usuario">
                                <div class="tabla-avatar">
                                    @if($solicitud->destinatario->foto_perfil)
                                        <img
                                            src="{{ asset('storage/' . $solicitud->destinatario->foto_perfil) }}"
                                            alt="Foto de perfil"
                                        >
                                    @else
                                        @if($esProveedor)
                                            <i class="fa-solid fa-store"></i>
                                        @else
                                            <i class="fa-solid fa-user-tie"></i>
                                        @endif
                                    @endif
                                </div>

                                <div class="tabla-usuario-datos">
                                    <strong>
                                        {{ $solicitud->destinatario->nombre }}
                                        {{ $solicitud->destinatario->apellido_paterno }}
                                    </strong>

                                    <span>
                                        {{ $solicitud->destinatario->correo }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td>
                            @if($esProveedor)
                                <span class="admin-etiqueta">
                                    Proveedor
                                </span>
                            @else
                                <span class="admin-etiqueta">
                                    Profesional
                                </span>
                            @endif
                        </td>

                        <td>
                            @if($esProveedor)
                                @if($solicitud->materiales->isNotEmpty())
                                    <div class="solicitud-admin-materiales">
                                        @foreach($solicitud->materiales->take(2) as $material)
                                            <span>
                                                {{ $material->nombre }}
                                            </span>
                                        @endforeach

                                        @if($solicitud->materiales->count() > 2)
                                            <small>
                                                +{{ $solicitud->materiales->count() - 2 }}
                                            </small>
                                        @endif
                                    </div>
                                @else
                                    <span class="texto-muted">
                                        Sin materiales
                                    </span>
                                @endif
                            @else
                                @if($solicitud->servicio)
                                    {{ $solicitud->servicio->nombre }}
                                @else
                                    <span class="texto-muted">
                                        Sin servicio
                                    </span>
                                @endif
                            @endif
                        </td>

                        <td>
                            <span class="admin-estado solicitud-estado-{{ $solicitud->estado }}">
                                {{ $estados[$solicitud->estado] ?? ucfirst($solicitud->estado) }}
                            </span>
                        </td>

                        <td>
                            {{ $solicitud->created_at->format('d/m/Y') }}
                        </td>

                        <td>
                            <a
                                href="{{ route('admin.solicitudes.mostrar', $solicitud) }}"
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
                            No se encontraron solicitudes.
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