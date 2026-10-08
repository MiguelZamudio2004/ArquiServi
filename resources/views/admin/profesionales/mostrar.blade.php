@extends('admin.layout')

@section('titulo', 'Detalle del profesional')

@section('contenido')
@php
    $usuario = $profesional->usuario;

    $perfilPublicoDisponible =
        $profesional->estaDisponiblePublicamente()
        && $usuario->estado === 'activo';
@endphp

<div class="admin-volver">
    <a href="{{ route('admin.profesionales.index') }}">
        <i class="fa-solid fa-arrow-left"></i>
        Volver a profesionales
    </a>
</div>

<section class="detalle-usuario-cabecera">
    <div class="detalle-avatar">
        @if($usuario->foto_perfil)
            <img
                src="{{ asset('storage/' . $usuario->foto_perfil) }}"
                alt="Foto de perfil"
            >
        @else
            <i class="fa-solid fa-user-tie"></i>
        @endif
    </div>

    <div class="detalle-usuario-principal">
        <span class="admin-mini-titulo">
            Profesional
        </span>

        <h1>
            {{ $usuario->nombre }}
            {{ $usuario->apellido_paterno }}
            {{ $usuario->apellido_materno }}
        </h1>

        <div class="detalle-etiquetas">
            @foreach($profesional->profesiones as $profesion)
                <span class="admin-etiqueta">
                    {{ $profesion->nombre }}
                </span>
            @endforeach

            <span class="admin-estado estado-aprobacion-{{ $profesional->estado_aprobacion }}">
                {{ $estadosAprobacion[$profesional->estado_aprobacion] ?? 'Sin estado' }}
            </span>

            <span class="admin-estado estado-{{ $usuario->estado }}">
                {{ ucfirst($usuario->estado) }}
            </span>
        </div>
    </div>

    <div class="detalle-acciones-profesional">
        @if($perfilPublicoDisponible)
            <a
                href="{{ route('perfil.publico', $usuario) }}"
                class="admin-boton admin-boton-secundario"
                target="_blank"
                rel="noopener noreferrer"
            >
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                Ver perfil público
            </a>
        @endif

        <a
            href="{{ route('admin.usuarios.mostrar', $usuario) }}"
            class="admin-boton admin-boton-secundario"
        >
            <i class="fa-solid fa-user-gear"></i>
            Administrar cuenta
        </a>
    </div>
</section>

