@extends('admin.layout')

@section('titulo', 'Detalle de calificación')

@section('contenido')

@php
    $evaluador = $calificacion->evaluador;
    $evaluado = $calificacion->evaluado;
    $solicitud = $calificacion->solicitud;

    $promedioValor =
        (float) $calificacion->promedio;

    $tipos = [
        'profesional' => 'Profesional',
        'proveedor' => 'Proveedor',
        'cliente' => 'Cliente'
    ];
@endphp

<div class="admin-volver">

    <a href="{{ route('admin.calificaciones.index') }}">

        <i class="fa-solid fa-arrow-left"></i>

        Volver a calificaciones

    </a>

</div>

<section class="detalle-usuario-cabecera">

    <div class="detalle-avatar calificacion-detalle-icono">

        <i class="fa-solid fa-star"></i>

    </div>

    <div class="detalle-usuario-principal">

        <span class="admin-mini-titulo">
            Calificación
        </span>

        <h1>
            Evaluación #{{ $calificacion->id }}
        </h1>

        <div class="detalle-etiquetas">

            <span class="admin-etiqueta">
                {{ $tipos[$calificacion->tipo_evaluado] ?? ucfirst($calificacion->tipo_evaluado) }}
            </span>

            <span class="calificacion-promedio-badge">
                <i class="fa-solid fa-star"></i>

                {{ number_format($promedioValor, 1) }}
            </span>

        </div>

    </div>

    <div class="detalle-acciones-profesional">

        <a
            href="{{ route('admin.solicitudes.mostrar', $solicitud) }}"
            class="admin-boton admin-boton-secundario"
        >

            <i class="fa-solid fa-envelope-open-text"></i>

            Ver solicitud

        </a>

    </div>

</section>

<section class="calificacion-personas">

    <article class="admin-seccion calificacion-persona">

        <div class="calificacion-persona-cabecera">

            <div class="calificacion-persona-avatar">

                @if($evaluador->foto_perfil)

                    <img
                        src="{{ asset('storage/' . $evaluador->foto_perfil) }}"
                        alt="Foto del evaluador"
                    >

                @else

                    <i class="fa-solid fa-user"></i>

                @endif

            </div>

            <div>

                <span class="admin-mini-titulo">
                    Evaluador
                </span>

                <h2>
                    {{ $evaluador->nombre }}
                    {{ $evaluador->apellido_paterno }}
                    {{ $evaluador->apellido_materno }}
                </h2>

                <span class="calificacion-persona-rol">
                    {{ ucfirst($evaluador->rol->nombre) }}
                </span>

            </div>

        </div>

        <div class="calificacion-persona-datos">

            <div>
                <i class="fa-solid fa-envelope"></i>
                <span>{{ $evaluador->correo }}</span>
            </div>

            <div>
                <i class="fa-solid fa-phone"></i>
                <span>
                    {{ $evaluador->telefono ?: 'No registrado' }}
                </span>
            </div>

        </div>

        <a
            href="{{ route('admin.usuarios.mostrar', $evaluador) }}"
            class="admin-boton admin-boton-secundario"
        >
            <i class="fa-solid fa-user-gear"></i>
            Ver cuenta
        </a>

    </article>

    <article class="admin-seccion calificacion-persona">

        <div class="calificacion-persona-cabecera">

            <div class="calificacion-persona-avatar">

                @if($evaluado->foto_perfil)

                    <img
                        src="{{ asset('storage/' . $evaluado->foto_perfil) }}"
                        alt="Foto del evaluado"
                    >

                @else

                    <i class="fa-solid fa-user"></i>

                @endif

            </div>

            <div>

                <span class="admin-mini-titulo">
                    Evaluado
                </span>

                <h2>
                    {{ $evaluado->nombre }}
                    {{ $evaluado->apellido_paterno }}
                    {{ $evaluado->apellido_materno }}
                </h2>

                <span class="calificacion-persona-rol">
                    {{ $tipos[$calificacion->tipo_evaluado] ?? ucfirst($calificacion->tipo_evaluado) }}
                </span>

            </div>

        </div>

        <div class="calificacion-persona-datos">

            <div>
                <i class="fa-solid fa-envelope"></i>
                <span>{{ $evaluado->correo }}</span>
            </div>

            <div>
                <i class="fa-solid fa-phone"></i>
                <span>
                    {{ $evaluado->telefono ?: 'No registrado' }}
                </span>
            </div>

        </div>

        <a
            href="{{ route('admin.usuarios.mostrar', $evaluado) }}"
            class="admin-boton admin-boton-secundario"
        >
            <i class="fa-solid fa-user-gear"></i>
            Ver cuenta
        </a>

    </article>

</section>

