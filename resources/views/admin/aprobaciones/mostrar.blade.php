@extends('admin.layout')

@section('titulo', 'Revisión profesional')

@section('contenido')
@php
    $profesional = $solicitud->profesional;
    $usuario = $profesional->usuario;
@endphp

<div class="admin-volver">
    <a href="{{ route('admin.aprobaciones.index') }}">
        <i class="fa-solid fa-arrow-left"></i>
        Volver a aprobaciones
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
            Solicitud de aprobación
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
                @endswitch
            </span>
        </div>
    </div>

    <div class="detalle-acciones-profesional">
        <a
            href="{{ route('admin.profesionales.mostrar', $profesional) }}"
            class="admin-boton admin-boton-secundario"
        >
            <i class="fa-solid fa-user-tie"></i>
            Ver profesional
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
                    Datos profesionales
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
                        {{
                            $profesional->anios_experiencia === 1
                                ? 'año'
                                : 'años'
                        }}
                    </strong>
                </div>
            </div>
        </div>
    </article>

    <article class="admin-seccion detalle-panel">
        <div class="admin-seccion-cabecera simple">
            <div>
                <span class="admin-mini-titulo">
                    Solicitud
                </span>

                <h2>
                    Estado de revisión
                </h2>
            </div>
        </div>

        <div class="profesional-estado-resumen">
            <div class="profesional-estado-item">
                <span>
                    Estado
                </span>

                <div>
                    <span class="admin-estado aprobacion-estado-{{ $solicitud->estado }}">
                        {{ ucfirst($solicitud->estado) }}
                    </span>
                </div>
            </div>

            <div class="profesional-estado-item">
                <span>
                    Fecha de solicitud
                </span>

                <strong>
                    {{ $solicitud->created_at->format('d/m/Y H:i') }}
                </strong>
            </div>

            @if($solicitud->revisado_at)
                <div class="profesional-estado-item">
                    <span>
                        Fecha de revisión
                    </span>

                    <strong>
                        {{ $solicitud->revisado_at->format('d/m/Y H:i') }}
                    </strong>
                </div>
            @endif

            @if($solicitud->revisor)
                <div class="profesional-estado-item">
                    <span>
                        Revisado por
                    </span>

                    <strong>
                        {{ $solicitud->revisor->nombre }}
                        {{ $solicitud->revisor->apellido_paterno }}
                    </strong>
                </div>
            @endif
        </div>
    </article>
</section>

<section class="admin-seccion aprobacion-informacion">
    <div class="admin-seccion-cabecera">
        <div>
            <span class="admin-mini-titulo">
                Validación
            </span>

            <h2>
                Especialidades que requieren aprobación
            </h2>

            <p>
                Estas son las especialidades que provocaron
                la solicitud de revisión administrativa.
            </p>
        </div>
    </div>

    <div class="aprobacion-especialidades">
        @forelse($especialidadesRevision as $especialidad)
            <article class="aprobacion-especialidad">
                <div class="aprobacion-especialidad-icono">
                    <i class="fa-solid fa-certificate"></i>
                </div>

                <div class="aprobacion-especialidad-info">
                    <span>
                        {{ $especialidad->profesion->nombre }}
                    </span>

                    <strong>
                        {{ $especialidad->nombre }}
                    </strong>

                    @if($especialidad->descripcion)
                        <p>
                            {{ $especialidad->descripcion }}
                        </p>
                    @endif
                </div>
            </article>
        @empty
            <p class="texto-muted">
                No fue posible localizar las especialidades
                asociadas a esta solicitud.
            </p>
        @endforelse
    </div>
</section>

<section class="admin-seccion aprobacion-informacion">
    <div class="admin-seccion-cabecera">
        <div>
            <span class="admin-mini-titulo">
                Perfil
            </span>

            <h2>
                Descripción profesional
            </h2>
        </div>
    </div>

    <div class="aprobacion-descripcion">
        <p>
            {{ $profesional->descripcion }}
        </p>

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
        @endif
    </div>
</section>

@if($solicitud->estado === 'pendiente')
    <section class="admin-seccion aprobacion-decision">
        <div class="admin-seccion-cabecera">
            <div>
                <span class="admin-mini-titulo">
                    Decisión
                </span>

                <h2>
                    Revisar solicitud
                </h2>

                <p>
                    La decisión modificará el estado de
                    aprobación del profesional.
                </p>
            </div>
        </div>

        <div class="aprobacion-acciones">
            <form
                action="{{ route('admin.aprobaciones.aprobar', $solicitud) }}"
                method="POST"
                class="aprobacion-form-aprobar js-confirm-form"
                data-confirm-tipo="aprobar"
                data-confirm-titulo="Aprobar profesional"
                data-confirm-mensaje="¿Confirmas que deseas aprobar a este profesional? Su perfil podrá estar disponible públicamente dentro de ArquiServi."
                data-confirm-boton="Sí, aprobar"
            >
                @csrf
                @method('PATCH')

                <button
                    type="submit"
                    class="aprobacion-boton aprobar"
                >
                    <i class="fa-solid fa-circle-check"></i>
                    Aprobar profesional
                </button>
            </form>

            <form
                action="{{ route('admin.aprobaciones.rechazar', $solicitud) }}"
                method="POST"
                class="aprobacion-form-rechazar js-confirm-form"
                data-confirm-tipo="rechazar"
                data-confirm-titulo="Rechazar solicitud"
                data-confirm-mensaje="¿Confirmas que deseas rechazar esta solicitud? El motivo indicado quedará registrado en la revisión."
                data-confirm-boton="Sí, rechazar"
            >
                @csrf
                @method('PATCH')

                <label for="motivo_rechazo">
                    Motivo del rechazo
                </label>

                <textarea
                    name="motivo_rechazo"
                    id="motivo_rechazo"
                    rows="4"
                    placeholder="Explica por qué la solicitud no puede ser aprobada..."
                    required
                >{{ old('motivo_rechazo') }}</textarea>

                @error('motivo_rechazo')
                    <span class="aprobacion-error">
                        {{ $message }}
                    </span>
                @enderror

                <button
                    type="submit"
                    class="aprobacion-boton rechazar"
                >
                    <i class="fa-solid fa-circle-xmark"></i>
                    Rechazar solicitud
                </button>
            </form>
        </div>
    </section>
@elseif($solicitud->estado === 'rechazada')
    <section class="admin-seccion aprobacion-resultado rechazo">
        <div class="aprobacion-resultado-icono">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>

        <div>
            <span>
                Solicitud rechazada
            </span>

            <h2>
                Motivo del rechazo
            </h2>

            <p>
                {{
                    $solicitud->motivo_rechazo
                    ?: 'No se registró un motivo.'
                }}
            </p>
        </div>
    </section>
@elseif($solicitud->estado === 'aprobada')
    <section class="admin-seccion aprobacion-resultado aprobada">
        <div class="aprobacion-resultado-icono">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <div>
            <span>
                Solicitud aprobada
            </span>

            <h2>
                Profesional aprobado
            </h2>

            <p>
                El perfil profesional ya puede estar disponible
                públicamente mientras su cuenta permanezca activa.
            </p>
        </div>
    </section>
@endif
@endsection