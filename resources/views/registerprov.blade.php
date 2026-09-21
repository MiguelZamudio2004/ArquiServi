<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Proveedor</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
</head>
<body>
    <header class="encabezado">
        <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
    </header>

    <section class="form-register">
        <h2 id="subtitulo">Registro Proveedor</h2>
        <form>
            <label class="etiqueta">Descripción de la empresa:</label>
            <input class="form-control" type="text" placeholder="Ingrese la descripción de su empresa" required>
            <label class="etiqueta">Zona o ciudad donde trabaja:</label>
            <input class="form-control" type="text" placeholder="Ingrese su zona o ciudad" required>
            <input type="submit" value="Registrarse" class="btn">
        </form>
    </section>
</body>

<footer class="pie">
    <p>© 2026 ArquiServi. Todos los derechos reservados.</p>
</footer>

</html>