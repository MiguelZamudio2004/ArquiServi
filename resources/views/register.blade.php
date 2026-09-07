<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>

<body>

    <section>

        <form action="{{ route('register.store') }}" method="POST">
            @csrf
        <h2>Nueva Cuenta</h2>
            @if ($errors->any())
                <div>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <label>Nombre:</label>
            <input type="text" name="nombre" placeholder="Ingrese su nombre" value="{{ old('nombre') }}" required>

            <label>Apellido paterno:</label>
            <input type="text" name="apellido_paterno" placeholder="Ingrese su apellido paterno" value="{{ old('apellido_paterno') }}" required>

            <label>Apellido materno:</label>
            <input type="text" name="apellido_materno" placeholder="Ingrese su apellido materno" value="{{ old('apellido_materno') }}" required>

            <label>Correo electrónico:</label>
            <input type="email" name="correo" placeholder="Ingrese su correo electrónico" value="{{ old('correo') }}" required>

            <label>Número de teléfono:</label>
            <input type="tel" name="telefono" placeholder="Ingrese su número de teléfono" value="{{ old('telefono') }}" required>

            <label>Rol:</label>
            <select name="rol" required>
            <option value="" disabled {{ old('rol') ? '' : 'selected' }}>
                Seleccione un rol
            </option>

                <option
                    value="usuario"
                    {{ old('rol') == 'usuario' ? 'selected' : '' }}
                >
                    Cliente
                </option>

                <option
                    value="profesional"
                    {{ old('rol') == 'profesional' ? 'selected' : '' }}
                >
                    Profesional
                </option>

                <option
                    value="proveedor"
                    {{ old('rol') == 'proveedor' ? 'selected' : '' }}
                >
                    Proveedor
                </option>
            </select>

            <label>Contraseña:</label>
            <input type="password" name="password" placeholder="Ingrese su contraseña" required>

            <label>Confirmar contraseña:</label>
            <input type="password" name="password_confirmation" placeholder="Confirme su contraseña" required>

            <p>¿Ya tienes una cuenta? <a href="/login">Inicia sesión</a></p>

            <button type="submit">Siguiente</button>

        </form>

    </section>

</body>

</html>