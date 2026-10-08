@extends('admin.layout')

@section('titulo', 'Detalle de solicitud')

@section('contenido')
@php
    $solicitante = $solicitud->solicitante;
    $destinatario = $solicitud->destinatario;

    $tipoDestinatario = $destinatario->rol->nombre;

    $esProveedor = $tipoDestinatario === 'proveedor';

    $estados = [
        'pendiente' => 'Pendiente',
        'aceptada' => 'Aceptada',
        'rechazada' => 'Rechazada',
        'cancelada' => 'Cancelada',
        'terminada' => 'Terminada'
    ];
@endphp

<div class="admin-volver">
    <a href="{{ route('admin.solicitudes.index') }}">
        <i class="fa-solid fa-arrow-left"></i>
        Volver a solicitudes
    </a>
</div>

<section class="detalle-usuario-cabecera">
    <div class="detalle-avatar solicitud-detalle-icono">
        <i class="fa-solid fa-envelope-open-text"></i>
    </div>

    <div class="detalle-usuario-principal">
        <span class="admin-mini-titulo">
            Solicitud
        </span>

        <h1>
            Solicitud #{{ $solicitud->id }}
        </h1>

        <div class="detalle-etiquetas">
            <span class="admin-etiqueta">
                @if($esProveedor)
                    Proveedor
                @else
                    Profesional
                @endif
            </span>

            <span class="admin-estado solicitud-estado-{{ $solicitud->estado }}">
                {{ $estados[$solicitud->estado] ?? ucfirst($solicitud->estado) }}
            </span>
        </div>
    </div>
</section>

<div class="solicitud-supervision-aviso">
    <i class="fa-solid fa-eye"></i>

    <div>
        <strong>
            Vista de supervisión
        </strong>

        <span>
            El administrador puede consultar la información
            de esta solicitud, pero las acciones sobre su
            estado corresponden a los participantes.
        </span>
    </div>
</div>

<section class="solicitud-participantes">
    <article class="admin-seccion solicitud-participante">
        <div class="solicitud-participante-cabecera">
            <div class="solicitud-participante-avatar">
                @if($solicitante->foto_perfil)
                    <img
                        src="{{ asset('storage/' . $solicitante->foto_perfil) }}"
                        alt="Foto del solicitante"
                    >
                @else
                    <i class="fa-solid fa-user"></i>
                @endif
            </div>

            <div>
                <span class="admin-mini-titulo">
                    Solicitante
                </span>

                <h2>
                    {{ $solicitante->nombre }}
                    {{ $solicitante->apellido_paterno }}
                    {{ $solicitante->apellido_materno }}
                </h2>

                <span class="solicitud-participante-rol">
                    {{ ucfirst($solicitante->rol->nombre) }}
                </span>
            </div>
        </div>

        <div class="solicitud-participante-datos">
            <div>
                <i class="fa-solid fa-envelope"></i>

                <span>
                    {{ $solicitante->correo }}
                </span>
            </div>

            <div>
                <i class="fa-solid fa-phone"></i>

                <span>
                    {{ $solicitante->telefono ?: 'No registrado' }}
                </span>
            </div>
        </div>

        <a
            href="{{ route('admin.usuarios.mostrar', $solicitante) }}"
            class="admin-boton admin-boton-secundario"
        >
            <i class="fa-solid fa-user-gear"></i>
            Ver cuenta
        </a>
    </article>

    <article class="admin-seccion solicitud-participante">
        <div class="solicitud-participante-cabecera">
            <div class="solicitud-participante-avatar">
                @if($destinatario->foto_perfil)
                    <img
                        src="{{ asset('storage/' . $destinatario->foto_perfil) }}"
                        alt="Foto del destinatario"
                    >
                @else
                    @if($esProveedor)
                        <i class="fa-solid fa-store"></i>
                    @else
                        <i class="fa-solid fa-user-tie"></i>
                    @endif
                @endif
            </div>

            <div>
                <span class="admin-mini-titulo">
                    Destinatario
                </span>

                <h2>
                    {{ $destinatario->nombre }}
                    {{ $destinatario->apellido_paterno }}
                    {{ $destinatario->apellido_materno }}
                </h2>

                <span class="solicitud-participante-rol">
                    {{ ucfirst($destinatario->rol->nombre) }}
                </span>
            </div>
        </div>

        <div class="solicitud-participante-datos">
            <div>
                <i class="fa-solid fa-envelope"></i>

                <span>
                    {{ $destinatario->correo }}
                </span>
            </div>

            <div>
                <i class="fa-solid fa-phone"></i>

                <span>
                    {{ $destinatario->telefono ?: 'No registrado' }}
                </span>
            </div>
        </div>

        <a
            href="{{ route('admin.usuarios.mostrar', $destinatario) }}"
            class="admin-boton admin-boton-secundario"
        >
            <i class="fa-solid fa-user-gear"></i>
            Ver cuenta
        </a>
    </article>
