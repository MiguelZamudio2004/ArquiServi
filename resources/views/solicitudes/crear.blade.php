<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        Nueva solicitud - ArquiServi
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/solicitudes.css') }}"
    >

    <link
        rel="icon"
        href="{{ asset('icono.png') }}"
        type="image/png"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >

    <script
        src="{{ asset('js/notification.js') }}"
        defer
    ></script>

    <script
        src="{{ asset('js/perfil.js') }}"
        defer
    ></script>
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

                    <span class="texto">
                        Inicio
                    </span>
                </a>
            </li>

            <li>
                <a href="{{ route('usuarios.buscar') }}">
                    <i class="fa-solid fa-folder-open icono"></i>

                    <span class="texto">
                        Catálogo
                    </span>
                </a>
            </li>

            @auth
                @if(in_array(auth()->user()->rol->nombre, ['usuario', 'profesional', 'proveedor']))
                    <li>
                        <a href="{{ route('solicitudes.mias') }}">
                            <i class="fa-solid fa-envelope icono"></i>

                            <span class="texto">
                                Mis solicitudes
                            </span>
                        </a>
                    </li>
                @endif

                @if(in_array(auth()->user()->rol->nombre, ['profesional', 'proveedor']))
                    <li>
                        <a href="{{ route('solicitudes.recibidas') }}">
                            <i class="fa-solid fa-inbox icono"></i>

                            <span class="texto">
                                Solicitudes recibidas
                            </span>
                        </a>
                    </li>
                @endif

                <li class="notificaciones-container">
                    <input
                        type="hidden"
                        id="csrf-notificaciones"
                        value="{{ csrf_token() }}"
                    >

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

                        <span class="texto">
                            Notificaciones
                        </span>
                    </button>

                    <div
                        class="notificaciones-dropdown"
                        id="notificacionesDropdown"
                    >
                        <div class="notificaciones-header">
                            <h3>
                                Notificaciones
                            </h3>
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

                            <span class="texto">
                                Cerrar sesión
                            </span>
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
                @php
                    $materialesSeleccionados = array_map(
                        'intval',
                        old('materiales', [])
                    );
                @endphp

                <div class="campo">
                    <label>
                        Materiales o productos
                    </label>

                    <p class="campo-ayuda">
                        Selecciona uno o varios materiales.
                    </p>

                    <div class="materiales-solicitud">
                        @forelse($destinatario->proveedor->materiales as $material)
                            @if($material->activo && $material->pivot->disponible)
                                <label
                                    class="material-solicitud-opcion"
                                    for="material_{{ $material->id }}"
                                >
                                    <input
                                        type="checkbox"
                                        name="materiales[]"
                                        id="material_{{ $material->id }}"
                                        value="{{ $material->id }}"
                                        {{ in_array((int) $material->id, $materialesSeleccionados, true) ? 'checked' : '' }}
                                    >

                                    <span class="material-solicitud-contenido">
                                        <span class="material-solicitud-check">
                                            <i class="fa-solid fa-check"></i>
                                        </span>

                                        <span class="material-solicitud-info">
                                            <strong class="material-solicitud-nombre">
                                                {{ $material->nombre }}
                                            </strong>

                                            @if($material->categoria)
                                                <small class="material-solicitud-categoria">
                                                    {{ $material->categoria }}
                                                </small>
                                            @endif
                                        </span>
                                    </span>
                                </label>
                            @endif
                        @empty
                            <p class="sin-materiales">
                                Este proveedor no tiene materiales disponibles.
                            </p>
                        @endforelse
                    </div>
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
    <div class="pie-contenido">
        <div class="pie-marca">
            <a
                href="{{ route('menu') }}"
                class="pie-logo-enlace"
                aria-label="ArquiServi"
            >
                <img
                    src="{{ asset('encabezado2.png') }}"
                    alt="ArquiServi"
                    class="pie-logo-arquiservi"
                >
            </a>

            <p class="pie-eslogan">
                Conectando personas, construyendo espacios.
            </p>

            <p class="pie-descripcion">
                Encuentra profesionales y proveedores para llevar tus proyectos a la realidad de forma sencilla, segura y confiable.
            </p>
        </div>

        <!-- COLUMNAS CENTRALES -->
        <div class="pie-columnas">
            <div class="pie-columna">
                <h3>
                    ArquiServi
                </h3>

                <nav class="pie-enlaces">
                    <a
                        href="{{ route('acerca') }}"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-circle-info"></i>

                        <span>
                            Acerca de
                        </span>
                    </a>

                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-briefcase"></i>

                        <span>
                            Servicios
                        </span>
                    </a>

                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-users"></i>

                        <span>
                            Profesionales
                        </span>
                    </a>

                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-store"></i>

                        <span>
                            Proveedores
                        </span>
                    </a>
                </nav>
            </div>

            <div class="pie-columna">
                <h3>
                    Ayuda
                </h3>

                <nav class="pie-enlaces">
                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-circle-question"></i>

                        <span>
                            Centro de ayuda
                        </span>
                    </a>

                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-envelope"></i>

                        <span>
                            Contacto
                        </span>
                    </a>

                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-shield-halved"></i>

                        <span>
                            Seguridad
                        </span>
                    </a>
                </nav>
            </div>

            <div class="pie-columna">
                <h3>
                    Legal
                </h3>

                <nav class="pie-enlaces">
                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-file-contract"></i>

                        <span>
                            Términos y condiciones
                        </span>
                    </a>

                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-lock"></i>

                        <span>
                            Privacidad
                        </span>
                    </a>

                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-cookie-bite"></i>

                        <span>
                            Cookies
                        </span>
                    </a>
                </nav>
            </div>

            <div class="pie-redes">
                <span class="pie-subtitulo">
                    Síguenos
                </span>

                <div class="pie-redes-lista">
                    <a
                        href="#"
                        class="pie-red-social"
                        aria-label="Facebook"
                    >
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>

                    <a
                        href="#"
                        class="pie-red-social"
                        aria-label="Instagram"
                    >
                        <i class="fa-brands fa-instagram"></i>
                    </a>

                    <a
                        href="#"
                        class="pie-red-social"
                        aria-label="LinkedIn"
                    >
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>

                    <a
                        href="#"
                        class="pie-red-social"
                        aria-label="GitHub"
                    >
                        <i class="fa-brands fa-github"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="pie-derecha">
            <div class="pie-colaboradores">
                <span class="pie-subtitulo">
                    Colaboradores
                </span>

                <div class="pie-logos">
                    <img
                        src="{{ asset('footer/Logo_Labsol.png') }}"
                        alt="LABSOL Network"
                        class="logo-footer logo-labsol"
                    >

                    <img
                        src="{{ asset('footer/Logo_GPLv3.png') }}"
                        alt="GPLv3 Free Software"
                        class="logo-footer logo-gpl"
                    >
                </div>
            </div>
        </div>
    </div>

    <div class="pie-separador"></div>

    <div class="pie-inferior">
        <div class="pie-copyright">
            <p>
                © 2026 ArquiServi. Todos los derechos reservados.
            </p>
        </div>

        <div class="pie-inferior-enlaces">
            <a href="#">
                Aviso de privacidad
            </a>

            <span class="pie-punto"></span>

            <a href="#">
                Términos
            </a>

            <span class="pie-punto"></span>

            <a href="#">
                Contacto
            </a>
        </div>
    </div>
</footer>

</body>

</html>