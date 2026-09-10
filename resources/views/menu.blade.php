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
</head>

<body>
    <header class="encabezado">
    <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
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
    <h2 class="bienvenida"> {{ Auth::user()->nombre }} </h2>

    <section class="contenedor-principal">
        <section class="contenedor-form">
            <label class="etiqueta">¿Qué servicio necesitas hoy?</label>
            <select class="form-control">
            <option disabled selected>Selecciona tu servicio requerido</option>
            </select>
            <button class="btn">Ver catálogo completo de servicios</button>
            <label class="etiqueta">Inicia sesión para ver detalles de tu cuenta</label>
            <button class="btn"><a href="{{ route('login') }}">Iniciar sesión</a></button>
        </section>

        <section class="contenedor-imagen">
            <img src="{{ asset('menu.png') }}" alt="Imagen de bienvenida" class="imagen-bienvenida">
        </section>
    </section>

</body>

<footer class="pie">
    <p>© 2026 ArquiServi. Todos los derechos reservados.</p>
</footer>

</html>