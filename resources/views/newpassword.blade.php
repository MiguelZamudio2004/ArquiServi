<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña</title>
    <link rel="stylesheet" href="{{ asset('css/newpassword.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
</head>
<body>
    <header class="encabezado">
    <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
    </header>

    <section class="form-newpassword">
        <form action="{{ route('recuperacion.cambiar') }}" method="POST">
            @csrf 
        <h2 id="subtitulo">Nueva Contraseña</h2>
        <label class="etiqueta">Ingrese su nueva contraseña:</label>
        <input class="form-control" type="password" placeholder="Nueva contraseña" name="password" required>

        @error('password')
            <p class="error">{{ $message }}</p>
        @enderror

        <label class="etiqueta">Confirme su nueva contraseña:</label>
        <input class="form-control" type="password" placeholder="Confirmar contraseña" name="password_confirmation" required>
        <button class="btn">Cambiar contraseña</button>
    </form>
    </section>
    
</body>

<footer class="pie">
    <p>© 2026 ArquiServi. Todos los derechos reservados.</p>
</footer>

</html>