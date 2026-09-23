<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
</head>

<body>

    <header class="encabezado">
        <a href="{{ route('menu') }}">
            <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
        </a>

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
                        <strong>{{ auth()->user()->nombre }} {{ auth()->user()->apellido_paterno }}</strong>
                        <span>{{ auth()->user()->correo }}</span>
                    </div>

                    <a href="{{ route('perfil') }}" class="perfil-opcion">Mi perfil</a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="perfil-opcion cerrar-sesion">Cerrar sesión</button>
                    </form>
                </div>
            </div>

        </div>
    </header>

    <main class="perfil">

        <section class="perfil-cabecera">
            <div class="perfil-foto" tabindex="0">
                @if($usuario->foto_perfil)
                    <img src="{{ asset('storage/' . $usuario->foto_perfil) }}" alt="Foto de perfil">
                @else
                    {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                @endif
            </div>

            <h1>
                {{ $usuario->nombre }}
                {{ $usuario->apellido_paterno }}
                {{ $usuario->apellido_materno }}
            </h1>

            <p>{{ strtoupper($usuario->rol->nombre) }}</p>

            @if($usuario->descripcion)
                <div class="perfil-descripcion">
                    <p>{{ $usuario->descripcion }}</p>
                </div>
            @endif
        </section>

        <section class="perfil-datos">
            <div>
                <span>Correo electrónico</span>
                <strong>{{ $usuario->correo }}</strong>
            </div>

            <div>
                <span>Teléfono</span>
                <strong>{{ $usuario->telefono ?? 'No especificado' }}</strong>
            </div>

            <div>
                <span>Ubicación</span>
                <strong>{{ $usuario->ubicacion ?? 'No especificada' }}</strong>
            </div>

            <div>
                <span>Tipo de cuenta</span>
                <strong>{{ ucfirst($usuario->rol->nombre) }}</strong>
            </div>
        </section>

        @if($usuario->rol->nombre === 'profesional')

            <section class="perfil-seccion">
                <h2>Información profesional</h2>

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
    <h2>Servicios que ofrece</h2>

    @if($usuario->profesional && $usuario->profesional->servicios->isNotEmpty())
        <p>{{ $usuario->profesional->servicios->pluck('nombre')->join(', ') }}</p>
    @else
        <p>Aún no has registrado servicios.</p>
    @endif
</section>
            

            <section class="perfil-seccion">
                <h2>Descripción profesional</h2>

                <p>
                    {{ $usuario->profesional?->descripcion ?? 'Sin descripción profesional.' }}
                </p>
            </section>

            @if($usuario->profesional?->portafolio_url)
                <section class="perfil-seccion">
                    <h2>Portafolio</h2>

                    <p>
                        <a href="{{ $usuario->profesional->portafolio_url }}"
                            target="_blank"
                            rel="noopener noreferrer">
                            Ver portafolio
                        </a>
                    </p>
                </section>
            @endif

        @elseif($usuario->rol->nombre === 'proveedor')

            <section class="perfil-seccion">
                <h2>Información del proveedor</h2>

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
                <h2>Materiales o productos</h2>

                @if($usuario->proveedor && $usuario->proveedor->materiales->isNotEmpty())
                    <p>
                        @foreach($usuario->proveedor->materiales as $material)
                            {{ $material->nombre }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </p>
                @else
                    <p>No hay materiales registrados.</p>
                @endif
            </section>

        @endif

        <a href="{{ route('perfil.editar') }}" class="btn-editar">
            Editar perfil
        </a>

    </main>

    <footer class="pie">
        <p>© 2026 ArquiServi. Todos los derechos reservados.</p>
    </footer>

    <script src="{{ asset('js/notification.js') }}" defer></script>
    <script src="{{ asset('js/perfil.js') }}" defer></script>

</body>
</html>