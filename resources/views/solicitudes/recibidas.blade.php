<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Solicitudes recibidas - ArquiServi</title>

    <link rel="stylesheet" href="{{ asset('css/solicitudes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/calificacion.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="{{ asset('js/notification.js') }}" defer></script>
    <script src="{{ asset('js/perfil.js') }}" defer></script>
</head>

<body>

<header class="encabezado">

    <a href="{{ route('menu') }}" class="logo-link">
        <img
            src="{{ asset('encabezado2.png') }}"
            class="logo"
            alt="ArquiServi"
        >
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

                <li>
                    <a href="{{ route('perfil') }}">
                        <i class="fa-solid fa-user icono"></i>
                        <span class="texto">Perfil</span>
                    </a>
                </li>

            @endauth

        </ul>
    </nav>

    @auth

        <div class="acciones-usuario">

            <div class="notificaciones-container">

                <button
                    type="button"
                    class="campana"
                    id="btnNotificaciones"
                    aria-label="Notificaciones"
                >
                    <i class="fa-solid fa-bell campana-icono"></i>

                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span
                            class="contador-notificaciones"
                            id="contadorNotificaciones"
                        >
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>

                <div
                    class="notificaciones-dropdown"
                    id="notificacionesDropdown"
                >
                    <div class="notificaciones-header">
                        <h3>Notificaciones</h3>
                    </div>

                    <div class="notificaciones-lista">

                        @forelse(auth()->user()->notifications as $notificacion)

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

            </div>

            <div class="perfil-container">

                <button
                    type="button"
                    class="perfil-boton"
                    id="btnPerfil"
                >
                    <div class="perfil-avatar">

                        @if(auth()->user()->foto_perfil)
                            <img
                                src="{{ asset('storage/' . auth()->user()->foto_perfil) }}"
                                alt="Foto de perfil"
                            >
                        @else
                            {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
                        @endif

                    </div>

                    <span class="perfil-nombre">
                        {{ auth()->user()->nombre }}
                    </span>

                    <span class="perfil-flecha">
                        <i class="fa-solid fa-chevron-down"></i>
                    </span>

                </button>

                <div
                    class="perfil-dropdown"
                    id="perfilDropdown"
                >
                    <div class="perfil-info">

                        <strong>
                            {{ auth()->user()->nombre }}
                            {{ auth()->user()->apellido_paterno }}
                        </strong>

                        <span>
                            {{ auth()->user()->correo }}
                        </span>

                    </div>

                    <a
                        href="{{ route('perfil') }}"
                        class="perfil-opcion"
                    >
                        Mi Perfil
                    </a>

                    @if(in_array(auth()->user()->rol->nombre, ['usuario', 'profesional', 'proveedor']))
                        <a
                            href="{{ route('solicitudes.mias') }}"
                            class="perfil-opcion"
                        >
                            Mis solicitudes
                        </a>
                    @endif

                    @if(in_array(auth()->user()->rol->nombre, ['profesional', 'proveedor']))
                        <a
                            href="{{ route('solicitudes.recibidas') }}"
                            class="perfil-opcion"
                        >
                            Solicitudes recibidas
                        </a>
                    @endif

                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="perfil-opcion cerrar-sesion"
                        >
                            Cerrar sesión
                        </button>
                    </form>

                </div>

            </div>

        </div>

    @endauth

</header>

<main class="solicitudes-contenedor">

    <h1>
        Solicitudes recibidas
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

    @if($solicitudes->isNotEmpty())

        <div class="tabla-contenedor">

            <table class="tabla-solicitudes">

                <thead>

                    <tr>
                        <th>Solicitante</th>
                        <th>Tipo</th>
                        <th>Servicio / Material</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>

                </thead>

                <tbody>

                    @foreach($solicitudes as $solicitud)

                        @php
                            $concepto =
                                $solicitud->servicio?->nombre
                                ?? $solicitud->material?->nombre
                                ?? 'Sin especificar';
                        @endphp

                        <tr>

                            <td>
                                {{ $solicitud->solicitante->nombre }}
                                {{ $solicitud->solicitante->apellido_paterno }}
                            </td>

                            <td>
                                {{ ucfirst($solicitud->solicitante->rol->nombre) }}
                            </td>

                            <td>
                                {{ $concepto }}
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
                                        class="btn-ver"
                                    >
                                        Ver
                                    </a>

                                    @if(
                                        in_array(
                                            $solicitud->estado,
                                            ['aceptada', 'terminada']
                                        ) &&
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
                                            WhatsApp
                                        </a>

                                    @endif

                                    @if($solicitud->estado === 'aceptada')

                                        <form
                                            action="{{ route('solicitudes.terminar', $solicitud) }}"
                                            method="POST"
                                            class="form-accion-tabla"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn-terminar"
                                            >
                                                Terminar
                                            </button>

                                        </form>

                                    @endif

                                    @if(
                                        $solicitud->estado === 'terminada' &&
                                        !$solicitud->calificaciones->contains(
                                            'evaluador_id',
                                            auth()->id()
                                        )
                                    )

                                        <button
                                            type="button"
                                            class="btn-calificar btn-abrir-calificacion"
                                            data-url="{{ route('solicitudes.calificar', $solicitud) }}"
                                        >
                                            Calificar solicitante
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