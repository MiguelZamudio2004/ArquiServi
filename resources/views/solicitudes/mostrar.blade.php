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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="{{ asset('js/notification.js') }}" defer></script>
    <script src="{{ asset('js/perfil.js') }}" defer></script>
    <script src="{{ asset('js/reseña.js') }}" defer></script>
</head>

<body>

<header class="encabezado">
    <a href="{{ route('menu') }}" class="logo-link">
        <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
    </a>

    <nav class="menu-superior">
        <ul class="menu-lista">
            <li>
                <a href="{{ route('menu') }}">
                    <i class="fa-solid fa-house icono"></i>
                    <span class="texto">Inicio</span>
                </a>
            </li>

            <li>
                <a href="{{ route('usuarios.buscar') }}">
                    <i class="fa-solid fa-folder-open icono"></i>
                    <span class="texto">Catálogo</span>
                </a>
            </li>

            @auth
                @if(in_array(auth()->user()->rol->nombre, ['usuario', 'profesional', 'proveedor']))
                    <li>
                        <a href="{{ route('solicitudes.mias') }}">
                            <i class="fa-solid fa-envelope icono"></i>
                            <span class="texto">Mis solicitudes</span>
                        </a>
                    </li>
                @endif

                @if(in_array(auth()->user()->rol->nombre, ['profesional', 'proveedor']))
                    <li>
                        <a href="{{ route('solicitudes.recibidas') }}">
                            <i class="fa-solid fa-inbox icono"></i>
                            <span class="texto">Solicitudes recibidas</span>
                        </a>
                    </li>
                @endif

                <li class="notificaciones-container">
                    <input type="hidden" id="csrf-notificaciones" value="{{ csrf_token() }}">

                    <button
                        type="button"
                        class="campana-menu"
                        id="btnNotificaciones"
                        aria-label="Notificaciones"
                    >
                        <span class="campana-icono-menu">
                            <i class="fa-solid fa-bell icono"></i>

                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span
                                    class="contador-notificaciones"
                                    id="contadorNotificaciones"
                                >
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </span>

                        <span class="texto">Notificaciones</span>
                    </button>

                    <div
                        class="notificaciones-dropdown"
                        id="notificacionesDropdown"
                    >
                        <div class="notificaciones-header">
                            <h3>Notificaciones</h3>
                        </div>

                        <div class="notificaciones-lista">
                            @forelse(auth()->user()->notifications()->latest()->take(10)->get() as $notificacion)
                                <button
                                    type="button"
                                    class="notificacion {{ $notificacion->read_at ? 'leida' : 'no-leida' }}"
                                    data-url="{{ route('notificaciones.leer', $notificacion->id) }}"
                                >
                                    <div class="notificacion-contenido">
                                        <strong class="notificacion-titulo">
                                            {{ $notificacion->data['titulo'] ?? 'Notificación' }}
                                        </strong>

                                        <p class="notificacion-mensaje">
                                            {{ $notificacion->data['mensaje'] ?? '' }}
                                        </p>

                                        <small class="notificacion-fecha">
                                            {{ $notificacion->created_at->diffForHumans() }}
                                        </small>
                                    </div>

                                    @if(!$notificacion->read_at)
                                        <span class="indicador-no-leida"></span>
                                    @endif
                                </button>
                            @empty
                                <div class="sin-notificaciones">
                                    No tienes notificaciones.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </li>

                <li>
                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                        class="logout-menu-form"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="logout-menu-boton"
                            aria-label="Cerrar sesión"
                        >
                            <i class="fa-solid fa-right-from-bracket icono"></i>
                            <span class="texto">Cerrar sesión</span>
                        </button>
                    </form>
                </li>
            @endauth
        </ul>
    </nav>

    @auth
        <div class="acciones-usuario">
            <a
                href="{{ route('perfil') }}"
                class="perfil-header"
                aria-label="Mi perfil"
            >
                <span class="perfil-avatar-header">
                    @if(auth()->user()->foto_perfil)
                        <img
                            src="{{ asset('storage/' . auth()->user()->foto_perfil) }}"
                            alt="Foto de perfil"
                        >
                    @else
                        <i class="fa-solid fa-user"></i>
                    @endif
                </span>
            </a>
        </div>
    @endauth
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

    <h1>Detalle de solicitud</h1>

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

        <div class="detalle-item">
            <span>Solicitante</span>
            <strong>
                {{ $solicitud->solicitante->nombre }}
                {{ $solicitud->solicitante->apellido_paterno }}
            </strong>
        </div>

        <div class="detalle-item">
            <span>Destinatario</span>
            <strong>
                {{ $solicitud->destinatario->nombre }}
                {{ $solicitud->destinatario->apellido_paterno }}
            </strong>
        </div>

        <div class="detalle-item">
            <span>Tipo de destinatario</span>
            <strong>
                {{ ucfirst($solicitud->destinatario->rol->nombre) }}
            </strong>
        </div>

        @if($solicitud->servicio)
            <div class="detalle-item">
                <span>Servicio</span>
                <strong>{{ $solicitud->servicio->nombre }}</strong>
            </div>
        @endif

        @if($solicitud->material)
            <div class="detalle-item">
                <span>Material o producto</span>
                <strong>{{ $solicitud->material->nombre }}</strong>
            </div>
        @endif

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
                {{ $solicitud->created_at->format('d/m/Y H:i') }}
            </strong>
        </div>

        <div class="detalle-descripcion">
            <span>Descripción</span>
            <p>{{ $solicitud->descripcion }}</p>
        </div>

        {{-- ACEPTAR / RECHAZAR --}}
        @if($esDestinatario && $solicitud->estado === 'pendiente')
            <div class="acciones-trabajo">

                <form action="{{ route('solicitudes.rechazar', $solicitud) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="btn-rechazar">
                        Rechazar
                    </button>
                </form>

                <form action="{{ route('solicitudes.aceptar', $solicitud) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="btn-aceptar">
                        Aceptar
                    </button>
                </form>

            </div>
        @endif

        {{-- WHATSAPP --}}
        @if(
            in_array($solicitud->estado, ['aceptada', 'terminada']) &&
            $telefonoWhatsapp
        )
            <div class="contacto-whatsapp">
                <a href="https://wa.me/{{ $telefonoWhatsapp }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn-whatsapp">

                    Contactar por WhatsApp

                </a>
            </div>
        @endif

        {{-- TERMINAR TRABAJO --}}
        @if(
            $esDestinatario &&
            $solicitud->estado === 'aceptada'
        )
            <div class="acciones-trabajo">

                <form action="{{ route('solicitudes.terminar', $solicitud) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="btn-terminar">
                        Terminar trabajo
                    </button>
                </form>

            </div>
        @endif

        {{-- CALIFICACIÓN --}}
        @if($solicitud->estado === 'terminada')
            <div class="acciones-calificacion">

                @if(!$yaCalifico)
                    <button type="button"
                        class="btn-calificar btn-abrir-calificacion"
                        data-url="{{ route('solicitudes.calificar', $solicitud) }}">

                        {{ $textoCalificar }}

                    </button>
                @else
                    <button type="button" class="btn-calificar" disabled>
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
    <p>© 2026 ArquiServi. Todos los derechos reservados.</p>
</footer>

</body>
</html>