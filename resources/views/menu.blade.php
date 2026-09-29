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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <header class="encabezado">
    <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">


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

            @if(in_array(auth()->user()->rol->nombre, ['usuario', 'profesional']))

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

                <input type="hidden" id="csrf-notificaciones" value="{{ csrf_token() }}">

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

                        @forelse(auth()->user()->notifications as $notificacion)

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

                <button type="button" class="perfil-boton" id="btnPerfil">

                    <div class="perfil-avatar">

                        @if(auth()->user()->foto_perfil)

                            <img
                                src="{{ asset('storage/' . auth()->user()->foto_perfil) }}"
                                alt="Foto de perfil"
                            >

                        @else

                            {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}

                        @endif

                    </div>

                    <span class="perfil-nombre">
                        {{ auth()->user()->nombre }}
                    </span>

                    <span class="perfil-flecha">▼</span>

                </button>

                <div class="perfil-dropdown" id="perfilDropdown">

                    <div class="perfil-info">

                        <strong>
                            {{ auth()->user()->nombre }}
                            {{ auth()->user()->apellido_paterno }}
                        </strong>

                        <span>
                            {{ auth()->user()->correo }}
                        </span>

                    </div>

                    <a href="{{ route('perfil') }}" class="perfil-opcion">
                        Mi Perfil
                    </a>

                    @if(in_array(auth()->user()->rol->nombre, ['usuario', 'profesional']))

                        <a href="{{ route('solicitudes.mias') }}" class="perfil-opcion">
                            Mis solicitudes
                        </a>

                    @endif

                    @if(in_array(auth()->user()->rol->nombre, ['profesional', 'proveedor']))

                        <a href="{{ route('solicitudes.recibidas') }}" class="perfil-opcion">
                            Solicitudes recibidas
                        </a>

                    @endif

                    <form action="{{ route('logout') }}" method="POST">

                        @csrf

                        <button type="submit" class="perfil-opcion cerrar-sesion">
                            Cerrar sesión
                        </button>

                    </form>

                </div>

            </div>

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

            <a href="{{ route('usuarios.buscar') }}" class="btn btn-perfiles">
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
        <!-- Bloque 1 -->
        <div class="bloque">
        <div class="bloque-contenido">
            <div class="bloque-texto">
            <h3 class="subtitulo">¿Qué es ArquiServi?</h3>
            <p class="parrafo">ArquiServi es la plataforma digital que concentra y conecta el entorno de la arquitectura, construcción, remodelación y servicios relacionados. Es el medio de encuentro donde usuarios con profesionales de la industria de la construcción, arquitectura e ingeniería, proveedores de materiales e insumos y servicios relacionados conectan de manera directa, eficiente y transparente.</p>
            </div>
            <div class="bloque-imagen">
            <img src="{{ asset('images/Que es.png') }}" alt="¿Qué es ArquiServi?">
            </div>
        </div>
        </div>

        <!-- Bloque 2 -->
        <div class="bloque alterno">
        <div class="bloque-contenido">
            <div class="bloque-imagen">
            <img src="{{ asset('images/Finalidad.png') }}" alt="Finalidad de ArquiServi">
            </div>
            <div class="bloque-texto">
            <h3 class="subtitulo">Finalidad</h3>
            <p class="parrafo">Está enfocada en el contacto rápido entre particulares que necesitan obras/remodelaciones y profesionales o contratistas, materiales y servicios relacionados al sector. Nuestra finalidad es democratizar y simplificar la gestión de proyectos e insumos de construcción y diseño, facilitando la visibilidad de talento profesional, y contacto directo entre las partes sin intermediarios.</p>
            </div>
        </div>
        </div>

        <!-- Bloque 3 -->
        <div class="bloque">
        <div class="bloque-contenido">
            <div class="bloque-texto">
            <h3 class="subtitulo">Misión</h3>
            <p class="parrafo">Conectar e integrar a las personas a crear el hogar en el que desean vivir, facilitándolo de forma confiable y mejor la experiencia en el diseño técnico de construcción y servicios relacionados en el hogar, enlazando a profesionales, prestadores de servicios relacionados, mano de obra calificada, brindando soluciones en espacios habitables a través de una plataforma digital intuitiva.</p>
            </div>
            <div class="bloque-imagen">
            <img src="{{ asset('images/Mision.png') }}" alt="Misión de ArquiServi">
            </div>
        </div>
        </div>

        <!-- Bloque 4 -->
        <div class="bloque alterno">
        <div class="bloque-contenido">
            <div class="bloque-imagen">
            <img src="{{ asset('images/Vision.png') }}" alt="Visión de ArquiServi">
            </div>
            <div class="bloque-texto">
            <h3 class="subtitulo">Visión</h3>
            <p class="parrafo">Ser el directorio global y digital de referencia en el sector para que tu vida sea sencilla, siendo imprescindible para integrar y atender las necesidades de nuestros usuarios, con calidad, confianza, rapidez y servicio profesional en la contratación de servicios, optimizando la cadena de valor.</p>
            </div>
        </div>
        </div>

        <!-- Bloque 5 -->
        <div class="bloque">
        <div class="bloque-contenido">
            <div class="bloque-texto">
            <h3 class="subtitulo">Objetivos</h3>
            <ul class="lista">
                <li>Simplificar la búsqueda de profesionales éticos mediante portafolios visuales y reseñas verificadas y calificadas.</li>
                <li>Reducir el tiempo de investigación técnica para identificar despachos de arquitectura, directores responsables de obra y trámites ante instancias gubernamentales.</li>
                <li>Ser un canal impulsador para proveedores de la construcción y servicios relacionados.</li>
                <li>Facilitar a los profesionales herramientas y mecanismo de gestión comercial y visibilidad de servicios a prestar.</li>
                <li>Conectar mediante suscripciones y registros de visibilidad para profesionales, prestadores de servicios y materiales en negociación directa.</li>
                <li>Generar una base de datos de materiales calificados por criterios técnicos, de alta calidad, sustentabilidad y aplicación.</li>
                <li>Fomentar la profesionalización e inclusión digital de pequeñas empresas y técnicos de la construcción.</li>
            </ul>
            </div>
            <div class="bloque-imagen">
            <img src="{{ asset('images/Objetivos.png') }}" alt="Misión de ArquiServi">
            </div>
        </div>
        </div>

        <!-- Bloque 6 -->
        <div class="bloque alterno">
        <div class="bloque-contenido">
            <div class="bloque-imagen">
            <img src="{{ asset('images/Valores.png') }}" alt="Valores de ArquiServi">
            </div>
            <div class="bloque-texto">
            <h3 class="subtitulo">Valores</h3>
            <ul class="lista">
                <li>Transparencia: Promovemos relaciones directas, claras y honestas entre clientes, profesionales y proveedores.</li>
                <li>Colaboración: Creemos en la fuerza de la comunidad para potenciar el crecimiento técnico y comercial del sector.</li>
                <li>Calidad y Excelencia: Incentivamos el trabajo bien hecho a través de portafolios verificables y evaluaciones de experiencias reales.</li>
                <li>Innovación: Digitalizamos procesos tradicionales de búsqueda, contacto y selección en la industria de la edificación.</li>
                <li>Inclusión Sectorial: Brindamos las mismas oportunidades de visibilidad tanto a grandes empresas de servicios como a profesionales e independientes.</li>
            </ul>
            </div>
        </div>
        </div>

        <!-- Bloque 7 -->
        <div class="bloque">
        <div class="bloque-contenido">
            <div class="bloque-texto">
            <h3 class="subtitulo">¿Por qué elegirnos?</h3>

            <h4 class="subapartado">Para Clientes y Usuarios</h4>
            <ul class="lista">
                <li>Todo en un solo lugar: Encuentra desde el arquitecto para tu proyecto hasta el proveedor del material y servicios de reparación y remodelación que necesitas.</li>
                <li>Decisiones informadas: Explora portafolios visuales, catálogos actualizados y opiniones de otros usuarios.</li>
                <li>Contacto directo: Negocia, cotiza y acuerda sin intermediarios ni comisiones ocultas.</li>
            </ul>

            <h4 class="subapartado">Para Profesionales</h4>
            <ul class="lista">
                <li>Vitrina profesional: Exhibe tu portafolio, especialidad y zona de cobertura ante clientes potenciales.</li>
                <li>Reputación digital: Construye credibilidad en el sector a través de valoraciones y experiencias reales.</li>
                <li>Red de alianzas: Localiza proveedores de materiales cerca de tus zonas de obra.</li>
            </ul>

            <h4 class="subapartado">Para Proveedores</h4>
            <ul class="lista">
                <li>Catálogo activo: Publica tus insumos y productos para que sean vistos por profesionales en etapa de especificación.</li>
                <li>Alcance local y regional: Posiciona tu negocio en las zonas de trabajo donde entregas material.</li>
                <li>Crecimiento B2B y B2C: Impacta tanto al cliente final como a los contratistas y despachos de arquitectura.</li>
            </ul>
            </div>
            <div class="bloque-imagen">
            <img src="{{ asset('images/Porque elegirnos.png') }}" alt="¿Por qué elegirnos?">
            </div>
        </div>
        </div>

    </section>
    </section>

    <footer class="pie">
        <p> © 2026 ArquiServi. Todos los derechos reservados. </p>
    </footer>
</body>
</html>