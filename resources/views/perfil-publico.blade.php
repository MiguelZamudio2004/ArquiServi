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

        <p><strong>Profesión:</strong> {{ $usuario->profesional?->profesiones->pluck('nombre')->join(', ') ?: 'No especificada' }}</p>
        <p><strong>Especialidad:</strong> {{ $usuario->profesional?->especialidades->pluck('nombre')->join(', ') ?: 'No especificada' }}</p>
        <p><strong>Años de experiencia:</strong> {{ $usuario->profesional?->anios_experiencia ?? 'No especificados' }}</p>
        <p><strong>Zona de trabajo:</strong> {{ $usuario->profesional?->zona_trabajo ?? 'No especificada' }}</p>

        @if($usuario->profesional?->descripcion)
            <p><strong>Descripción profesional:</strong> {{ $usuario->profesional->descripcion }}</p>
        @endif

        @if($usuario->profesional?->portafolio_url)
            <p>
                <strong>Portafolio:</strong>
                <a href="{{ $usuario->profesional->portafolio_url }}" target="_blank" rel="noopener noreferrer">Ver portafolio</a>
            </p>
        @endif
    </section>

    <section class="perfil-seccion">
        <h2>Servicios que ofrece</h2>

        @if($usuario->profesional && $usuario->profesional->servicios->isNotEmpty())
            <p>{{ $usuario->profesional->servicios->pluck('nombre')->join(', ') }}</p>
        @else
            <p>Este profesional aún no ha registrado servicios.</p>
        @endif
    </section>

@elseif($usuario->rol->nombre === 'proveedor')
    <section class="perfil-seccion">
        <h2>Información del proveedor</h2>

        <p><strong>Descripción de la empresa:</strong> {{ $usuario->proveedor?->descripcion ?? 'Sin descripción' }}</p>
        <p><strong>Zona de trabajo:</strong> {{ $usuario->proveedor?->zona_trabajo ?? 'No especificada' }}</p>
    </section>

    <section class="perfil-seccion">
        <h2>Materiales o productos</h2>

        @if($usuario->proveedor && $usuario->proveedor->materiales->isNotEmpty())
            <p>{{ $usuario->proveedor->materiales->pluck('nombre')->join(', ') }}</p>
        @else
            <p>Este proveedor aún no ha registrado materiales.</p>
        @endif
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