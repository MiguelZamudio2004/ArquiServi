<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Validación de código - ArquiServi</title>

    <link rel="stylesheet" href="{{ asset('css/validar.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >
</head>

<body>

    <!-- ENCABEZADO -->
    <header class="encabezado">
        <img
            src="{{ asset('encabezado2.png') }}"
            class="logo"
            alt="ArquiServi"
        >
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="validar-main">

        <section class="form-validar">

            <!-- FORMULARIO DE VALIDACIÓN -->
            <form
                action="{{ route('recuperacion.validar') }}"
                method="POST"
            >
                @csrf

                <h2 id="subtitulo">Validar</h2>

                <p class="etiqueta">
                    Hemos enviado un código a tu correo electrónico.
                    Por favor, ingresa el código para continuar:
                </p>

                <!-- CAMPOS DEL CÓDIGO -->
                <div class="codigo-container">

                    <input
                        class="codigo"
                        type="text"
                        name="codigo[]"
                        maxlength="1"
                        inputmode="numeric"
                        autocomplete="off"
                        aria-label="Dígito 1 del código"
                    >

                    <input
                        class="codigo"
                        type="text"
                        name="codigo[]"
                        maxlength="1"
                        inputmode="numeric"
                        autocomplete="off"
                        aria-label="Dígito 2 del código"
                    >

                    <input
                        class="codigo"
                        type="text"
                        name="codigo[]"
                        maxlength="1"
                        inputmode="numeric"
                        autocomplete="off"
                        aria-label="Dígito 3 del código"
                    >

                    <input
                        class="codigo"
                        type="text"
                        name="codigo[]"
                        maxlength="1"
                        inputmode="numeric"
                        autocomplete="off"
                        aria-label="Dígito 4 del código"
                    >

                    <input
                        class="codigo"
                        type="text"
                        name="codigo[]"
                        maxlength="1"
                        inputmode="numeric"
                        autocomplete="off"
                        aria-label="Dígito 5 del código"
                    >

                    <input
                        class="codigo"
                        type="text"
                        name="codigo[]"
                        maxlength="1"
                        inputmode="numeric"
                        autocomplete="off"
                        aria-label="Dígito 6 del código"
                    >

                </div>

                <!-- ERRORES DE VALIDACIÓN -->
                @error('codigo')
                    <p class="error">
                        {{ $message }}
                    </p>
                @enderror

                <!-- BOTÓN DE VALIDACIÓN -->
                <button class="btn" type="submit">
                    Validar código
                </button>

            </form>

            <!-- FORMULARIO PARA REENVIAR EL CÓDIGO -->
            <form
                action="{{ route('recuperacion.reenviar') }}"
                method="POST"
                class="reenviar-form"
            >
                @csrf

                <p class="text-alter">
                    ¿No recibiste ningún código?
                </p>

                <button
                    type="submit"
                    class="reenviar-codigo"
                >
                    Reenviar código
                </button>

            </form>

        </section>

    </main>

    <!-- PIE DE PÁGINA -->
    <footer class="pie">

        <div class="pie-contenido">

            <!-- MARCA ARQUISERVI -->
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
                    Encuentra profesionales y proveedores para llevar tus
                    proyectos a la realidad de forma sencilla, segura y confiable.
                </p>

            </div>

            <!-- COLUMNAS CENTRALES -->
            <div class="pie-columnas">

                <!-- ARQUISERVI -->
                <div class="pie-columna">
                    <h3>ArquiServi</h3>

                    <nav class="pie-enlaces">

                        <a
                            href="{{ route('acerca') }}"
                            class="pie-enlace"
                        >
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

                <!-- AYUDA -->
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

                <!-- LEGAL -->
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

                <!-- REDES SOCIALES -->
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

            <!-- COLABORADORES -->
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

        <!-- SEPARADOR -->
        <div class="pie-separador"></div>

        <!-- PARTE INFERIOR -->
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

    <!-- COMPORTAMIENTO DEL CÓDIGO DE VALIDACIÓN -->
    <script>
        const inputs = document.querySelectorAll('.codigo-container input');

        inputs.forEach((input, index) => {

            input.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');

                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', function (event) {
                if (
                    event.key === 'Backspace' &&
                    this.value === '' &&
                    index > 0
                ) {
                    inputs[index - 1].focus();
                }
            });

        });
    </script>

</body>
</html>