<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perfil de {{ $usuario->nombre }}</title>
    <link rel="stylesheet" href="{{ asset('css/perfil-publico.css') }}">
    <link rel="stylesheet" href="{{ asset('css/calificaciones-perfil.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="{{ asset('js/notification.js') }}" defer></script>
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
                    <form action="{{ route('logout') }}" method="POST" class="logout-menu-form">
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
    $calificacionesPerfil = $usuario->calificacionesRecibidas;
    $totalCalificaciones = $calificacionesPerfil->count();

    $promedioCalificacion = $totalCalificaciones > 0
        ? round((float) $calificacionesPerfil->avg('promedio'), 1)
        : 0;

    $descripcionPrincipal = $usuario->descripcion;

    if (!$descripcionPrincipal && $usuario->rol->nombre === 'profesional') {
        $descripcionPrincipal = $usuario->profesional?->descripcion;
    }

    if (!$descripcionPrincipal && $usuario->rol->nombre === 'proveedor') {
        $descripcionPrincipal = $usuario->proveedor?->descripcion;
    }
@endphp

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

        <div class="calificacion-perfil">
            <div
                class="estrellas-calificacion"
                aria-label="Calificación promedio {{ number_format($promedioCalificacion, 1) }} de 5"
            >
                @for($i = 1; $i <= 5; $i++)
                    @php
                        $relleno = ($promedioCalificacion - ($i - 1)) * 100;
                        $relleno = max(0, min(100, $relleno));
                    @endphp

                    <span
                        class="estrella-calificacion"
                        style="--relleno: {{ $relleno }}%"
                        aria-hidden="true"
                    >
                        ★
                    </span>
                @endfor
            </div>

            @if($totalCalificaciones > 0)
                <div class="calificacion-datos">
                    <strong>
                        {{ number_format($promedioCalificacion, 1) }}
                    </strong>

                    <span>
                        {{ $totalCalificaciones }}
                        {{ $totalCalificaciones === 1 ? 'calificación' : 'calificaciones' }}
                    </span>
                </div>
            @else
                <span class="sin-calificaciones">
                    Sin calificaciones
                </span>
            @endif
        </div>

        <div class="perfil-descripcion">
            @if($descripcionPrincipal)
                <p>
                    {{ $descripcionPrincipal }}
                </p>
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

    @auth
        <section class="perfil-seccion contacto-seccion">
            <div class="seccion-titulo">
                <h2>Datos de contacto</h2>

                <span class="contacto-disponible">
                    Disponible
                </span>
            </div>

            <div class="contacto-grid">
                <div class="contacto-dato">
                    <span class="contacto-etiqueta">
                        Correo electrónico
                    </span>

                    @if($usuario->correo)
                        <a href="mailto:{{ $usuario->correo }}">
                            {{ $usuario->correo }}
                        </a>
                    @else
                        <strong>No especificado</strong>
                    @endif
                </div>

                <div class="contacto-dato">
                    <span class="contacto-etiqueta">
                        Teléfono
                    </span>

                    @if($usuario->telefono)
                        <a href="tel:{{ preg_replace('/\D/', '', $usuario->telefono) }}">
                            {{ $usuario->telefono }}
                        </a>
                    @else
                        <strong>No especificado</strong>
                    @endif
                </div>
            </div>
        </section>
    @else
        <section class="perfil-seccion contacto-bloqueado">
            <div class="contacto-bloqueado-icono">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        d="M7 10V7a5 5 0 0 1 10 0v3"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />

                    <rect
                        x="5"
                        y="10"
                        width="14"
                        height="10"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M12 14v2"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>
            </div>

            <div class="contacto-bloqueado-contenido">
                <h2>
                    Datos de contacto
                </h2>

                <p>
                    Inicia sesión para consultar el correo electrónico y teléfono de este perfil.
                </p>

                <a
                    href="{{ route('login') }}"
                    class="btn-login-contacto"
                >
                    Iniciar sesión
                </a>
            </div>
        </section>
    @endauth

    @if($usuario->rol->nombre === 'profesional')
        <section class="perfil-seccion">
            <h2>
                Información profesional
            </h2>

            <div class="informacion-lista">
                <p>
                    <strong>Profesión:</strong>
                    {{ $usuario->profesional?->profesiones->pluck('nombre')->join(', ') ?: 'No especificada' }}
                </p>

                <p>
                    <strong>Especialidad:</strong>
                    {{ $usuario->profesional?->especialidades->pluck('nombre')->join(', ') ?: 'No especificada' }}
                </p>

                <p>
                    <strong>Años de experiencia:</strong>
                    {{ $usuario->profesional?->anios_experiencia ?? 'No especificados' }}
                </p>

                <p>
                    <strong>Zona de trabajo:</strong>
                    {{ $usuario->profesional?->zona_trabajo ?? 'No especificada' }}
                </p>

                @if($usuario->profesional?->descripcion)
                    <p>
                        <strong>Descripción profesional:</strong>
                        {{ $usuario->profesional->descripcion }}
                    </p>
                @endif

                @if($usuario->profesional?->portafolio_url)
                    <p>
                        <strong>Portafolio:</strong>

                        <a
                            href="{{ $usuario->profesional->portafolio_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="enlace-portafolio"
                        >
                            Ver portafolio
                        </a>
                    </p>
                @endif
            </div>
        </section>

        <section class="perfil-seccion">
            <h2>
                Servicios que ofrece
            </h2>

            @if($usuario->profesional && $usuario->profesional->servicios->isNotEmpty())
                <div class="servicios-lista">
                    @foreach($usuario->profesional->servicios as $servicio)
                        <span class="servicio-etiqueta">
                            {{ $servicio->nombre }}
                        </span>
                    @endforeach
                </div>
            @else
                <p>
                    Este profesional aún no ha registrado servicios.
                </p>
            @endif
        </section>

    @elseif($usuario->rol->nombre === 'proveedor')
        <section class="perfil-seccion">
            <h2>
                Información del proveedor
            </h2>

            <div class="informacion-lista">
                <p>
                    <strong>Descripción de la empresa:</strong>
                    {{ $usuario->proveedor?->descripcion ?? 'Sin descripción' }}
                </p>

                <p>
                    <strong>Zona de trabajo:</strong>
                    {{ $usuario->proveedor?->zona_trabajo ?? 'No especificada' }}
                </p>
            </div>
        </section>

        <section class="perfil-seccion">
            <h2>
                Materiales o productos
            </h2>

            @if($usuario->proveedor && $usuario->proveedor->materiales->isNotEmpty())
                <div class="servicios-lista">
                    @foreach($usuario->proveedor->materiales as $material)
                        <span class="servicio-etiqueta">
                            {{ $material->nombre }}
                        </span>
                    @endforeach
                </div>
            @else
                <p>
                    Este proveedor aún no ha registrado materiales.
                </p>
            @endif
        </section>
    @endif

    @if(
        in_array($usuario->rol->nombre, ['profesional', 'proveedor']) &&
        !empty($usuario->portafolio_fotos) &&
        count($usuario->portafolio_fotos) > 0
    )

        <section class="portafolio-seccion">

            <div class="portafolio-encabezado">

                <div>
                    <h2>Portafolio</h2>

                    <p>
                        Trabajos realizados
                    </p>
                </div>

                <span class="portafolio-contador">
                    <span id="portafolioActual">1</span>
                    /
                    <span id="portafolioTotal">
                        {{ count($usuario->portafolio_fotos) }}
                    </span>
                </span>

            </div>

            <div
                class="portafolio-carrusel"
                id="portafolioCarrusel"
                data-total="{{ count($usuario->portafolio_fotos) }}"
            >

                @if(count($usuario->portafolio_fotos) > 1)

                    <button
                        type="button"
                        class="portafolio-flecha portafolio-anterior"
                        id="portafolioAnterior"
                        aria-label="Imagen anterior"
                    >
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                @endif

                <div class="portafolio-ventana">

                    @foreach($usuario->portafolio_fotos as $indice => $foto)

                        <div
                            class="portafolio-slide {{ $loop->first ? 'activo' : '' }}"
                            data-indice="{{ $indice }}"
                        >

                            <button
                                type="button"
                                class="portafolio-imagen-boton"
                                data-imagen="{{ asset('storage/' . $foto) }}"
                                aria-label="Ampliar imagen {{ $indice + 1 }} del portafolio"
                            >

                                <img
                                    src="{{ asset('storage/' . $foto) }}"
                                    alt="Trabajo del portafolio {{ $indice + 1 }}"
                                    loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                >

                                <span class="portafolio-overlay">

                                    <span class="portafolio-expandir">
                                        <i class="fa-solid fa-expand"></i>
                                    </span>

                                </span>

                            </button>

                        </div>

                    @endforeach

                </div>

                @if(count($usuario->portafolio_fotos) > 1)

                    <button
                        type="button"
                        class="portafolio-flecha portafolio-siguiente"
                        id="portafolioSiguiente"
                        aria-label="Imagen siguiente"
                    >
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                @endif

            </div>

            <div class="portafolio-pie">

                <span class="portafolio-etiqueta">
                    <i class="fa-solid fa-camera"></i>
                    Trabajo
                    <span id="portafolioTrabajoActual">1</span>
                </span>

                <span class="portafolio-ayuda">
                    Haz clic en la imagen para ampliarla
                </span>

            </div>

        </section>

        <div
            class="portafolio-modal"
            id="portafolioModal"
            aria-hidden="true"
        >

            <div
                class="portafolio-modal-fondo"
                id="portafolioModalFondo"
            ></div>

            <div class="portafolio-modal-contenido">

                <button
                    type="button"
                    class="portafolio-modal-cerrar"
                    id="cerrarPortafolio"
                    aria-label="Cerrar imagen"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <img
                    src=""
                    id="imagenPortafolioGrande"
                    alt="Imagen ampliada del portafolio"
                >

            </div>

        </div>

    @endif

    @if(in_array($usuario->rol->nombre, ['profesional', 'proveedor']))
        @auth
            @if(
                auth()->id() !== $usuario->id &&
                in_array(auth()->user()->rol->nombre, ['usuario', 'profesional', 'proveedor'])
            )
                @if(
                    $usuario->rol->nombre === 'profesional' &&
                    $usuario->profesional &&
                    $usuario->profesional->servicios->isNotEmpty()
                )
                    <div class="acciones-perfil">
                        <a
                            href="{{ route('solicitudes.crear', $usuario) }}"
                            class="btn-solicitar"
                        >
                            Solicitar servicio
                        </a>
                    </div>

                @elseif(
                    $usuario->rol->nombre === 'proveedor' &&
                    $usuario->proveedor &&
                    $usuario->proveedor->materiales->isNotEmpty()
                )
                    <div class="acciones-perfil">
                        <a
                            href="{{ route('solicitudes.crear', $usuario) }}"
                            class="btn-solicitar"
                        >
                            Solicitar material
                        </a>
                    </div>
                @endif
            @endif

        @else
            @if(
                $usuario->rol->nombre === 'profesional' &&
                $usuario->profesional &&
                $usuario->profesional->servicios->isNotEmpty()
            )
                <div class="acciones-perfil">
                    <a
                        href="{{ route('login') }}"
                        class="btn-solicitar"
                    >
                        Inicia sesión para solicitar un servicio
                    </a>
                </div>

            @elseif(
                $usuario->rol->nombre === 'proveedor' &&
                $usuario->proveedor &&
                $usuario->proveedor->materiales->isNotEmpty()
            )
                <div class="acciones-perfil">
                    <a
                        href="{{ route('login') }}"
                        class="btn-solicitar"
                    >
                        Inicia sesión para solicitar un material
                    </a>
                </div>
            @endif
        @endauth
    @endif

    @auth
        @if(auth()->id() === $usuario->id)
            <div class="acciones-perfil">
                <a
                    href="{{ route('perfil.editar') }}"
                    class="btn-editar"
                >
                    Editar perfil
                </a>
            </div>
        @endif
    @endauth
</main>

<footer class="pie">
    <p>
        © 2026 ArquiServi. Todos los derechos reservados.
    </p>
</footer>

<script
    src="{{ asset('js/portafolio.js') }}"
    defer
></script>

</body>
</html>