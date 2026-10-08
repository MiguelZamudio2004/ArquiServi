<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registro - ArquiServi</title>

    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >

    <script src="{{ asset('js/mostrar.js') }}" defer></script>
</head>

<body>
    <header class="encabezado">
        <img
            src="{{ asset('encabezado2.png') }}"
            class="logo"
            alt="ArquiServi"
        >
    </header>

    <main class="register-main">
        <section class="form-register">
            <form action="{{ route('register.store') }}" method="POST">
                @csrf

                <h2 id="subtitulo">Nueva Cuenta</h2>

                <label class="etiqueta" for="nombre">
                    Nombre:
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="nombre"
                    name="nombre"
                    placeholder="Ingrese su nombre"
                    value="{{ old('nombre') }}"
                    required
                >

                <label class="etiqueta" for="apellido_paterno">
                    Apellido paterno:
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="apellido_paterno"
                    name="apellido_paterno"
                    placeholder="Ingrese su apellido paterno"
                    value="{{ old('apellido_paterno') }}"
                    required
                >

                <label class="etiqueta" for="apellido_materno">
                    Apellido materno:
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="apellido_materno"
                    name="apellido_materno"
                    placeholder="Ingrese su apellido materno"
                    value="{{ old('apellido_materno') }}"
                    required
                >

                <label class="etiqueta" for="correo">
                    Correo electrónico:
                </label>

                <input
                    class="form-control"
                    type="email"
                    id="correo"
                    name="correo"
                    placeholder="Ingrese su correo electrónico"
                    value="{{ old('correo') }}"
                    required
                >

                <label class="etiqueta" for="telefono">
                    Número de teléfono:
                </label>

                <input
                    class="form-control"
                    type="tel"
                    id="telefono"
                    name="telefono"
                    placeholder="Ingrese su número de teléfono"
                    value="{{ old('telefono') }}"
                    required
                >

                <label class="etiqueta" for="rol">
                    Rol:
                </label>

                <select
                    class="select"
                    id="rol"
                    name="rol"
                    required
                >
                    <option
                        value=""
                        disabled
                        {{ old('rol') ? '' : 'selected' }}
                    >
                        Seleccione un rol
                    </option>

                    <option
                        value="usuario"
                        {{ old('rol') === 'usuario' ? 'selected' : '' }}
                    >
                        Cliente
                    </option>

                    <option
                        value="profesional"
                        {{ old('rol') === 'profesional' ? 'selected' : '' }}
                    >
                        Profesional
                    </option>

                    <option
                        value="proveedor"
                        {{ old('rol') === 'proveedor' ? 'selected' : '' }}
                    >
                        Proveedor
                    </option>
                </select>

                <label class="etiqueta" for="password">
                    Contraseña:
                </label>

                <div class="campo-password">
                    <input
                        class="form-control"
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Ingrese su contraseña"
                        required
                    >

                    <span class="eye" id="eye">
                        &#x1F441;
                    </span>
                </div>

                <div class="strength">
                    <div class="bar" id="bar"></div>
                </div>

                <div class="message" id="message">
                    Empieza a escribir...
                </div>

                <label class="etiqueta" for="confirmPassword">
                    Confirmar contraseña:
                </label>

                <div class="campo-password">
                    <input
                        class="form-control"
                        id="confirmPassword"
                        type="password"
                        name="password_confirmation"
                        placeholder="Confirme su contraseña"
                        required
                    >

                    <span class="eye" id="eyeConfirm">
                        &#x1F441;
                    </span>
                </div>

                <div class="message" id="confirmMessage"></div>

                @if($errors->any())
                    <div class="error">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <p class="text-alter">
                    ¿Ya tienes una cuenta?
                    <a href="{{ route('login') }}">Inicia sesión</a>
                </p>

                <button class="btn" type="submit">
                    Siguiente
                </button>
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
                    <h3>ArquiServi</h3>

                    <nav class="pie-enlaces">
                        <a href="{{ route('acerca') }}" class="pie-enlace">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Acerca de</span>
                        </a>

                        <a href="#" class="pie-enlace">
                            <i class="fa-solid fa-briefcase"></i>
                            <span>Servicios</span>
                        </a>

                        <a href="#" class="pie-enlace">
                            <i class="fa-solid fa-users"></i>
                            <span>Profesionales</span>
                        </a>

                        <a href="#" class="pie-enlace">
                            <i class="fa-solid fa-store"></i>
                            <span>Proveedores</span>
                        </a>
                    </nav>
                </div>

                <div class="pie-columna">
                    <h3>Ayuda</h3>

                    <nav class="pie-enlaces">
                        <a href="#" class="pie-enlace">
                            <i class="fa-solid fa-circle-question"></i>
                            <span>Centro de ayuda</span>
                        </a>

                        <a href="#" class="pie-enlace">
                            <i class="fa-solid fa-envelope"></i>
                            <span>Contacto</span>
                        </a>

                        <a href="#" class="pie-enlace">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Seguridad</span>
                        </a>
                    </nav>
                </div>

                <div class="pie-columna">
                    <h3>Legal</h3>

                    <nav class="pie-enlaces">
                        <a href="#" class="pie-enlace">
                            <i class="fa-solid fa-file-contract"></i>
                            <span>Términos y condiciones</span>
                        </a>

                        <a href="#" class="pie-enlace">
                            <i class="fa-solid fa-lock"></i>
                            <span>Privacidad</span>
                        </a>

                        <a href="#" class="pie-enlace">
                            <i class="fa-solid fa-cookie-bite"></i>
                            <span>Cookies</span>
                        </a>
                    </nav>
                </div>

                <div class="pie-redes">
                    <span class="pie-subtitulo">Síguenos</span>

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

            <!-- COLABORADORES -->
            <div class="pie-derecha">
                <div class="pie-colaboradores">
                    <span class="pie-subtitulo">Colaboradores</span>

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
                <a href="#">Aviso de privacidad</a>
                <span class="pie-punto"></span>
                <a href="#">Términos</a>
                <span class="pie-punto"></span>
                <a href="#">Contacto</a>
            </div>
        </div>
    </footer>

    <script>
        document.querySelectorAll('.select').forEach(select => {
            if (select.value !== '') {
                select.classList.add('seleccionado');
            }

            select.addEventListener('change', () => {
                if (select.value !== '') {
                    select.classList.add('seleccionado');
                } else {
                    select.classList.remove('seleccionado');
                }
            });
        });
    </script>
</body>
</html>