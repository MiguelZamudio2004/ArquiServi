<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Mi Perfil</title>

    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<header class="encabezado">
    <a href="{{ route('menu') }}" class="logo-enlace">
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
        </ul>
    </nav>

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
</header>

<main class="perfil">
    <section class="perfil-cabecera">
        <div
            class="perfil-foto"
            tabindex="0"
        >
            @if($usuario->foto_perfil)
                <img
                    src="{{ asset('storage/' . $usuario->foto_perfil) }}"
                    alt="Foto de perfil"
                >
            @else
                {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
            @endif
        </div>

        <h1>
            {{ $usuario->nombre }}
            {{ $usuario->apellido_paterno }}
            {{ $usuario->apellido_materno }}
        </h1>

        <p>
            {{ strtoupper($usuario->rol->nombre) }}
        </p>

        @if($usuario->descripcion)
            <div class="perfil-descripcion">
                <p>
                    {{ $usuario->descripcion }}
                </p>
            </div>
        @endif
    </section>

    <section class="perfil-datos">
        <div>
            <span>Correo electrónico</span>

            <strong>
                {{ $usuario->correo }}
            </strong>
        </div>

        <div>
            <span>Teléfono</span>

            <strong>
                {{ $usuario->telefono ?? 'No especificado' }}
            </strong>
        </div>

        <div>
            <span>Ubicación</span>

            <strong>
                {{ $usuario->ubicacion ?? 'No especificada' }}
            </strong>
        </div>

        <div>
            <span>Tipo de cuenta</span>

            <strong>
                {{ ucfirst($usuario->rol->nombre) }}
            </strong>
        </div>
    </section>

    @if($usuario->rol->nombre === 'profesional')
        <section class="perfil-seccion">
            <h2>
                Información profesional
            </h2>

            <p>
                <strong>Profesión:</strong>

                @if($usuario->profesional && $usuario->profesional->profesiones->isNotEmpty())
                    {{ $usuario->profesional->profesiones->pluck('nombre')->join(', ') }}
                @else
                    No especificada
                @endif
            </p>

            <p>
                <strong>Especialidad:</strong>

                @if($usuario->profesional && $usuario->profesional->especialidades->isNotEmpty())
                    {{ $usuario->profesional->especialidades->pluck('nombre')->join(', ') }}
                @else
                    No especificada
                @endif
            </p>

            <p>
                <strong>Años de experiencia:</strong>

                {{ $usuario->profesional?->anios_experiencia ?? 'No especificado' }}
            </p>

            <p>
                <strong>Zona de trabajo:</strong>

                {{ $usuario->profesional?->zona_trabajo ?? 'No especificada' }}
            </p>
        </section>

        <section class="perfil-seccion">
            <h2>
                Servicios que ofrece
            </h2>

            @if($usuario->profesional && $usuario->profesional->servicios->isNotEmpty())
                <p>
                    {{ $usuario->profesional->servicios->pluck('nombre')->join(', ') }}
                </p>
            @else
                <p>
                    Aún no has registrado servicios.
                </p>
            @endif
        </section>

        <section class="perfil-seccion">
            <h2>
                Descripción profesional
            </h2>

            <p>
                {{ $usuario->profesional?->descripcion ?? 'Sin descripción profesional.' }}
            </p>
        </section>

        @if($usuario->profesional?->portafolio_url)
            <section class="perfil-seccion">
                <h2>
                    Portafolio
                </h2>

                <p>
                    <a
                        href="{{ $usuario->profesional->portafolio_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Ver portafolio
                    </a>
                </p>
            </section>
        @endif

    @elseif($usuario->rol->nombre === 'proveedor')
        <section class="perfil-seccion">
            <h2>
                Información del proveedor
            </h2>

            <p>
                <strong>Descripción de la empresa:</strong>

                {{ $usuario->proveedor?->descripcion ?? 'Sin descripción' }}
            </p>

            <p>
                <strong>Zona o ciudad donde trabaja:</strong>

                {{ $usuario->proveedor?->zona_trabajo ?? 'No especificada' }}
            </p>
        </section>

        <section class="perfil-seccion">
            <h2>
                Materiales o productos
            </h2>

            @if($usuario->proveedor && $usuario->proveedor->materiales->isNotEmpty())
                <p>
                    @foreach($usuario->proveedor->materiales as $material)
                        {{ $material->nombre }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </p>
            @else
                <p>
                    No hay materiales registrados.
                </p>
            @endif
        </section>
    @endif

    <a
        href="{{ route('perfil.editar') }}"
        class="btn-editar"
    >
        Editar perfil
    </a>
</main>

<footer class="pie">
    <p>
        © 2026 ArquiServi. Todos los derechos reservados.
    </p>
</footer>

<script src="{{ asset('js/notification.js') }}" defer></script>

</body>
</html>