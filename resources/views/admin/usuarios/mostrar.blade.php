@extends('admin.layout')

@section('titulo', 'Detalle de usuario')

@section('contenido')

@php
    $rol = $usuario->rol->nombre;

    $roles = [
        'usuario' => 'Usuario',
        'profesional' => 'Profesional',
        'proveedor' => 'Proveedor'
    ];
@endphp

<div class="admin-volver">

    <a href="{{ route('admin.usuarios.index') }}">

        <i class="fa-solid fa-arrow-left"></i>

        Volver a usuarios

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

            <i class="fa-solid fa-user"></i>

        @endif

    </div>

    <div class="detalle-usuario-principal">

        <span class="admin-mini-titulo">
            Cuenta de usuario
        </span>

        <h1>
            {{ $usuario->nombre }}
            {{ $usuario->apellido_paterno }}
            {{ $usuario->apellido_materno }}
        </h1>

        <div class="detalle-etiquetas">

            <span class="admin-etiqueta">
                {{ $roles[$rol] ?? ucfirst($rol) }}
            </span>

            <span class="admin-estado estado-{{ $usuario->estado }}">
                {{ ucfirst($usuario->estado) }}
            </span>

        </div>

    </div>

    <div class="detalle-acciones-profesional">

        @if($rol === 'profesional' && $usuario->profesional)

            <a
                href="{{ route('admin.profesionales.mostrar', $usuario->profesional) }}"
                class="admin-boton admin-boton-secundario"
            >

                <i class="fa-solid fa-user-tie"></i>

                Ver profesional

            </a>

        @elseif($rol === 'proveedor' && $usuario->proveedor)

            <a
                href="{{ route('admin.proveedores.mostrar', $usuario->proveedor) }}"
                class="admin-boton admin-boton-secundario"
            >

                <i class="fa-solid fa-store"></i>

                Ver proveedor

            </a>

        @endif

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
                    Datos de la cuenta
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

                <i class="fa-solid fa-user-tag"></i>

                <div>

                    <span>
                        Tipo de cuenta
                    </span>

                    <strong>
                        {{ $roles[$rol] ?? ucfirst($rol) }}
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
                        {{ ucfirst($usuario->estado) }}
                    </strong>

                </div>

            </div>

            <div class="detalle-dato">

                <i class="fa-solid fa-calendar-plus"></i>

                <div>

                    <span>
                        Fecha de registro
                    </span>

                    <strong>
                        {{ $usuario->created_at->format('d/m/Y H:i') }}
                    </strong>

                </div>

            </div>

        </div>

    </article>

    <article class="admin-seccion detalle-panel">

        <div class="admin-seccion-cabecera simple">

            <div>

                <span class="admin-mini-titulo">
                    Administración
                </span>

                <h2>
                    Estado de la cuenta
                </h2>

            </div>

        </div>

        <div class="usuario-estado-actual">

            <div>

                <span>
                    Estado actual
                </span>

                <span class="admin-estado estado-{{ $usuario->estado }}">
                    {{ ucfirst($usuario->estado) }}
                </span>

            </div>

            <p>
                Puedes modificar el acceso de esta cuenta
                sin eliminar permanentemente su información.
            </p>

        </div>

        <div class="usuario-estado-acciones">

            @foreach($estadosCuenta as $valor => $texto)

                @if($usuario->estado !== $valor)

                    <form
                        action="{{ route('admin.usuarios.estado', $usuario) }}"
                        method="POST"
                        class="js-confirm-form"
                        data-confirm-tipo="{{ $valor === 'suspendido' ? 'advertencia' : 'normal' }}"
                        data-confirm-titulo="Cambiar estado de cuenta"
                        data-confirm-mensaje="¿Confirmas que deseas cambiar el estado de esta cuenta a {{ strtolower($texto) }}?"
                        data-confirm-boton="Sí, cambiar estado"
                    >

                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="estado"
                            value="{{ $valor }}"
                        >

                        <button
                            type="submit"
                            class="usuario-estado-boton estado-accion-{{ $valor }}"
                        >

                            @if($valor === 'activo')

                                <i class="fa-solid fa-circle-check"></i>

                            @elseif($valor === 'inactivo')

                                <i class="fa-solid fa-circle-pause"></i>

                            @else

                                <i class="fa-solid fa-ban"></i>

                            @endif

                            {{ $texto }}

                        </button>

                    </form>

                @endif

            @endforeach

        </div>

    </article>

</section>

