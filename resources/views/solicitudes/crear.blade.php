<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Nueva solicitud - ArquiServi</title>

    <link rel="stylesheet" href="{{ asset('css/solicitudes.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >

    <script src="{{ asset('js/notification.js') }}" defer></script>
    <script src="{{ asset('js/perfil.js') }}" defer></script>
</head>

<body>

<header class="encabezado">

    <a
        href="{{ route('menu') }}"
        class="logo-link"
    >
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
                        Mi perfil
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
        Nueva solicitud
    </h1>

    @if($errors->any())

        <div class="errores">

            @foreach($errors->all() as $error)

                <p>
                    {{ $error }}
                </p>

            @endforeach

        </div>

    @endif

    <section class="solicitud-form">

        <div class="solicitud-destinatario">

            <span>
                {{ $destinatario->rol->nombre === 'proveedor' ? 'Proveedor' : 'Profesional' }}
            </span>

            <strong>
                {{ $destinatario->nombre }}
                {{ $destinatario->apellido_paterno }}
            </strong>

        </div>

        <form
            action="{{ route('solicitudes.guardar') }}"
            method="POST"
        >

            @csrf

            <input
                type="hidden"
                name="destinatario_id"
                value="{{ $destinatario->id }}"
            >

            @if($destinatario->rol->nombre === 'profesional')

                <div class="campo">

                    <label for="servicio_id">
                        Servicio
                    </label>

                    <select
                        name="servicio_id"
                        id="servicio_id"
                        required
                    >

                        <option value="">
                            Selecciona un servicio
                        </option>

                        @foreach($destinatario->profesional->servicios as $servicio)

                            <option
                                value="{{ $servicio->id }}"
                                {{ old('servicio_id') == $servicio->id ? 'selected' : '' }}
                            >
                                {{ $servicio->nombre }}
                            </option>

                        @endforeach

                    </select>

                </div>

            @endif

            @if($destinatario->rol->nombre === 'proveedor')

                <div class="campo">

                    <label for="material_id">
                        Material o producto
                    </label>

                    <select
                        name="material_id"
                        id="material_id"
                        required
                    >

                        <option value="">
                            Selecciona un material o producto
                        </option>

                        @foreach($destinatario->proveedor->materiales as $material)

                            @if($material->pivot->disponible)

                                <option
                                    value="{{ $material->id }}"
                                    {{ old('material_id') == $material->id ? 'selected' : '' }}
                                >
                                    {{ $material->nombre }}
                                </option>

                            @endif

                        @endforeach

                    </select>

                </div>

            @endif

            <div class="campo">

                <label for="descripcion">
                    Descripción
                </label>

                <textarea
                    name="descripcion"
                    id="descripcion"
                    maxlength="1000"
                    required
                    placeholder="Describe lo que necesitas..."
                >{{ old('descripcion') }}</textarea>

            </div>

            <div class="acciones">

                <a
                    href="{{ route('perfil.publico', $destinatario) }}"
                    class="btn-secundario"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn-principal"
                >
                    Enviar solicitud
                </button>

            </div>

        </form>

    </section>

</main>

<footer class="pie">

    <p>
        © 2026 ArquiServi. Todos los derechos reservados.
    </p>

</footer>

</body>

</html>