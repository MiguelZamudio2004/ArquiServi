<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
</head>

<body>
    <header class="encabezado">
    <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
    </header>

    <section class="form-register">

        <form action="{{ route('register.store') }}" method="POST">
            @csrf
        <h2 id="subtitulo">Nueva Cuenta</h2>
            @if ($errors->any())
                <div>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <label class="etiqueta">Nombre:</label>
            <input class="form-control" type="text" name="nombre" placeholder="Ingrese su nombre" value="{{ old('nombre') }}" required>

            <label class="etiqueta">Apellido paterno:</label>
            <input class="form-control" type="text" name="apellido_paterno" placeholder="Ingrese su apellido paterno" value="{{ old('apellido_paterno') }}" required>

            <label class="etiqueta">Apellido materno:</label>
            <input class="form-control" type="text" name="apellido_materno" placeholder="Ingrese su apellido materno" value="{{ old('apellido_materno') }}" required>

            <label class="etiqueta">Correo electrónico:</label>
            <input class="form-control" type="email" name="correo" placeholder="Ingrese su correo electrónico" value="{{ old('correo') }}" required>

            <label class="etiqueta">Número de teléfono:</label>
            <input class="form-control" type="tel" name="telefono" placeholder="Ingrese su número de teléfono" value="{{ old('telefono') }}" required>

            <label class="etiqueta">Rol:</label>
            <select  class="form-control" name="rol" required>
            <option class="text-alter" value="" disabled {{ old('rol') ? '' : 'selected' }}>
                Seleccione un rol
            </option>

                <option class="form-control"
                    value="usuario"
                    {{ old('rol') == 'usuario' ? 'selected' : '' }}
                >
                    Cliente
                </option>

                <option class="form-control"
                    value="profesional"
                    {{ old('rol') == 'profesional' ? 'selected' : '' }}
                >
                    Profesional
                </option>

                <option class="form-control"
                    value="proveedor"
                    {{ old('rol') == 'proveedor' ? 'selected' : '' }}
                >
                    Proveedor
                </option>
            </select>

            <label class="etiqueta">Contraseña:</label>
            <input class="form-control" type="password" name="password" placeholder="Ingrese su contraseña" required>

            <label class="etiqueta">Confirmar contraseña:</label>
            <input class="form-control" type="password" name="password_confirmation" placeholder="Confirme su contraseña" required>

            <p class="text-alter">¿Ya tienes una cuenta? <a href="/login">Inicia sesión</a></p>

            <button class="btn" type="submit">Siguiente</button>

        </form>

    </section>

</body>

<footer class="pie">
    <p>© 2024 ArquiServi. Todos los derechos reservados.</p>
</footer>

</html>