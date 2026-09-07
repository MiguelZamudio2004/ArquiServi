<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
</head>
<body>
    <section class="form-recuperation">
        <form action="{{ route('recuperacion.enviar') }}" method="POST">
        @csrf
        <h2>Recuperar Contraseña</h2>
            <label>Correo Electrónico:</label>
            <input type="email" name="correo" placeholder="Ingrese su correo electrónico" required>
            @error('correo')
                <span class="error">{{ $message }}</span>
            @enderror
            <input type="submit" value="Enviar Codigo de Autentificacion" class="boton">
        </form>
        </section>

</body>
</html>