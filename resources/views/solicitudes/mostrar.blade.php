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
            alt="ArquiServi">
    </a>

</header>

<main class="solicitudes-contenedor">

    <h1>Solicitud de servicio</h1>

    @if(session('exito'))

        <div class="mensaje-exito">
            {{ session('exito') }}
        </div>

    @endif

    @if($errors->any())

        <div class="errores">

            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach

        </div>

    @endif

    <section class="detalle-solicitud">

        @if($esCliente)

            <div class="detalle-item">

                <span>Profesional</span>

                <strong>
                    {{ $solicitud->profesional->usuario->nombre }}
                    {{ $solicitud->profesional->usuario->apellido_paterno }}
                </strong>

            </div>

        @endif

        @if($esProfesional)

            <div class="detalle-item">

                <span>Cliente</span>

                <strong>
                    {{ $solicitud->usuario->nombre }}
                    {{ $solicitud->usuario->apellido_paterno }}
                </strong>

            </div>

        @endif

        <div class="detalle-item">

            <span>Servicio</span>

            <strong>
                {{ $solicitud->servicio->nombre }}
            </strong>

        </div>

        <div class="detalle-item">

            <span>Estado</span>

            <strong>
                <span class="estado estado-{{ $solicitud->estado }}">
                    {{ ucfirst($solicitud->estado) }}
                </span>
            </strong>

        </div>

        <div class="detalle-item">

            <span>Fecha</span>

            <strong>
                {{ $solicitud->created_at->format('d/m/Y') }}
            </strong>

        </div>

        <div class="detalle-descripcion">

            <span>Descripción</span>

            <p>
                {{ $solicitud->descripcion }}
            </p>

        </div>

        @if($solicitud->estado === 'pendiente')

            <div class="acciones">

                @if($esCliente)

                    <form
                        action="{{ route('solicitudes.cancelar', $solicitud) }}"
                        method="POST">

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="btn-cancelar">
                            Cancelar solicitud
                        </button>

                    </form>

                @endif

                @if($esProfesional)

                    <form
                        action="{{ route('solicitudes.rechazar', $solicitud) }}"
                        method="POST">

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="btn-rechazar">
                            Rechazar
                        </button>

                    </form>

                    <form
                        action="{{ route('solicitudes.aceptar', $solicitud) }}"
                        method="POST">

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="btn-aceptar">
                            Aceptar
                        </button>

                    </form>

                @endif

            </div>

        @endif

        @if(in_array($solicitud->estado, ['aceptada', 'terminada']))

            <div class="contacto-whatsapp">

                @if(
                    $esCliente &&
                    $solicitud->profesional->usuario->telefono
                )

                    @php
                        $telefono =
                            $solicitud->profesional
                                ->usuario
                                ->telefonoWhatsapp();

                        $mensaje =
                            "Hola {$solicitud->profesional->usuario->nombre}, soy {$solicitud->usuario->nombre}. "
                            . "Te contacto desde ArquiServi por mi solicitud del servicio \"{$solicitud->servicio->nombre}\". "
                            . "Me gustaría continuar con los detalles del servicio.";

                        $whatsappUrl =
                            'https://wa.me/' .
                            $telefono .
                            '?text=' .
                            urlencode($mensaje);
                    @endphp

                    <a
                        href="{{ $whatsappUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn-whatsapp">
                        Contactar por WhatsApp
                    </a>

                @endif

                @if(
                    $esProfesional &&
                    $solicitud->usuario->telefono
                )

                    @php
                        $telefono =
                            $solicitud->usuario
                                ->telefonoWhatsapp();

                        $mensaje =
                            "Hola {$solicitud->usuario->nombre}, soy {$solicitud->profesional->usuario->nombre}. "
                            . "Te contacto desde ArquiServi respecto a tu solicitud del servicio \"{$solicitud->servicio->nombre}\". "
                            . "Podemos continuar con los detalles del servicio.";

                        $whatsappUrl =
                            'https://wa.me/' .
                            $telefono .
                            '?text=' .
                            urlencode($mensaje);
                    @endphp

                    <a
                        href="{{ $whatsappUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn-whatsapp">
                        Contactar por WhatsApp
                    </a>

                @endif

            </div>

        @endif

        @if(
            $solicitud->estado === 'aceptada' &&
            $esProfesional
        )

            <div class="acciones acciones-trabajo">

                <form
                    action="{{ route('solicitudes.terminar', $solicitud) }}"
                    method="POST">

                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="btn-terminar">
                        Terminar trabajo
                    </button>

                </form>

            </div>

        @endif

        @if(
            $solicitud->estado === 'terminada' &&
            $esCliente &&
            !$solicitud->calificaciones->contains('evaluador_id', auth()->id())
        )

            <div class="acciones acciones-calificacion">

                <button
                    type="button"
                    class="btn-calificar btn-abrir-calificacion"
                    data-url="{{ route('solicitudes.calificar', $solicitud) }}">
                    Calificar profesional
                </button>

            </div>

        @endif

        @if(
            $solicitud->estado === 'terminada' &&
            $esProfesional &&
            !$solicitud->calificaciones->contains('evaluador_id', auth()->id())
        )

            <div class="acciones acciones-calificacion">

                <button
                    type="button"
                    class="btn-calificar btn-abrir-calificacion"
                    data-url="{{ route('solicitudes.calificar', $solicitud) }}">
                    Calificar cliente
                </button>

            </div>

        @endif

        <div class="volver">

            @if($esCliente)

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