<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inicio</title>
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
    <script src="{{ asset('js/notification.js') }}" defer></script>   
    <script src="{{ asset('js/perfil.js')}}" defer></script>
</head>

<body>
    <header class="encabezado">
    <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">

    @auth
    <div class="acciones-usuario">
            
            <div class="notificaciones-container">
            <input type="hidden" id="csrf-notificaciones" value="{{ csrf_token() }}">

            <button type="button" class="campana" id="btnNotificaciones">
                <span class="campana-icono">🔔</span>

                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="contador-notificaciones" id="contadorNotificaciones">{{ auth()->user()->unreadNotifications->count() }}</span>
                @endif
            </button>

            <div class="notificaciones-dropdown" id="notificacionesDropdown">
                <div class="notificaciones-header">
                    <h3>Notificaciones</h3>
                </div>

                <div class="notificaciones-lista">
                    @forelse(auth()->user()->notifications as $notificacion)
                        <button type="button" class="notificacion {{ $notificacion->read_at ? 'leida' : 'no-leida' }}" data-url="{{ route('notificaciones.leer', $notificacion->id) }}">
                            <div class="notificacion-contenido">
                                <strong class="notificacion-titulo">{{ $notificacion->data['titulo'] ?? 'Notificación' }}</strong>
                                <p class="notificacion-mensaje">{{ $notificacion->data['mensaje'] ?? '' }}</p>
                                <small class="notificacion-fecha">{{ $notificacion->created_at->diffForHumans() }}</small>
                            </div>

                            @if(!$notificacion->read_at)
                                <span class="indicador-no-leida"></span>
                            @endif
                        </button>
                    @empty
                        <div class="sin-notificaciones">No tienes notificaciones.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="perfil-container">
            <button type="button" class="perfil-boton" id="btnPerfil">
                <div class="perfil-avatar">@if(auth()->user()->foto_perfil)
        <img src="{{ asset('storage/' . auth()->user()->foto_perfil) }}" alt="Foto de perfil">
        @else
            {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
    @endif
</div>
                <span class="perfil-nombre">{{ auth()->user()->nombre }}</span>
                <span class="perfil-flecha">▼</span>
            </button>

            <div class="perfil-dropdown" id="perfilDropdown">
                <div class="perfil-info">
                    <strong>{{ auth()->user()->nombre }} {{ auth()->user()->apellido_paterno }}</strong>
                    <span>{{ auth()->user()->correo }}</span>
                </div>

                <a href="{{ route('perfil') }}" class="perfil-opcion">Mi Perfil </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="perfil-opcion cerrar-sesion">Cerrar sesión</button>
                </form>
            </div>
        </div>

    </div>
    @endauth
</header>
    
    <h2 class="bienvenida"> Bienvenido(a)</h2>
    <h2 class="bienvenida">
        <span style="--i:1">A</span>
        <span style="--i:2">R</span>
        <span style="--i:3">Q</span>
        <span style="--i:4">U</span>
        <span style="--i:5">I</span>
        <span style="--i:6">S</span>
        <span style="--i:7">E</span>
        <span style="--i:8">R</span>
        <span style="--i:9">V</span>
        <span style="--i:10">I</span>
    </h2>
    @auth
    <h2 class="bienvenida">{{ auth() ->user()->nombre }}</h2>
    @endauth

    <section class="contenedor-principal">
        <section class="contenedor-form">
            <label class="etiqueta">¿Qué servicio necesitas hoy?</label>
            <select class="form-control">
            <option disabled selected>Selecciona tu servicio requerido</option>
            </select>
            <a href="{{ route('usuarios.buscar') }}" class="btn btn-perfiles">Ver perfiles</a>
            @guest
            <label class="etiqueta">Inicia sesión para ver detalles de tu cuenta</label>
            <button class="btn"><a href="{{ route('login') }}">Iniciar sesión</a></button>
            @endguest
        </section>

        <section class="contenedor-imagen">
            <img src="{{ asset('menu.png') }}" alt="Imagen de bienvenida" class="imagen-bienvenida">
        </section>
    </section>

    <section class="info-arquiservi">
        <!-- Bloque 1 -->
        <div class="bloque">
        <div class="bloque-contenido">
            <div class="bloque-texto">
            <h3 class="subtitulo">¿Qué es ArquiServi?</h3>
            <p class="parrafo">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.  Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>
            <div class="bloque-imagen">
            <img src="{{ asset('prueba.png') }}" alt="¿Qué es ArquiServi?">
            </div>
        </div>
        </div>

        <!-- Bloque 2 -->
        <div class="bloque alterno">
        <div class="bloque-contenido">
            <div class="bloque-imagen">
            <img src="{{ asset('prueba.png') }}" alt="Finalidad de ArquiServi">
            </div>
            <div class="bloque-texto">
            <h3 class="subtitulo">Finalidad</h3>
            <p class="parrafo">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.  Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>
        </div>
        </div>

        <!-- Bloque 3 -->
        <div class="bloque">
        <div class="bloque-contenido">
            <div class="bloque-texto">
            <h3 class="subtitulo">Misión</h3>
            <p class="parrafo">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.  Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>
            <div class="bloque-imagen">
            <img src="{{ asset('prueba.png') }}" alt="Misión de ArquiServi">
            </div>
        </div>
        </div>

        <!-- Bloque 4 -->
        <div class="bloque alterno">
        <div class="bloque-contenido">
            <div class="bloque-imagen">
            <img src="{{ asset('prueba.png') }}" alt="Visión de ArquiServi">
            </div>
            <div class="bloque-texto">
            <h3 class="subtitulo">Visión</h3>
            <p class="parrafo">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.  Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>
        </div>
        </div>

        <!-- Bloque 5 -->
        <div class="bloque">
        <div class="bloque-contenido">
            <div class="bloque-texto">
            <h3 class="subtitulo">Valores</h3>
            <p class="parrafo">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.  Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>
            <div class="bloque-imagen">
            <img src="{{ asset('prueba.png') }}" alt="Valores de ArquiServi">
            </div>
        </div>
        </div>

        <!-- Bloque 6 -->
        <div class="bloque alterno">
        <div class="bloque-contenido">
            <div class="bloque-imagen">
            <img src="{{ asset('prueba.png') }}" alt="¿Por qué elegirnos?">
            </div>
            <div class="bloque-texto">
            <h3 class="subtitulo">¿Por qué elegirnos?</h3>
            <p class="parrafo">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.  Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>
        </div>
        </div>
</section>


</body>

<footer class="pie">
    <p>© 2026 ArquiServi. Todos los derechos reservados.</p>
</footer>

</html>