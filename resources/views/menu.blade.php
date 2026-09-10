<!DOCTYPE html>
<html lang="en">
<head>
    <title>Inicio</title>
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
</head>

<body>
    <header class="encabezado">
    <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
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