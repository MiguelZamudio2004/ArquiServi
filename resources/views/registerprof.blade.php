<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Profesional</title>
    <link rel="stylesheet" href="{{ asset('css/registerprof.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
</head>
<body>
    <header class="encabezado">
        <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
    </header>

    <section class="form-register">
        <h2 id="subtitulo">Registro Profesional</h2>
        <form>
            <label class="etiqueta">Años de experiencia:</label>
            <input class="form-control" type="number" placeholder="Ingrese el tiempo que tiene de experiencia" required>
            <label class="etiqueta">Descripción:</label>
            <input class="form-control" type="text" placeholder="Describa su trabajo" required>
            <label class="etiqueta">Link de su portafolio:</label>
            <input class="form-control" type="url" placeholder="Ingrese el enlace de su portafolio" required>
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