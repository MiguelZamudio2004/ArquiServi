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

    <title>
        @yield('titulo', 'Administración') - ArquiServi
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/menu.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/admin.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/admin-confirmacion.css') }}"
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
        src="{{ asset('js/admin-confirmacion.js') }}"
        defer
    ></script>
</head>

<body>
<header class="encabezado admin-encabezado">
    <img
        src="{{ asset('encabezado2.png') }}"
        class="logo"
        alt="ArquiServi"
    >

    <nav class="menu-superior">
        <ul class="menu-lista">
            <li>
                <a
                    href="{{ route('menu') }}"
                    title="Ir a la página principal"
                >
                    <i class="fa-solid fa-house icono"></i>

                    <span class="texto">
                        Inicio
                    </span>
                </a>
            </li>

            <li>
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'admin-menu-seleccionado' : '' }}"
                >
                    <i class="fa-solid fa-chart-pie icono"></i>

                    <span class="texto">
                        Dashboard
                    </span>
                </a>
            </li>

            <li>
                <a
                    href="{{ route('admin.usuarios.index') }}"
                    class="{{ request()->routeIs('admin.usuarios.*') ? 'admin-menu-seleccionado' : '' }}"
                >
                    <i class="fa-solid fa-users icono"></i>

                    <span class="texto">
                        Usuarios
                    </span>
                </a>
            </li>

            <li>
                <a
                    href="{{ route('admin.profesionales.index') }}"
                    class="{{ request()->routeIs('admin.profesionales.*') ? 'admin-menu-seleccionado' : '' }}"
                >
                    <i class="fa-solid fa-user-tie icono"></i>

                    <span class="texto">
                        Profesionales
                    </span>
                </a>
            </li>

            <li>
                <a
                    href="{{ route('admin.proveedores.index') }}"
                    class="{{ request()->routeIs('admin.proveedores.*') ? 'admin-menu-seleccionado' : '' }}"
                >
                    <i class="fa-solid fa-store icono"></i>

                    <span class="texto">
                        Proveedores
                    </span>
                </a>
            </li>

            <li>
                <a
                    href="{{ route('admin.aprobaciones.index') }}"
                    class="{{ request()->routeIs('admin.aprobaciones.*') ? 'admin-menu-seleccionado' : '' }}"
                >
                    <i class="fa-solid fa-circle-check icono"></i>

                    <span class="texto">
                        Aprobaciones
                    </span>
                </a>
            </li>

            <li>
                <a
                    href="{{ route('admin.solicitudes.index') }}"
                    class="{{ request()->routeIs('admin.solicitudes.*') ? 'admin-menu-seleccionado' : '' }}"
                >
                    <i class="fa-solid fa-envelope-open-text icono"></i>

                    <span class="texto">
                        Solicitudes
                    </span>
                </a>
            </li>

            <li>
                <a
                    href="{{ route('admin.calificaciones.index') }}"
                    class="{{ request()->routeIs('admin.calificaciones.*') ? 'admin-menu-seleccionado' : '' }}"
                >
                    <i class="fa-solid fa-star icono"></i>

                    <span class="texto">
                        Calificaciones
                    </span>
                </a>
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
        </ul>
    </nav>

    <div class="acciones-usuario">
        <a
            href="{{ route('admin.dashboard') }}"
            class="perfil-header"
            aria-label="Panel de administración"
            title="Panel de administración"
        >
            <span class="perfil-avatar-header">
                <i class="fa-solid fa-user-shield"></i>
            </span>
        </a>
    </div>
</header>

<main class="admin-main">
    @if(session('success'))
        <div class="admin-alerta admin-alerta-exito">
            <i class="fa-solid fa-circle-check"></i>

            <span>
                {{ session('success') }}
            </span>
        </div>
    @endif

    @if($errors->any())
        <div class="admin-alerta admin-alerta-error">
            <i class="fa-solid fa-circle-exclamation"></i>

            <span>
                {{ $errors->first() }}
            </span>
        </div>
    @endif

    @yield('contenido')
</main>

@include('admin.components.confirmacion')

</body>

</html>