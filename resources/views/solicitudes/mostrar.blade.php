<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Detalle de solicitud - ArquiServi</title>

    <link rel="stylesheet" href="{{ asset('css/solicitudes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/calificacion.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">

    <script src="{{ asset('js/reseña.js') }}" defer></script>
</head>

<body>

<header class="encabezado">

    <a href="{{ route('menu') }}">
        <img
            src="{{ asset('encabezado2.png') }}"
            class="logo"
            alt="ArquiServi"
        >
    </a>

</header>

@php
    $otraPersona = $esSolicitante
        ? $solicitud->destinatario
        : $solicitud->solicitante;

    $telefonoWhatsapp = $otraPersona->telefonoWhatsapp();

    $yaCalifico = $solicitud
        ->calificaciones
        ->contains('evaluador_id', auth()->id());

    if ($esSolicitante) {
        if ($solicitud->destinatario->rol->nombre === 'proveedor') {
            $textoCalificar = 'Calificar proveedor';
        } else {
            $textoCalificar = 'Calificar profesional';
        }
    } else {
        $textoCalificar = 'Calificar cliente';
    }
@endphp

<main class="solicitudes-contenedor">

    <h1>
        Detalle de solicitud
    </h1>

    @if(session('exito'))

        <div class="mensaje-exito">
            {{ session('exito') }}
        </div>

    @endif

    @if($errors->any())

        <div class="errores">

            @foreach($errors->all() as $error)

                <p>
                    {{ $error }}
                </p>

            @endforeach

        </div>

    @endif

    <section class="detalle-solicitud">

        <div class="detalle-item">

            <span>
                Solicitante
            </span>

            <strong>
                {{ $solicitud->solicitante->nombre }}
                {{ $solicitud->solicitante->apellido_paterno }}
            </strong>

        </div>

        <div class="detalle-item">

            <span>
                Destinatario
            </span>

            <strong>
                {{ $solicitud->destinatario->nombre }}
                {{ $solicitud->destinatario->apellido_paterno }}
            </strong>

        </div>

        <div class="detalle-item">

            <span>
                Tipo de destinatario
            </span>

            <strong>
                {{ ucfirst($solicitud->destinatario->rol->nombre) }}
            </strong>

        </div>

        @if($solicitud->servicio)

            <div class="detalle-item">

                <span>
                    Servicio
                </span>

                <strong>
                    {{ $solicitud->servicio->nombre }}
                </strong>

            </div>

        @endif

        @if($solicitud->material)

            <div class="detalle-item">

                <span>
                    Material o producto
                </span>

                <strong>
                    {{ $solicitud->material->nombre }}
                </strong>

            </div>

        @endif

        <div class="detalle-item">

            <span>
                Estado
            </span>

            <strong>
                <span class="estado estado-{{ $solicitud->estado }}">
                    {{ ucfirst($solicitud->estado) }}
                </span>
            </strong>

        </div>

        <div class="detalle-item">

            <span>
                Fecha
            </span>

            <strong>
                {{ $solicitud->created_at->format('d/m/Y H:i') }}
            </strong>

        </div>

        <div class="detalle-descripcion">

            <span>
                Descripción
            </span>

            <p>
                {{ $solicitud->descripcion }}
            </p>

        </div>

        @if(
            in_array($solicitud->estado, ['aceptada', 'terminada']) &&
            $telefonoWhatsapp
        )

            <div class="contacto-whatsapp">

                <a
                    href="https://wa.me/{{ $telefonoWhatsapp }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn-whatsapp"
                >
                    Contactar por WhatsApp
                </a>

            </div>

        @endif

        @if(
            $esDestinatario &&
            $solicitud->estado === 'aceptada'
        )

            <div class="acciones-trabajo">

                <form
                    action="{{ route('solicitudes.terminar', $solicitud) }}"
                    method="POST"
                >

                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="btn-terminar"
                    >
                        Terminar trabajo
                    </button>

                </form>

            </div>

        @endif

        @if($solicitud->estado === 'terminada')

            <div class="acciones-calificacion">

                @if(!$yaCalifico)

                    <button
                        type="button"
                        class="btn-calificar btn-abrir-calificacion"
                        data-url="{{ route('solicitudes.calificar', $solicitud) }}"
                    >
                        {{ $textoCalificar }}
                    </button>

                @else

                    <button
                        type="button"
                        class="btn-calificar"
                        disabled
                    >
                        Ya calificaste
                    </button>

                @endif

            </div>

        @endif

    </section>

    <div class="volver">

        @if($esSolicitante)

            <a href="{{ route('solicitudes.mias') }}">
                Volver a mis solicitudes
            </a>

        @else

            <a href="{{ route('solicitudes.recibidas') }}">
                Volver a solicitudes recibidas
            </a>

        @endif

    </div>

</main>

<div id="modalCalificacionContenedor"></div>

<footer class="pie">

    <p>
        © 2026 ArquiServi. Todos los derechos reservados.
    </p>

</footer>

</body>

</html>