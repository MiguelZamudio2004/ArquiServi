<!DOCTYPE html>
<html lang="es">

<head>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta charset="UTF-8">

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>Acerca de - ArquiServi</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/menu.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/acerca.css') }}"
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
</head>

<body>
<header class="encabezado">
    <a
        href="{{ route('menu') }}"
        class="acerca-logo-enlace"
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
            @if(auth()->user()->rol && auth()->user()->rol->nombre === 'administrador')
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="volver-admin-header"
                    aria-label="Volver al panel de administración"
                    title="Panel de administración"
                >
                    <span class="volver-admin-icono">
                        <i class="fa-solid fa-user-shield"></i>
                    </span>

                    <span class="volver-admin-texto">
                        Panel admin
                    </span>
                </a>
            @else
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
            @endif
        </div>
    @endauth
</header>

<main class="acerca-main">
    <section class="acerca-presentacion">
        <span class="acerca-mini-titulo">
            Equipo de desarrollo
        </span>

        <h1>
            Acerca de nosotros
        </h1>

        <p>
            ArquiServi fue desarrollado mediante el trabajo colaborativo
            de un equipo encargado de diseñar e implementar tanto la
            lógica interna de la plataforma como su interfaz visual,
            buscando ofrecer una experiencia funcional, clara y
            accesible para los usuarios.
        </p>
    </section>

    <section class="acerca-proyecto">
        <div class="proyecto-icono">
            <i class="fa-solid fa-laptop-code"></i>
        </div>

        <div class="proyecto-informacion">
            <span class="acerca-mini-titulo">
                ArquiServi
            </span>

            <h2>
                Desarrollo del proyecto
            </h2>

            <p>
                ArquiServi es una plataforma digital enfocada en
                conectar usuarios con profesionales, prestadores de
                servicios y proveedores relacionados con la
                arquitectura, construcción, remodelación y servicios
                asociados.
            </p>

            <p>
                Durante el desarrollo se trabajó en diferentes áreas
                del sistema, incluyendo backend, frontend, base de
                datos, autenticación, gestión de perfiles,
                solicitudes, notificaciones, diseño de interfaces y
                adaptación para diferentes dispositivos.
            </p>

            <div class="tecnologias">
                <span>
                    <i class="fa-brands fa-laravel"></i>
                    Laravel
                </span>

                <span>
                    <i class="fa-brands fa-php"></i>
                    PHP
                </span>

                <span>
                    <i class="fa-solid fa-database"></i>
                    MySQL
                </span>

                <span>
                    <i class="fa-brands fa-html5"></i>
                    HTML
                </span>

                <span>
                    <i class="fa-brands fa-css3-alt"></i>
                    CSS
                </span>

                <span>
                    <i class="fa-brands fa-js"></i>
                    JavaScript
                </span>
            </div>
        </div>
    </section>

    <section class="equipo-seccion">
        <div class="equipo-encabezado">
            <span class="acerca-mini-titulo">
                Programadores
            </span>

            <h2>
                Equipo de desarrollo
            </h2>

            <p>
                El desarrollo de ArquiServi se dividió principalmente
                entre el desarrollo backend y frontend, trabajando de
                manera conjunta para integrar la lógica del sistema con
                la interfaz utilizada por los usuarios.
            </p>
        </div>

        <div class="equipo-grid">
            <article class="programador-card">
                <div class="programador-cabecera">
                    <div class="programador-avatar">
                        JM
                    </div>

                    <div class="programador-nombre">
                        <h3>
                            Jose Miguel Cruz Zamudio
                        </h3>

                        <span>
                            Desarrollador Backend
                        </span>
                    </div>
                </div>

                <p class="programador-descripcion">
                    Encargado principalmente del desarrollo backend de
                    ArquiServi, trabajando con Laravel y PHP en la lógica
                    del sistema, administración de la base de datos,
                    autenticación, perfiles de usuario, profesionales y
                    proveedores, solicitudes, validaciones, notificaciones
                    y diferentes funcionalidades de la plataforma.
                </p>

                <div class="programador-datos">
                    <div class="programador-dato">
                        <i class="fa-solid fa-graduation-cap"></i>

                        <div>
                            <span>
                                Carrera
                            </span>

                            <strong>
                                Ingeniería en Sistemas Computacionales
                            </strong>
                        </div>
                    </div>

                    <div class="programador-dato">
                        <i class="fa-solid fa-envelope"></i>

                        <div>
                            <span>
                                Correo electrónico
                            </span>

                            <a
                                href="mailto:josemiguelzamudio47@gmail.com"
                                class="programador-correo"
                            >
                                josemiguelzamudio47@gmail.com
                            </a>
                        </div>
                    </div>

                    <div class="programador-dato">
                        <i class="fa-solid fa-code"></i>

                        <div>
                            <span>
                                Área principal
                            </span>

                            <strong>
                                Desarrollo Backend
                            </strong>
                        </div>
                    </div>

                    <div class="programador-dato">
                        <i class="fa-solid fa-server"></i>

                        <div>
                            <span>
                                Tecnologías principales
                            </span>

                            <strong>
                                Laravel, PHP y MySQL
                            </strong>
                        </div>
                    </div>
                </div>

                <div class="programador-enlaces">
                    <a
                        href="https://github.com/MiguelZamudio2004"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <i class="fa-brands fa-github"></i>
                        Ver GitHub
                    </a>
                </div>
            </article>

            <article class="programador-card">
                <div class="programador-cabecera">
                    <div class="programador-avatar">
                        AR
                    </div>

                    <div class="programador-nombre">
                        <h3>
                            Aracely Rodriguez Contreras
                        </h3>

                        <span>
                            Desarrolladora Frontend
                        </span>
                    </div>
                </div>

                <p class="programador-descripcion">
                    Encargada principalmente del desarrollo frontend de
                    ArquiServi, trabajando en la construcción y diseño de
                    las interfaces visuales, organización de los
                    elementos de las vistas, estilos, adaptación
                    responsive y experiencia de usuario dentro de la
                    plataforma.
                </p>

                <div class="programador-datos">
                    <div class="programador-dato">
                        <i class="fa-solid fa-graduation-cap"></i>

                        <div>
                            <span>
                                Carrera
                            </span>

                            <strong>
                                Ingeniería en Sistemas Computacionales
                            </strong>
                        </div>
                    </div>

                    <div class="programador-dato">
                        <i class="fa-solid fa-envelope"></i>

                        <div>
                            <span>
                                Correo electrónico
                            </span>

                            <a
                                href="mailto:aracelyrodriguez9417@gmail.com"
                                class="programador-correo"
                            >
                                aracelyrodriguez9417@gmail.com
                            </a>
                        </div>
                    </div>

                    <div class="programador-dato">
                        <i class="fa-solid fa-palette"></i>

                        <div>
                            <span>
                                Área principal
                            </span>

                            <strong>
                                Desarrollo Frontend
                            </strong>
                        </div>
                    </div>

                    <div class="programador-dato">
                        <i class="fa-solid fa-display"></i>

                        <div>
                            <span>
                                Tecnologías principales
                            </span>

                            <strong>
                                HTML, CSS y JavaScript
                            </strong>
                        </div>
                    </div>
                </div>

                <div class="programador-enlaces">
                    <a
                        href="https://github.com/aracely-rdz"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <i class="fa-brands fa-github"></i>
                        Ver GitHub
                    </a>
                </div>
            </article>
        </div>
    </section>

    <section class="proyecto-academico">
        <div class="proyecto-academico-icono">
            <i class="fa-solid fa-code"></i>
        </div>

        <div>
            <span class="acerca-mini-titulo">
                Desarrollo de software
            </span>

            <h2>
                Trabajo colaborativo
            </h2>

            <p>
                El desarrollo de ArquiServi combina la implementación
                backend y frontend para construir una plataforma
                funcional, integrando lógica de negocio, persistencia de
                datos, seguridad, diseño de interfaces y adaptación
                responsive en una misma solución.
            </p>
        </div>
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