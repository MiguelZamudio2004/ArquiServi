<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="{{ asset('css/recuperation.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
</head>

<body>
    <header class="encabezado">
    <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
    </header>

    <section class="form-recuperation">
        <form action="{{ route('recuperacion.enviar') }}" method="POST">
        @csrf
        <h2 id="subtitulo">Recuperar Contraseña</h2>
            <label class="etiqueta">Correo Electrónico:</label>
            <input class="form-control" type="email" name="correo" placeholder="Ingrese su correo electrónico" required>
            @error('correo')
                <p class="error">{{ $message }}</p>
            @enderror
            <button class="btn" type="submit"> Enviar código de autentificación </button>
        </form>
        </section>

</body>

<footer class="pie">
    <p>© 2026 ArquiServi. Todos los derechos reservados.</p>
</footer>

</html>