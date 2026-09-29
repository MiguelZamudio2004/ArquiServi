<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Solicitudes recibidas - ArquiServi</title>

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

    <h1>Solicitudes recibidas</h1>

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

    @if($solicitudes->isNotEmpty())

        <div class="tabla-contenedor">

            <table class="tabla-solicitudes">

                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($solicitudes as $solicitud)

                        <tr>

                            <td>
                                {{ $solicitud->usuario->nombre }}
                                {{ $solicitud->usuario->apellido_paterno }}
                            </td>

                            <td>
                                {{ $solicitud->servicio->nombre }}
                            </td>

                            <td>
                                <span class="estado estado-{{ $solicitud->estado }}">
                                    {{ ucfirst($solicitud->estado) }}
                                </span>
                            </td>

                            <td>

                                <div class="acciones-tabla">

                                    <a
                                        href="{{ route('solicitudes.mostrar', $solicitud) }}"
                                        class="btn-ver">
                                        Ver
                                    </a>

                                    @if(
                                        in_array($solicitud->estado, ['aceptada', 'terminada']) &&
                                        $solicitud->usuario->telefono
                                    )

                                        @php
                                            $telefono = $solicitud->usuario->telefonoWhatsapp();

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
                                            WhatsApp
                                        </a>

                                    @endif

                                    @if($solicitud->estado === 'aceptada')

                                        <form
                                            action="{{ route('solicitudes.terminar', $solicitud) }}"
                                            method="POST"
                                            class="form-accion-tabla">

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn-terminar">
                                                Terminar
                                            </button>

                                        </form>

                                    @endif

                                    @if(
                                        $solicitud->estado === 'terminada' &&
                                        !$solicitud->calificaciones->contains('evaluador_id', auth()->id())
                                    )

                                        <button
                                            type="button"
                                            class="btn-calificar btn-abrir-calificacion"
                                            data-url="{{ route('solicitudes.calificar', $solicitud) }}">
                                            Calificar cliente
                                        </button>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @else

        <div class="sin-solicitudes">
            <p>
                Aún no has recibido solicitudes.
            </p>
        </div>

    @endif

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