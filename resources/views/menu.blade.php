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

    <title>Inicio - ArquiServi</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/menu.css') }}"
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
    <img
        src="{{ asset('encabezado2.png') }}"
        class="logo"
        alt="ArquiServi"
    >

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

<h2 class="bienvenida">
    Bienvenido(a)
</h2>

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
    <h2 class="bienvenida">
        {{ auth()->user()->nombre }}
    </h2>
@endauth

<section class="contenedor-principal">
    <section class="contenedor-form">
        <label class="etiqueta">
            ¿Qué servicio necesitas hoy?
        </label>

        <select class="form-control">
            <option disabled selected>
                Selecciona tu servicio requerido
            </option>
        </select>

        <a
            href="{{ route('usuarios.buscar') }}"
            class="btn btn-perfiles"
        >
            Ver perfiles
        </a>

        @guest
            <label class="etiqueta">
                Inicia sesión para ver detalles de tu cuenta
            </label>

            <button class="btn">
                <a href="{{ route('login') }}">
                    Iniciar sesión
                </a>
            </button>
        @endguest
    </section>

    <section class="contenedor-imagen">
        <img
            src="{{ asset('menu.png') }}"
            alt="Imagen de bienvenida"
            class="imagen-bienvenida"
        >
    </section>
</section>