@if($usuario->descripcion)

    <section class="admin-seccion usuario-descripcion-seccion">

        <div class="admin-seccion-cabecera">

            <div>

                <span class="admin-mini-titulo">
                    Perfil
                </span>

                <h2>
                    Descripción
                </h2>

            </div>

        </div>

        <div class="usuario-descripcion-contenido">

            <p>
                {{ $usuario->descripcion }}
            </p>

        </div>

    </section>

@endif

@if($rol === 'profesional' && $usuario->profesional)

    <section class="admin-seccion usuario-resumen-rol">

        <div class="admin-seccion-cabecera">

            <div>

                <span class="admin-mini-titulo">
                    Profesional
                </span>

                <h2>
                    Información relacionada
                </h2>

            </div>

        </div>

        <div class="usuario-resumen-contenido">

            <div class="usuario-resumen-item">

                <span>
                    Profesiones
                </span>

                <strong>

                    @if($usuario->profesional->profesiones->isNotEmpty())

                        {{
                            $usuario
                                ->profesional
                                ->profesiones
                                ->pluck('nombre')
                                ->join(', ')
                        }}

                    @else

                        Sin profesiones

                    @endif

                </strong>

            </div>

            <div class="usuario-resumen-item">

                <span>
                    Especialidades
                </span>

                <strong>

                    @if($usuario->profesional->especialidades->isNotEmpty())

                        {{
                            $usuario
                                ->profesional
                                ->especialidades
                                ->pluck('nombre')
                                ->join(', ')
                        }}

                    @else

                        Sin especialidades

                    @endif

                </strong>

            </div>

            <div class="usuario-resumen-item">

                <span>
                    Estado de aprobación
                </span>

                <strong>
                    {{
                        match(
                            $usuario
                                ->profesional
                                ->estado_aprobacion
                        ) {
                            'no_requerida' => 'No requerida',
                            'pendiente' => 'Pendiente',
                            'aprobado' => 'Aprobado',
                            'rechazado' => 'Rechazado',
                            default => 'Sin estado'
                        }
                    }}
                </strong>

            </div>

        </div>

    </section>

@elseif($rol === 'proveedor' && $usuario->proveedor)

    <section class="admin-seccion usuario-resumen-rol">

        <div class="admin-seccion-cabecera">

            <div>

                <span class="admin-mini-titulo">
                    Proveedor
                </span>

                <h2>
                    Información relacionada
                </h2>

            </div>

        </div>

        <div class="usuario-resumen-contenido">

            <div class="usuario-resumen-item">

                <span>
                    Zona de trabajo
                </span>

                <strong>
                    {{ $usuario->proveedor->zona_trabajo }}
                </strong>

            </div>

            <div class="usuario-resumen-item">

                <span>
                    Materiales registrados
                </span>

                <strong>
                    {{ $usuario->proveedor->materiales->count() }}
                </strong>

            </div>

            <div class="usuario-resumen-item">

                <span>
                    Materiales
                </span>

                <strong>

                    @if($usuario->proveedor->materiales->isNotEmpty())

                        {{
                            $usuario
                                ->proveedor
                                ->materiales
                                ->pluck('nombre')
                                ->join(', ')
                        }}

                    @else

                        Sin materiales

                    @endif

                </strong>

            </div>

        </div>

    </section>

@endif

<section class="admin-seccion usuario-zona-riesgo">

    <div class="usuario-zona-riesgo-cabecera">

        <div class="usuario-zona-riesgo-icono">

            <i class="fa-solid fa-triangle-exclamation"></i>

        </div>

        <div>

            <span class="admin-mini-titulo">
                Zona de riesgo
            </span>

            <h2>
                Eliminar cuenta
            </h2>

            <p>
                Esta acción elimina permanentemente la cuenta
                y la información asociada que dependa de ella.
                No se puede deshacer.
            </p>

        </div>

    </div>

    <div class="usuario-zona-riesgo-accion">

        <div>

            <strong>
                Eliminación permanente
            </strong>

            <span>
                Utiliza esta opción únicamente cuando la cuenta
                deba ser retirada definitivamente de ArquiServi.
            </span>

        </div>

        <form
            action="{{ route('admin.usuarios.eliminar', $usuario) }}"
            method="POST"
            class="js-confirm-form"
            data-confirm-tipo="rechazar"
            data-confirm-titulo="Eliminar cuenta"
            data-confirm-mensaje="¿Confirmas que deseas eliminar permanentemente esta cuenta? También se eliminará la información asociada que dependa de ella. Esta acción no se puede deshacer."
            data-confirm-boton="Sí, eliminar cuenta"
        >

            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="usuario-eliminar-boton"
            >

                <i class="fa-solid fa-trash-can"></i>

                Eliminar cuenta

            </button>

        </form>

    </div>

</section>

@endsection