<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Perfil de {{ $usuario->nombre }}</title>

    <link rel="stylesheet" href="{{ asset('css/perfil-publico.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">

    <script src="{{ asset('js/notification.js') }}" defer></script>
    <script src="{{ asset('js/perfil.js') }}" defer></script>
</head>

<body>

<header class="encabezado">
    <a href="{{ route('menu') }}" class="logo-link">
        <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
    </a>

    @auth
    <div class="acciones-usuario">

        <div class="notificaciones-container">
            <input type="hidden" id="csrf-notificaciones" value="{{ csrf_token() }}">

            <button type="button" class="campana" id="btnNotificaciones">
                <span class="campana-icono">🔔</span>

                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="contador-notificaciones" id="contadorNotificaciones">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </button>

            <div class="notificaciones-dropdown" id="notificacionesDropdown">
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
            <button type="button" class="perfil-boton" id="btnPerfil">
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

                <span class="perfil-flecha">▼</span>
            </button>

            <div class="perfil-dropdown" id="perfilDropdown">
                <div class="perfil-info">
                    <strong>
                        {{ auth()->user()->nombre }}
                        {{ auth()->user()->apellido_paterno }}
                    </strong>

                    <span>
                        {{ auth()->user()->correo }}
                    </span>
                </div>

                <a href="{{ route('perfil') }}" class="perfil-opcion">
                    Mi perfil
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button type="submit" class="perfil-opcion cerrar-sesion">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>

    </div>
    @endauth
</header>

<main class="perfil-publico">

    <section class="perfil-cabecera">

        <div class="perfil-foto">
            @if($usuario->foto_perfil)
                <img
                    src="{{ asset('storage/' . $usuario->foto_perfil) }}"
                    alt="Foto de perfil de {{ $usuario->nombre }}"
                >
            @else
                <span>
                    {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                </span>
            @endif
        </div>

        <h1>
            {{ $usuario->nombre }}
            {{ $usuario->apellido_paterno }}
        </h1>

        <span class="tipo-cuenta">
            {{ ucfirst($usuario->rol->nombre) }}
        </span>

        <div class="perfil-descripcion">
            @if($usuario->descripcion)
                <p>{{ $usuario->descripcion }}</p>
            @else
                <p class="sin-descripcion">
                    Este usuario aún no ha agregado una descripción.
                </p>
            @endif
        </div>

    </section>

    <section class="perfil-datos">

        <div class="dato">
            <span>Ubicación</span>

            <strong>
                {{ $usuario->ubicacion ?? 'No especificada' }}
            </strong>
        </div>

        <div class="dato">
            <span>Tipo de cuenta</span>

            <strong>
                {{ ucfirst($usuario->rol->nombre) }}
            </strong>
        </div>

    </section>

    @if($usuario->rol->nombre === 'profesional')
        <section class="perfil-seccion">
            <h2>Información profesional</h2>

            <p>
                La información sobre profesión, especialidades y servicios
                se mostrará aquí cuando se defina la clasificación de los profesionales.
            </p>
        </section>

    @elseif($usuario->rol->nombre === 'proveedor')
        <section class="perfil-seccion">
            <h2>Información del proveedor</h2>

            <p>
                Aquí se mostrarán los materiales, productos y demás información
                relacionada con el proveedor.
            </p>
        </section>
    @endif

    @auth
        @if(auth()->id() === $usuario->id)
            <div class="acciones-perfil">
                <a href="{{ route('perfil.editar') }}" class="btn-editar">
                    Editar perfil
                </a>
            </div>
        @endif
    @endauth

</main>

<footer class="pie">
    <p>© 2026 ArquiServi. Todos los derechos reservados.</p>
</footer>

</body>
</html>