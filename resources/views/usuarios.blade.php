<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Catálogo - ArquiServi</title>
    <script src="{{ asset('js/notification.js') }}" defer></script>
    <script src="{{ asset('js/perfil.js') }}" defer></script>
    <script src="{{ asset('js/usuarios.js') }}" defer></script>
    <link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
</head>

<body>

    <header class="encabezado">
        <a href="{{ route('menu') }}" class="logo-enlace">
            <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
        </a>

        @auth
            <div class="acciones-usuario">

                <div class="notificaciones-container">
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
                            @forelse(auth()->user()->notifications()->latest()->take(10)->get() as $notificacion)
                                <button type="button"
                                    class="notificacion {{ $notificacion->read_at ? 'leida' : 'no-leida' }}"
                                    data-id="{{ $notificacion->id }}">

                                    <div class="notificacion-contenido">
                                        <span class="notificacion-titulo">
                                            {{ $notificacion->data['titulo'] ?? 'Notificación' }}
                                        </span>

                                        <p class="notificacion-mensaje">
                                            {{ $notificacion->data['mensaje'] ?? '' }}
                                        </p>

                                        <span class="notificacion-fecha">
                                            {{ $notificacion->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    @if(!$notificacion->read_at)
                                        <span class="indicador-no-leida"></span>
                                    @endif
                                </button>
                            @empty
                                <p class="sin-notificaciones">No tienes notificaciones.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="perfil-container">
                    <button type="button" class="perfil-boton" id="btnPerfil">

                        <span class="perfil-avatar">
                            @if(auth()->user()->foto_perfil)
                                <img src="{{ asset('storage/' . auth()->user()->foto_perfil) }}" alt="Foto de perfil">
                            @else
                                {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
                            @endif
                        </span>

                        <span class="perfil-nombre">{{ auth()->user()->nombre }}</span>
                        <span class="perfil-flecha">▼</span>
                    </button>

                    <div class="perfil-dropdown" id="perfilDropdown">
                        <div class="perfil-info">
                            <strong>
                                {{ auth()->user()->nombre }}
                                {{ auth()->user()->apellido_paterno }}
                            </strong>

                            <span>{{ auth()->user()->correo }}</span>
                        </div>

                        <a href="{{ route('perfil') }}" class="perfil-opcion">Mi perfil</a>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="perfil-opcion cerrar-sesion">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        @else
            <div class="acciones-invitado">
                <a href="{{ route('login') }}" class="btn-login">Iniciar sesión</a>
            </div>
        @endauth
    </header>

    <main class="catalogo">

        <section class="catalogo-encabezado">
            <h1>Encuentra lo que necesitas</h1>

            <p>
                Explora profesionales, proveedores y usuarios registrados en ArquiServi.
            </p>
        </section>

        <section class="buscador">
            <form action="{{ route('usuarios.buscar') }}" method="GET">

                <div class="campo-busqueda">
                    <input
                        type="text"
                        name="buscar"
                        value="{{ $busqueda }}"
                        placeholder="Buscar por nombre, profesión, especialidad, material o ubicación..."
                    >

                    <button type="submit">Buscar</button>
                </div>

                <div class="filtros">

                    <select name="tipo">
                        <option value="">Todos los perfiles</option>
                        <option value="usuario" {{ $tipo === 'usuario' ? 'selected' : '' }}>
                            Usuarios
                        </option>
                        <option value="profesional" {{ $tipo === 'profesional' ? 'selected' : '' }}>
                            Profesionales
                        </option>
                        <option value="proveedor" {{ $tipo === 'proveedor' ? 'selected' : '' }}>
                            Proveedores
                        </option>
                    </select>

                    <select name="profesion_id" id="profesion_id">
                        <option value="">Todas las profesiones</option>

                        @foreach($profesiones as $profesion)
                            <option value="{{ $profesion->id }}"
                                {{ $profesionId == $profesion->id ? 'selected' : '' }}>
                                {{ $profesion->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <select name="especialidad_id" id="especialidad_id">
                        <option value="">Todas las especialidades</option>

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

                    @if($busqueda || $tipo || $profesionId || $especialidadId)
                        <a href="{{ route('usuarios.buscar') }}" class="btn-limpiar">
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
                                    {{ ucfirst($usuario->rol->nombre) }}
                                </span>

                                <h3>
                                    {{ $usuario->nombre }}
                                    {{ $usuario->apellido_paterno }}
                                </h3>

                                @if($usuario->rol->nombre === 'profesional' && $usuario->profesional)

                                    @if($usuario->profesional->profesiones->isNotEmpty())
                                        <p class="usuario-profesion">
                                            {{ $usuario->profesional->profesiones->pluck('nombre')->join(', ') }}
                                        </p>
                                    @endif

                                    @if($usuario->profesional->especialidades->isNotEmpty())
                                        <p class="usuario-especialidad">
                                            {{ $usuario->profesional->especialidades->pluck('nombre')->join(', ') }}
                                        </p>
                                    @endif

                                @elseif($usuario->rol->nombre === 'proveedor' && $usuario->proveedor)

                                    @if($usuario->proveedor->materiales->isNotEmpty())
                                        <p class="usuario-profesion">
                                            {{ $usuario->proveedor->materiales->pluck('nombre')->take(3)->join(', ') }}
                                        </p>
                                    @endif

                                @endif

                                @if($usuario->ubicacion)
                                    <p class="usuario-ubicacion">
                                        📍 {{ $usuario->ubicacion }}
                                    </p>
                                @endif

                                @php
                                    $descripcion = $usuario->descripcion;

                                    if (!$descripcion && $usuario->rol->nombre === 'profesional') {
                                        $descripcion = $usuario->profesional?->descripcion;
                                    }

                                    if (!$descripcion && $usuario->rol->nombre === 'proveedor') {
                                        $descripcion = $usuario->proveedor?->descripcion;
                                    }
                                @endphp

                                @if($descripcion)
                                    <p class="usuario-descripcion">
                                        {{ \Illuminate\Support\Str::limit($descripcion, 120) }}
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
                            <span class="pagina-deshabilitada">Anterior</span>
                        @else
                            <a href="{{ $usuarios->previousPageUrl() }}">Anterior</a>
                        @endif

                        <span class="pagina-actual">
                            Página {{ $usuarios->currentPage() }} de {{ $usuarios->lastPage() }}
                        </span>

                        @if($usuarios->hasMorePages())
                            <a href="{{ $usuarios->nextPageUrl() }}">Siguiente</a>
                        @else
                            <span class="pagina-deshabilitada">Siguiente</span>
                        @endif

                    </div>
                @endif

            @else

                <div class="sin-resultados">
                    <h3>No encontramos perfiles</h3>
                    <p>Intenta realizar otra búsqueda o cambiar los filtros.</p>
                </div>

            @endif

        </section>

    </main>

    <footer class="pie">
        <p>© 2026 ArquiServi. Todos los derechos reservados.</p>
    </footer>

</body>

</html>