<section class="admin-seccion calificacion-resumen">

    <div class="calificacion-resumen-principal">

        <span class="admin-mini-titulo">
            Resultado
        </span>

        <h2>
            Calificación general
        </h2>

        <div class="calificacion-resumen-estrellas">

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

        <strong class="calificacion-resumen-numero">
            {{ number_format($promedioValor, 1) }}
        </strong>

        <span class="calificacion-resumen-maximo">
            de 5.0
        </span>

    </div>

</section>

<section class="admin-seccion calificacion-criterios-seccion">

    <div class="admin-seccion-cabecera">

        <div>

            <span class="admin-mini-titulo">
                Evaluación
            </span>

            <h2>
                Aspectos evaluados
            </h2>

            <p>
                Desglose de los criterios utilizados
                para obtener la calificación final.
            </p>

        </div>

    </div>

    <div class="calificacion-criterios">

        @foreach($calificacion->criterios as $clave => $valor)

            @php
                $valorNumerico = (float) $valor;

                $porcentaje =
                    ($valorNumerico / 5) * 100;
            @endphp

            <article class="calificacion-criterio">

                <div class="calificacion-criterio-superior">

                    <strong>
                        {{ $criteriosEtiquetas[$clave] ?? ucfirst(str_replace('_', ' ', $clave)) }}
                    </strong>

                    <span>
                        {{ number_format($valorNumerico, 1) }}
                    </span>

                </div>

                <div class="calificacion-criterio-estrellas">

                    @for($i = 1; $i <= 5; $i++)

                        @if($valorNumerico >= $i)

                            <i class="fa-solid fa-star"></i>

                        @elseif($valorNumerico >= ($i - 0.5))

                            <i class="fa-solid fa-star-half-stroke"></i>

                        @else

                            <i class="fa-regular fa-star"></i>

                        @endif

                    @endfor

                </div>

                <div class="calificacion-barra">

                    <div
                        class="calificacion-barra-progreso"
                        style="width: {{ $porcentaje }}%;"
                    ></div>

                </div>

            </article>

        @endforeach

    </div>

</section>

<section class="admin-seccion calificacion-comentario">

    <div class="admin-seccion-cabecera">

        <div>

            <span class="admin-mini-titulo">
                Comentario
            </span>

            <h2>
                Opinión del evaluador
            </h2>

        </div>

    </div>

    <div class="calificacion-comentario-contenido">

        @if($calificacion->comentario)

            <i class="fa-solid fa-quote-left"></i>

            <p>
                {{ $calificacion->comentario }}
            </p>

        @else

            <span class="texto-muted">
                Esta evaluación no incluye un comentario.
            </span>

        @endif

    </div>

</section>

<section class="detalle-grid calificacion-informacion">

    <article class="admin-seccion detalle-panel">

        <div class="admin-seccion-cabecera simple">

            <div>

                <span class="admin-mini-titulo">
                    Registro
                </span>

                <h2>
                    Datos de la evaluación
                </h2>

            </div>

        </div>

        <div class="detalle-datos">

            <div class="detalle-dato">

                <i class="fa-solid fa-hashtag"></i>

                <div>
                    <span>Calificación</span>
                    <strong>#{{ $calificacion->id }}</strong>
                </div>

            </div>

            <div class="detalle-dato">

                <i class="fa-solid fa-envelope-open-text"></i>

                <div>
                    <span>Solicitud</span>
                    <strong>#{{ $solicitud->id }}</strong>
                </div>

            </div>

            <div class="detalle-dato">

                <i class="fa-solid fa-calendar-plus"></i>

                <div>
                    <span>Fecha</span>
                    <strong>
                        {{ $calificacion->created_at->format('d/m/Y H:i') }}
                    </strong>
                </div>

            </div>

            <div class="detalle-dato">

                <i class="fa-solid fa-user-check"></i>

                <div>
                    <span>Tipo evaluado</span>
                    <strong>
                        {{ $tipos[$calificacion->tipo_evaluado] ?? ucfirst($calificacion->tipo_evaluado) }}
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
                    Servicio o materiales
                </h2>

            </div>

        </div>

        <div class="calificacion-origen">

            @if($solicitud->servicio)

                <div class="calificacion-origen-item">

                    <i class="fa-solid fa-screwdriver-wrench"></i>

                    <div>
                        <span>Servicio</span>
                        <strong>
                            {{ $solicitud->servicio->nombre }}
                        </strong>
                    </div>

                </div>

            @endif

            @if($solicitud->materiales->isNotEmpty())

                <div class="calificacion-origen-item">

                    <i class="fa-solid fa-boxes-stacked"></i>

                    <div>

                        <span>Materiales</span>

                        <strong>
                            {{
                                $solicitud
                                    ->materiales
                                    ->pluck('nombre')
                                    ->join(', ')
                            }}
                        </strong>

                    </div>

                </div>

            @endif

        </div>

    </article>

</section>

@endsection