<section class="detalle-grid">
    <article class="admin-seccion detalle-panel">
        <div class="admin-seccion-cabecera simple">
            <div>
                <span class="admin-mini-titulo">
                    Información
                </span>

                <h2>
                    Datos del profesional
                </h2>
            </div>
        </div>

        <div class="detalle-datos">
            <div class="detalle-dato">
                <i class="fa-solid fa-envelope"></i>

                <div>
                    <span>
                        Correo electrónico
                    </span>

                    <strong>
                        {{ $usuario->correo }}
                    </strong>
                </div>
            </div>

            <div class="detalle-dato">
                <i class="fa-solid fa-phone"></i>

                <div>
                    <span>
                        Teléfono
                    </span>

                    <strong>
                        {{ $usuario->telefono ?: 'No registrado' }}
                    </strong>
                </div>
            </div>

            <div class="detalle-dato">
                <i class="fa-solid fa-location-dot"></i>

                <div>
                    <span>
                        Ubicación
                    </span>

                    <strong>
                        {{ $usuario->ubicacion ?: 'No registrada' }}
                    </strong>
                </div>
            </div>

            <div class="detalle-dato">
                <i class="fa-solid fa-map-location-dot"></i>

                <div>
                    <span>
                        Zona de trabajo
                    </span>

                    <strong>
                        {{ $profesional->zona_trabajo }}
                    </strong>
                </div>
            </div>

            <div class="detalle-dato">
                <i class="fa-solid fa-briefcase"></i>

                <div>
                    <span>
                        Experiencia
                    </span>

                    <strong>
                        {{ $profesional->anios_experiencia }}
                        {{ $profesional->anios_experiencia === 1 ? 'año' : 'años' }}
                    </strong>
                </div>
            </div>

            <div class="detalle-dato">
                <i class="fa-solid fa-calendar-plus"></i>

                <div>
                    <span>
                        Registro
                    </span>

                    <strong>
                        {{ $profesional->created_at->format('d/m/Y') }}
                    </strong>
                </div>
            </div>
        </div>
    </article>

    <article class="admin-seccion detalle-panel">
        <div class="admin-seccion-cabecera simple">
            <div>
                <span class="admin-mini-titulo">
                    Estado
                </span>

                <h2>
                    Situación del perfil
                </h2>
            </div>
        </div>

        <div class="profesional-estado-resumen">
            <div class="profesional-estado-item">
                <span>
                    Estado de cuenta
                </span>

                <div>
                    <span class="admin-estado estado-{{ $usuario->estado }}">
                        {{ ucfirst($usuario->estado) }}
                    </span>
                </div>
            </div>

            <div class="profesional-estado-item">
                <span>
                    Estado de aprobación
                </span>

                <div>
                    <span class="admin-estado estado-aprobacion-{{ $profesional->estado_aprobacion }}">
                        {{ $estadosAprobacion[$profesional->estado_aprobacion] ?? 'Sin estado' }}
                    </span>
                </div>
            </div>

            <div class="profesional-estado-item">
                <span>
                    Perfil público
                </span>

                <strong>
                    @if($perfilPublicoDisponible)
                        Disponible
                    @else
                        No disponible
                    @endif
                </strong>
            </div>
        </div>
    </article>
</section>

<section class="admin-seccion profesional-informacion">
    <div class="admin-seccion-cabecera">
        <div>
            <span class="admin-mini-titulo">
                Perfil profesional
            </span>

            <h2>
                Información profesional
            </h2>

            <p>
                Información registrada por el profesional.
            </p>
        </div>
    </div>

    <div class="profesional-contenido">
        <div class="profesional-bloque">
            <h3>
                Profesión
            </h3>

            <div class="profesional-lista-etiquetas">
                @forelse($profesional->profesiones as $profesion)
                    <span class="admin-etiqueta">
                        {{ $profesion->nombre }}
                    </span>
                @empty
                    <span class="texto-muted">
                        Sin profesión registrada.
                    </span>
                @endforelse
            </div>
        </div>

        <div class="profesional-bloque">
            <h3>
                Especialidades
            </h3>

            <div class="profesional-lista-etiquetas">
                @forelse($profesional->especialidades as $especialidad)
                    <span class="admin-etiqueta">
                        {{ $especialidad->nombre }}
                    </span>
                @empty
                    <span class="texto-muted">
                        Sin especialidades registradas.
                    </span>
                @endforelse
            </div>
        </div>

        <div class="profesional-bloque">
            <h3>
                Servicios
            </h3>

            <div class="profesional-lista-etiquetas">
                @forelse($profesional->servicios as $servicio)
                    <span class="admin-etiqueta">
                        {{ $servicio->nombre }}
                    </span>
                @empty
                    <span class="texto-muted">
                        Aún no ha registrado servicios.
                    </span>
                @endforelse
            </div>
        </div>

        <div class="profesional-bloque profesional-bloque-completo">
            <h3>
                Descripción profesional
            </h3>

            <p class="profesional-descripcion">
                {{ $profesional->descripcion }}
            </p>
        </div>

        <div class="profesional-bloque profesional-bloque-completo">
            <h3>
                Portafolio externo
            </h3>

            @if($profesional->portafolio_url)
                <a
                    href="{{ $profesional->portafolio_url }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="profesional-portafolio"
                >
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    Abrir portafolio
                </a>
            @else
                <span class="texto-muted">
                    No registró un portafolio externo.
                </span>
            @endif
        </div>
    </div>
</section>
@endsection