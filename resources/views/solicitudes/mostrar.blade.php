<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Solicitud - ArquiServi</title>

    <link rel="stylesheet" href="{{ asset('css/solicitudes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/calificacion.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
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
    $concepto =
        $solicitud->servicio?->nombre
        ?? $solicitud->material?->nombre
        ?? 'Sin especificar';
@endphp

<main class="solicitudes-contenedor">

    <h1>
        Solicitud
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

        @if($esSolicitante)

            <div class="detalle-item">

                <span>
                    {{ $solicitud->destinatario->rol->nombre === 'proveedor' ? 'Proveedor' : 'Profesional' }}
                </span>

                <strong>
                    {{ $solicitud->destinatario->nombre }}
                    {{ $solicitud->destinatario->apellido_paterno }}
                </strong>

            </div>

        @endif

        @if($esDestinatario)

            <div class="detalle-item">

                <span>
                    Solicitante
                </span>

                <strong>
                    {{ $solicitud->solicitante->nombre }}
                    {{ $solicitud->solicitante->apellido_paterno }}
                </strong>

            </div>

        @endif

        <div class="detalle-item">

            <span>
                {{ $solicitud->material_id ? 'Material / Producto' : 'Servicio' }}
            </span>

            <strong>
                {{ $concepto }}
            </strong>

        </div>

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
                {{ $solicitud->created_at->format('d/m/Y') }}
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

        @if($solicitud->estado === 'pendiente')

            <div class="acciones">

                @if($esSolicitante)

                    <form
                        action="{{ route('solicitudes.cancelar', $solicitud) }}"
                        method="POST"
                    >

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="btn-cancelar"
                        >
                            Cancelar solicitud
                        </button>

                    </form>

                @endif

                @if($esDestinatario)

                    <form
                        action="{{ route('solicitudes.rechazar', $solicitud) }}"
                        method="POST"
                    >

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="btn-rechazar"
                        >
                            Rechazar
                        </button>

                    </form>

                    <form
                        action="{{ route('solicitudes.aceptar', $solicitud) }}"
                        method="POST"
                    >

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="btn-aceptar"
                        >
                            Aceptar
                        </button>

                    </form>

                @endif

            </div>

        @endif

        @if(
            in_array(
                $solicitud->estado,
                ['aceptada', 'terminada']
            )
        )

            <div class="contacto-whatsapp">

                @if(
                    $esSolicitante &&
                    $solicitud->destinatario->telefono
                )

                    @php
                        $telefono =
                            $solicitud
                                ->destinatario
                                ->telefonoWhatsapp();

                        $mensaje =
                            "Hola {$solicitud->destinatario->nombre}, soy {$solicitud->solicitante->nombre}. "
                            . "Te contacto desde ArquiServi respecto a mi solicitud de \"{$concepto}\". "
                            . "Me gustaría continuar con los detalles.";

                        $whatsappUrl =
                            'https://wa.me/'
                            . $telefono
                            . '?text='
                            . urlencode($mensaje);
                    @endphp

                    <a
                        href="{{ $whatsappUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn-whatsapp"
                    >
                        Contactar por WhatsApp
                    </a>

                @endif

                @if(
                    $esDestinatario &&
                    $solicitud->solicitante->telefono
                )

                    @php
                        $telefono =
                            $solicitud
                                ->solicitante
                                ->telefonoWhatsapp();

                        $mensaje =
                            "Hola {$solicitud->solicitante->nombre}, soy {$solicitud->destinatario->nombre}. "
                            . "Te contacto desde ArquiServi respecto a tu solicitud de \"{$concepto}\". "
                            . "Podemos continuar con los detalles.";

                        $whatsappUrl =
                            'https://wa.me/'
                            . $telefono
                            . '?text='
                            . urlencode($mensaje);
                    @endphp

                    <a
                        href="{{ $whatsappUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn-whatsapp"
                    >
                        Contactar por WhatsApp
                    </a>

                @endif

            </div>

        @endif

        @if(
            $solicitud->estado === 'aceptada' &&
            $esDestinatario
        )

            <div class="acciones acciones-trabajo">

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
                        Terminar solicitud
                    </button>

                </form>

            </div>

        @endif

        @if(
            $solicitud->estado === 'terminada' &&
            !$solicitud->calificaciones->contains(
                'evaluador_id',
                auth()->id()
            )
        )

            <div class="acciones acciones-calificacion">

                <button
                    type="button"
                    class="btn-calificar btn-abrir-calificacion"
                    data-url="{{ route('solicitudes.calificar', $solicitud) }}"
                >

                    @if($esSolicitante)

                        Calificar
                        {{ $solicitud->destinatario->rol->nombre === 'proveedor' ? 'proveedor' : 'profesional' }}

                    @else

                        Calificar solicitante

                    @endif

                </button>

            </div>

        @endif

        <div class="volver">

            @if($esSolicitante)

                <a href="{{ route('solicitudes.mias') }}">
                    ← Volver a mis solicitudes
                </a>

            @else

                <a href="{{ route('solicitudes.recibidas') }}">
                    ← Volver a solicitudes recibidas
                </a>

            @endif

        </div>

    </section>

</main>

<footer class="pie">

    <p>
        © 2026 ArquiServi. Todos los derechos reservados.
    </p>

</footer>

<div id="modalCalificacionContenedor"></div>

<script src="{{ asset('js/reseña.js') }}"></script>

</body>
</html>