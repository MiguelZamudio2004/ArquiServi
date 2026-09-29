<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Catálogo - ArquiServi</title>

    <link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="{{ asset('js/notification.js') }}" defer></script>
    <script src="{{ asset('js/perfil.js') }}" defer></script>
    <script src="{{ asset('js/usuarios.js') }}" defer></script>
</head>

<body>

<header class="encabezado">

    <a href="{{ route('menu') }}" class="logo-enlace">
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

                <input
                    type="hidden"
                    id="csrf-notificaciones"
                    value="{{ csrf_token() }}"
                >

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

                        @forelse(
                            auth()->user()
                                ->notifications()
                                ->latest()
                                ->take(10)
                                ->get()
                            as $notificacion
                        )

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
                    aria-label="Abrir menú de perfil"
                >

                    <span class="perfil-avatar">

                        @if(auth()->user()->foto_perfil)

                            <img
                                src="{{ asset('storage/' . auth()->user()->foto_perfil) }}"
                                alt="Foto de perfil"
                            >

                        @else

                            {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}

                        @endif

                    </span>

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

<main class="catalogo">

    <section class="catalogo-encabezado">

        <h1>
            Encuentra lo que necesitas
        </h1>

        <p>
            Explora profesionales, proveedores y usuarios registrados en ArquiServi.
        </p>

    </section>

    <section class="buscador">

        <form
            action="{{ route('usuarios.buscar') }}"
            method="GET"
        >

            <div class="campo-busqueda">

                <input
                    type="text"
                    name="buscar"
                    value="{{ $busqueda }}"
                    placeholder="Buscar por nombre, profesión, especialidad, material o ubicación..."
                >

                <button type="submit">
                    Buscar
                </button>

            </div>

            <div class="filtros">

                <select name="tipo">

                    <option value="">
                        Todos los perfiles
                    </option>

                    <option
                        value="usuario"
                        {{ $tipo === 'usuario' ? 'selected' : '' }}
                    >
                        Usuarios
                    </option>

                    <option
                        value="profesional"
                        {{ $tipo === 'profesional' ? 'selected' : '' }}
                    >
                        Profesionales
                    </option>

                    <option
                        value="proveedor"
                        {{ $tipo === 'proveedor' ? 'selected' : '' }}
                    >
                        Proveedores
                    </option>

                </select>

                <select
                    name="profesion_id"
                    id="profesion_id"
                >

                    <option value="">
                        Todas las profesiones
                    </option>

                    @foreach($profesiones as $profesion)

                        <option
                            value="{{ $profesion->id }}"
                            {{ $profesionId == $profesion->id ? 'selected' : '' }}
                        >
                            {{ $profesion->nombre }}
                        </option>

                    @endforeach

                </select>

                <select
                    name="especialidad_id"
                    id="especialidad_id"
                >

                    <option value="">
                        Todas las especialidades
                    </option>

                    @foreach($profesiones as $profesion)

                        @foreach($profesion->especialidades as $especialidad)

                            <option
                                value="{{ $especialidad->id }}"
                                data-profesion="{{ $profesion->id }}"
                                {{ $especialidadId == $especialidad->id ? 'selected' : '' }}
                            >
                                {{ $especialidad->nombre }}
                            </option>

                        @endforeach

                    @endforeach

                </select>

                @if(
                    $busqueda ||
                    $tipo ||
                    $profesionId ||
                    $especialidadId
                )

                    <a
                        href="{{ route('usuarios.buscar') }}"
                        class="btn-limpiar"
                    >
                        Limpiar filtros
                    </a>

                @endif

            </div>

        </form>

    </section>

    <section class="resultados">

        <div class="resultados-header">

            <h2>Perfiles</h2>

            <span>
                {{ $usuarios->total() }}
                {{ $usuarios->total() === 1 ? 'resultado' : 'resultados' }}
            </span>

        </div>

        @if($usuarios->count())

            <div class="usuarios-grid">

                @foreach($usuarios as $usuario)

                    @php
                        $calificacionesUsuario = $usuario->calificacionesRecibidas;
                        $totalCalificaciones = $calificacionesUsuario->count();

                        $promedioCalificacion = $totalCalificaciones > 0
                            ? round((float) $calificacionesUsuario->avg('promedio'), 1)
                            : 0;
                    @endphp

                    <article class="usuario-card">

                        <div class="usuario-foto">

                            @if($usuario->foto_perfil)

                                <img
                                    src="{{ asset('storage/' . $usuario->foto_perfil) }}"
                                    alt="Foto de {{ $usuario->nombre }}"
                                >

                            @else

                                <span>
                                    {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                                </span>

                            @endif

                        </div>

                        <div class="usuario-info">

                            <span class="usuario-rol">
                                {{ strtoupper($usuario->rol->nombre) }}
                            </span>

                            <h3 class="usuario-nombre">
                                {{ $usuario->nombre }}
                                {{ $usuario->apellido_paterno }}
                            </h3>

                            <div class="calificacion-tarjeta">

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

                                    <strong class="calificacion-numero">
                                        {{ number_format($promedioCalificacion, 1) }}
                                    </strong>

                                    <span class="calificacion-total">
                                        ({{ $totalCalificaciones }})
                                    </span>

                                @else

                                    <span class="sin-calificaciones-tarjeta">
                                        Sin calificaciones
                                    </span>

                                @endif

                            </div>

                            @if(
                                $usuario->rol->nombre === 'profesional' &&
                                $usuario->profesional
                            )

                                @if($usuario->profesional->profesiones->isNotEmpty())

                                    <p class="usuario-profesion-principal">
                                        {{ $usuario->profesional->profesiones->pluck('nombre')->join(', ') }}
                                    </p>

                                @endif

                                @if($usuario->profesional->especialidades->isNotEmpty())

                                    <p class="usuario-especialidad">
                                        {{ $usuario->profesional->especialidades->pluck('nombre')->join(', ') }}
                                    </p>

                                @endif

                                @if($usuario->profesional->servicios->isNotEmpty())

                                    <p class="usuario-servicios">

                                        <strong>
                                            Servicios:
                                        </strong>

                                        {{ $usuario->profesional->servicios->pluck('nombre')->take(2)->join(', ') }}

                                        @if($usuario->profesional->servicios->count() > 2)
                                            y
                                            {{ $usuario->profesional->servicios->count() - 2 }}
                                            más
                                        @endif

                                    </p>

                                @endif

                            @elseif(
                                $usuario->rol->nombre === 'proveedor' &&
                                $usuario->proveedor
                            )

                                @if($usuario->proveedor->materiales->isNotEmpty())

                                    <p class="usuario-servicios">

                                        <strong>
                                            Materiales:
                                        </strong>

                                        {{ $usuario->proveedor->materiales->pluck('nombre')->take(3)->join(', ') }}

                                    </p>

                                @endif

                            @endif

                            @if($usuario->ubicacion)

                                <p class="usuario-ubicacion">
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ $usuario->ubicacion }}
                                </p>

                            @endif

                            @php
                                $descripcion = $usuario->descripcion;

                                if (
                                    !$descripcion &&
                                    $usuario->rol->nombre === 'profesional'
                                ) {
                                    $descripcion = $usuario->profesional?->descripcion;
                                }

                                if (
                                    !$descripcion &&
                                    $usuario->rol->nombre === 'proveedor'
                                ) {
                                    $descripcion = $usuario->proveedor?->descripcion;
                                }
                            @endphp

                            @if($descripcion)

                                <p class="usuario-descripcion">
                                    {{ \Illuminate\Support\Str::limit($descripcion, 100) }}
                                </p>

                            @else

                                <p class="usuario-descripcion sin-descripcion">
                                    Sin descripción.
                                </p>

                            @endif

                        </div>

                        <a
                            href="{{ route('perfil.publico', $usuario) }}"
                            class="btn-ver-perfil"
                        >
                            Ver perfil
                        </a>

                    </article>

                @endforeach

            </div>

            @if($usuarios->hasPages())

                <div class="paginacion">

                    @if($usuarios->onFirstPage())

                        <span class="pagina-deshabilitada">
                            Anterior
                        </span>

                    @else

                        <a href="{{ $usuarios->previousPageUrl() }}">
                            Anterior
                        </a>

                    @endif

                    <span class="pagina-actual">
                        Página
                        {{ $usuarios->currentPage() }}
                        de
                        {{ $usuarios->lastPage() }}
                    </span>

                    @if($usuarios->hasMorePages())

                        <a href="{{ $usuarios->nextPageUrl() }}">
                            Siguiente
                        </a>

                    @else

                        <span class="pagina-deshabilitada">
                            Siguiente
                        </span>

                    @endif

                </div>

            @endif

        @else

            <div class="sin-resultados">

                <h3>
                    No encontramos perfiles
                </h3>

                <p>
                    Intenta realizar otra búsqueda o cambiar los filtros.
                </p>

            </div>

        @endif

    </section>

</main>

<footer class="pie">

    <p>
        © 2026 ArquiServi. Todos los derechos reservados.
    </p>

</footer>

</body>

</html>