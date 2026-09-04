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

<header>
    <h1 id="titulo">ArquiServi</h1>
    <img src="{{ asset('encabezado.png') }}" class="logo" alt="ArquiServi">
</header>

    <section class="form-login">
        <form action="{{ route('login.auth') }}" method="POST">
            @csrf
        <h2>Inicio de Sesion</h2>
        <form>
<<<<<<< HEAD
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" placeholder="Ingrese su nombre de usuario" name="correo" value="{{ old('correo') }}" required>
                <label>Contraseña:</label>
                <input type="password" placeholder="Ingrese su contraseña" name="password" required>
            <p>No tiene una cuenta? <a href="/register">Crea una</a></p>
=======
                <label class="etiqueta" for="username">Nombre de Usuario:</label>
                <input type="text" id="username" placeholder="Ingrese su nombre de usuario" required>
                <label>Contraseña:</label>
                <input type="password"  place placeholder="Ingrese su contraseña" required>
            <p>No tienes una cuenta? <a href="/register">Crea una</a></p>
>>>>>>> 971145c (feat: Creacion de CSS)
            <p>Olvidaste tu contraseña? <a href="/recuperation">Recuperala</a></p>
            <button type="submit">Iniciar Sesion</button>
            
        </form>
    </section>

</body>
</html>