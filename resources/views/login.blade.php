<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
</head>

<body>

@if ($errors->any())
    <div>
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<header class="encabezado">
    <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
</header>
    <h1 id="titulo">
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
    </h1>
    <section class="form-login">
        <form action="{{ route('login.auth') }}" method="POST">
            @csrf
        <h2 id="subtitulo">Inicio de sesión</h2>
        <form>
                <label class="etiqueta" for="username">Nombre de usuario:</label>
                <input class="form-control" type="text" id="username" placeholder="Ingrese su nombre de usuario" required>
                <label class="etiqueta">Contraseña:</label>
                <input class="form-control" type="password"  placeholder="Ingrese su contraseña" required>
            <p class="textalter">¿No tienes una cuenta? <a href="/register">Crea una</a></p>
            <p class="textalter">¿Olvidaste tu contraseña? <a href="/recuperation">Recuperala</a></p>
            <button class="btn" type="submit">Iniciar Sesion</button>
            
        </form>
    </section>

</body>

<footer class="pie">
    <p>© 2024 ArquiServi. Todos los derechos reservados.</p>
</footer>
</html>