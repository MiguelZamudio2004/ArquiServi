@extends('admin.layout')

@section('titulo', 'Detalle del proveedor')

@section('contenido')
@php
    $usuario = $proveedor->usuario;

    $materialesDisponibles = $proveedor
        ->materiales
        ->filter(function ($material) {
            return (bool) $material->pivot->disponible;
        })
        ->count();
@endphp

<div class="admin-volver">
    <a href="{{ route('admin.proveedores.index') }}">
        <i class="fa-solid fa-arrow-left"></i>
        Volver a proveedores
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
            <i class="fa-solid fa-store"></i>
        @endif
    </div>

    <div class="detalle-usuario-principal">
        <span class="admin-mini-titulo">
            Proveedor
        </span>

        <h1>
            {{ $usuario->nombre }}
            {{ $usuario->apellido_paterno }}
            {{ $usuario->apellido_materno }}
        </h1>

        <div class="detalle-etiquetas">
            <span class="admin-etiqueta">
                Proveedor
            </span>

            <span class="admin-estado estado-{{ $usuario->estado }}">
                {{ ucfirst($usuario->estado) }}
            </span>
        </div>
    </div>

    <div class="detalle-acciones-profesional">
        @if($usuario->estado === 'activo')
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
                    Datos del proveedor
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
                        {{ $proveedor->zona_trabajo }}
                    </strong>
                </div>
            </div>

            <div class="detalle-dato">
                <i class="fa-solid fa-boxes-stacked"></i>

                <div>
                    <span>
                        Materiales registrados
                    </span>

                    <strong>
                        {{ $proveedor->materiales->count() }}
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
                        {{ $proveedor->created_at->format('d/m/Y') }}
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
                    Situación del proveedor
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
                    Materiales registrados
                </span>

                <strong>
                    {{ $proveedor->materiales->count() }}
                </strong>
            </div>

            <div class="profesional-estado-item">
                <span>
                    Materiales disponibles
                </span>

                <strong>
                    {{ $materialesDisponibles }}
                </strong>
            </div>

            <div class="profesional-estado-item">
                <span>
                    Perfil público
                </span>

                <strong>
                    @if($usuario->estado === 'activo')
                        Disponible
                    @else
                        No disponible
                    @endif
                </strong>
            </div>
        </div>
    </article>
</section>

<section class="admin-seccion proveedor-informacion">
    <div class="admin-seccion-cabecera">
        <div>
            <span class="admin-mini-titulo">
                Perfil del proveedor
            </span>

            <h2>
                Información comercial
            </h2>

            <p>
                Información registrada por el proveedor.
            </p>
        </div>
    </div>

    <div class="proveedor-contenido">
        <div class="proveedor-bloque proveedor-bloque-completo">
            <h3>
                Descripción
            </h3>

            @if($proveedor->descripcion)
                <p class="profesional-descripcion">
                    {{ $proveedor->descripcion }}
                </p>
            @else
                <span class="texto-muted">
                    El proveedor no agregó una descripción.
                </span>
            @endif
        </div>

        <div class="proveedor-bloque proveedor-bloque-completo">
            <h3>
                Materiales o productos
            </h3>

            @if($proveedor->materiales->isNotEmpty())
                <div class="proveedor-materiales-grid">
                    @foreach($proveedor->materiales as $material)
                        <div class="proveedor-material">
                            <div class="proveedor-material-icono">
                                <i class="fa-solid fa-box"></i>
                            </div>

                            <div class="proveedor-material-info">
                                <strong>
                                    {{ $material->nombre }}
                                </strong>

                                @if($material->categoria)
                                    <span>
                                        {{ $material->categoria }}
                                    </span>
                                @endif
                            </div>

                            @if($material->pivot->disponible)
                                <span class="material-disponibilidad disponible">
                                    Disponible
                                </span>
                            @else
                                <span class="material-disponibilidad no-disponible">
                                    No disponible
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <span class="texto-muted">
                    Este proveedor no tiene materiales registrados.
                </span>
            @endif
        </div>
    </div>
</section>
@endsection