<section class="info-arquiservi">
    <div class="bloque">
        <div class="bloque-contenido">
            <div class="bloque-texto">
                <h3 class="subtitulo">
                    ¿Qué es ArquiServi?
                </h3>

                <p class="parrafo">
                    ArquiServi es la plataforma digital que concentra y conecta el entorno de la arquitectura, construcción, remodelación y servicios relacionados. Es el medio de encuentro donde usuarios con profesionales de la industria de la construcción, arquitectura e ingeniería, proveedores de materiales e insumos y servicios relacionados conectan de manera directa, eficiente y transparente.
                </p>
            </div>

            <div class="bloque-imagen">
                <img
                    src="{{ asset('images/1_QueEs.png') }}"
                    alt="¿Qué es ArquiServi?"
                >
            </div>
        </div>
    </div>

    <div class="bloque alterno">
        <div class="bloque-contenido">
            <div class="bloque-imagen">
                <img
                    src="{{ asset('images/2_Finalidad.png') }}"
                    alt="Finalidad de ArquiServi"
                >
            </div>

            <div class="bloque-texto">
                <h3 class="subtitulo">
                    Finalidad
                </h3>

                <p class="parrafo">
                    Está enfocada en el contacto rápido entre particulares que necesitan obras/remodelaciones y profesionales o contratistas, materiales y servicios relacionados al sector. Nuestra finalidad es democratizar y simplificar la gestión de proyectos e insumos de construcción y diseño, facilitando la visibilidad de talento profesional, y contacto directo entre las partes sin intermediarios.
                </p>
            </div>
        </div>
    </div>

    <div class="bloque">
        <div class="bloque-contenido">
            <div class="bloque-texto">
                <h3 class="subtitulo">
                    Misión
                </h3>

                <p class="parrafo">
                    Conectar e integrar a las personas a crear el hogar en el que desean vivir, facilitándolo de forma confiable y mejor la experiencia en el diseño técnico de construcción y servicios relacionados en el hogar, enlazando a profesionales, prestadores de servicios relacionados, mano de obra calificada, brindando soluciones en espacios habitables a través de una plataforma digital intuitiva.
                </p>
            </div>

            <div class="bloque-imagen">
                <img
                    src="{{ asset('images/3_Mision.png') }}"
                    alt="Misión de ArquiServi"
                >
            </div>
        </div>
    </div>

    <div class="bloque alterno">
        <div class="bloque-contenido">
            <div class="bloque-imagen">
                <img
                    src="{{ asset('images/4_Vision.png') }}"
                    alt="Visión de ArquiServi"
                >
            </div>

            <div class="bloque-texto">
                <h3 class="subtitulo">
                    Visión
                </h3>

                <p class="parrafo">
                    Ser el directorio global y digital de referencia en el sector para que tu vida sea sencilla, siendo imprescindible para integrar y atender las necesidades de nuestros usuarios, con calidad, confianza, rapidez y servicio profesional en la contratación de servicios, optimizando la cadena de valor.
                </p>
            </div>
        </div>
    </div>

    <div class="bloque">
        <div class="bloque-contenido">
            <div class="bloque-texto">
                <h3 class="subtitulo">
                    Objetivos
                </h3>

                <ul class="lista">
                    <li>
                        Simplificar la búsqueda de profesionales éticos mediante portafolios visuales y reseñas verificadas y calificadas.
                    </li>

                    <li>
                        Reducir el tiempo de investigación técnica para identificar despachos de arquitectura, directores responsables de obra y trámites ante instancias gubernamentales.
                    </li>

                    <li>
                        Ser un canal impulsador para proveedores de la construcción y servicios relacionados.
                    </li>

                    <li>
                        Facilitar a los profesionales herramientas y mecanismo de gestión comercial y visibilidad de servicios a prestar.
                    </li>

                    <li>
                        Conectar mediante suscripciones y registros de visibilidad para profesionales, prestadores de servicios y materiales en negociación directa.
                    </li>

                    <li>
                        Generar una base de datos de materiales calificados por criterios técnicos, de alta calidad, sustentabilidad y aplicación.
                    </li>

                    <li>
                        Fomentar la profesionalización e inclusión digital de pequeñas empresas y técnicos de la construcción.
                    </li>
                </ul>
            </div>

            <div class="bloque-imagen">
                <img
                    src="{{ asset('images/5_Objetivos.png') }}"
                    alt="Objetivos de ArquiServi"
                >
            </div>
        </div>
    </div>

    <div class="bloque alterno">
        <div class="bloque-contenido">
            <div class="bloque-imagen">
                <img
                    src="{{ asset('images/6_Valores.png') }}"
                    alt="Valores de ArquiServi"
                >
            </div>

            <div class="bloque-texto">
                <h3 class="subtitulo">
                    Valores
                </h3>

                <ul class="lista">
                    <li>
                        Transparencia: Promovemos relaciones directas, claras y honestas entre clientes, profesionales y proveedores.
                    </li>

                    <li>
                        Colaboración: Creemos en la fuerza de la comunidad para potenciar el crecimiento técnico y comercial del sector.
                    </li>

                    <li>
                        Calidad y Excelencia: Incentivamos el trabajo bien hecho a través de portafolios verificables y evaluaciones de experiencias reales.
                    </li>

                    <li>
                        Innovación: Digitalizamos procesos tradicionales de búsqueda, contacto y selección en la industria de la edificación.
                    </li>

                    <li>
                        Inclusión Sectorial: Brindamos las mismas oportunidades de visibilidad tanto a grandes empresas de servicios como a profesionales e independientes.
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="bloque">
        <div class="bloque-contenido">
            <div class="bloque-texto">
                <h3 class="subtitulo">
                    ¿Por qué elegirnos?
                </h3>

                <h4 class="subapartado">
                    Para Clientes y Usuarios
                </h4>

                <ul class="lista">
                    <li>
                        Todo en un solo lugar: Encuentra desde el arquitecto para tu proyecto hasta el proveedor del material y servicios de reparación y remodelación que necesitas.
                    </li>

                    <li>
                        Decisiones informadas: Explora portafolios visuales, catálogos actualizados y opiniones de otros usuarios.
                    </li>

                    <li>
                        Contacto directo: Negocia, cotiza y acuerda sin intermediarios ni comisiones ocultas.
                    </li>
                </ul>

                <h4 class="subapartado">
                    Para Profesionales
                </h4>

                <ul class="lista">
                    <li>
                        Vitrina profesional: Exhibe tu portafolio, especialidad y zona de cobertura ante clientes potenciales.
                    </li>

                    <li>
                        Reputación digital: Construye credibilidad en el sector a través de valoraciones y experiencias reales.
                    </li>

                    <li>
                        Red de alianzas: Localiza proveedores de materiales cerca de tus zonas de obra.
                    </li>
                </ul>

                <h4 class="subapartado">
                    Para Proveedores
                </h4>

                <ul class="lista">
                    <li>
                        Catálogo activo: Publica tus insumos y productos para que sean vistos por profesionales en etapa de especificación.
                    </li>

                    <li>
                        Alcance local y regional: Posiciona tu negocio en las zonas de trabajo donde entregas material.
                    </li>

                    <li>
                        Crecimiento B2B y B2C: Impacta tanto al cliente final como a los contratistas y despachos de arquitectura.
                    </li>
                </ul>
            </div>

            <div class="bloque-imagen">
                <img
                    src="{{ asset('images/7_PorqueElegirnos.png') }}"
                    alt="¿Por qué elegirnos?"
                >
            </div>
        </div>
    </div>
</section>

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