</section>

<section class="detalle-grid solicitud-detalle-grid">
    <article class="admin-seccion detalle-panel">
        <div class="admin-seccion-cabecera simple">
            <div>
                <span class="admin-mini-titulo">
                    Información
                </span>

                <h2>
                    Datos de la solicitud
                </h2>
            </div>
        </div>

        <div class="detalle-datos">
            <div class="detalle-dato">
                <i class="fa-solid fa-hashtag"></i>

                <div>
                    <span>
                        Folio
                    </span>

                    <strong>
                        #{{ $solicitud->id }}
                    </strong>
                </div>
            </div>

            <div class="detalle-dato">
                <i class="fa-solid fa-calendar-plus"></i>

                <div>
                    <span>
                        Fecha de creación
                    </span>

                    <strong>
                        {{ $solicitud->created_at->format('d/m/Y H:i') }}
                    </strong>
                </div>
            </div>

            <div class="detalle-dato">
                <i class="fa-solid fa-rotate"></i>

                <div>
                    <span>
                        Última actualización
                    </span>

                    <strong>
                        {{ $solicitud->updated_at->format('d/m/Y H:i') }}
                    </strong>
                </div>
            </div>

            <div class="detalle-dato">
                <i class="fa-solid fa-circle-info"></i>

                <div>
                    <span>
                        Estado
                    </span>

                    <strong>
                        {{ $estados[$solicitud->estado] ?? ucfirst($solicitud->estado) }}
                    </strong>
                </div>
            </div>
        </div>
    </article>

    <article class="admin-seccion detalle-panel">
        <div class="admin-seccion-cabecera simple">
            <div>
                <span class="admin-mini-titulo">
                    Relación
                </span>

                <h2>
                    Tipo de solicitud
                </h2>
            </div>
        </div>

        <div class="profesional-estado-resumen">
            <div class="profesional-estado-item">
                <span>
                    Dirigida a
                </span>

                <strong>
                    @if($esProveedor)
                        Proveedor
                    @else
                        Profesional
                    @endif
                </strong>
            </div>

            <div class="profesional-estado-item">
                <span>
                    Calificaciones asociadas
                </span>

                <strong>
                    {{ $solicitud->calificaciones->count() }}
                </strong>
            </div>
        </div>
    </article>
</section>

<section class="admin-seccion solicitud-contenido">
    <div class="admin-seccion-cabecera">
        <div>
            <span class="admin-mini-titulo">
                Solicitud
            </span>

            <h2>
                @if($esProveedor)
                    Materiales solicitados
                @else
                    Servicio solicitado
                @endif
            </h2>
        </div>
    </div>

    <div class="solicitud-servicio-material">
        @if($esProveedor)
            @forelse($solicitud->materiales as $material)
                <article class="solicitud-material-card">
                    <div class="solicitud-material-icono">
                        <i class="fa-solid fa-box"></i>
                    </div>

                    <div>
                        <strong>
                            {{ $material->nombre }}
                        </strong>

                        @if($material->categoria)
                            <span>
                                {{ $material->categoria }}
                            </span>
                        @endif

                        @if($material->descripcion)
                            <p>
                                {{ $material->descripcion }}
                            </p>
                        @endif
                    </div>
                </article>
            @empty
                <span class="texto-muted">
                    No hay materiales asociados a esta solicitud.
                </span>
            @endforelse
        @else
            @if($solicitud->servicio)
                <article class="solicitud-servicio-card">
                    <div class="solicitud-material-icono">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>

                    <div>
                        <strong>
                            {{ $solicitud->servicio->nombre }}
                        </strong>

                        @if($solicitud->servicio->descripcion)
                            <p>
                                {{ $solicitud->servicio->descripcion }}
                            </p>
                        @endif
                    </div>
                </article>
            @else
                <span class="texto-muted">
                    No hay un servicio asociado.
                </span>
            @endif
        @endif
    </div>
</section>

<section class="admin-seccion solicitud-contenido">
    <div class="admin-seccion-cabecera">
        <div>
            <span class="admin-mini-titulo">
                Descripción
            </span>

            <h2>
                Detalles proporcionados
            </h2>
        </div>
    </div>

    <div class="solicitud-descripcion-admin">
        <p>
            {{ $solicitud->descripcion }}
        </p>
    </div>
</section>
@endsection