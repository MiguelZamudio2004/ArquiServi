<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar servicio - ArquiServi</title>
    <link rel="stylesheet" href="{{ asset('css/solicitudes.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
</head>
<body>

<header class="encabezado">
    <a href="{{ route('menu') }}">
        <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
    </a>
</header>

<main class="solicitudes-contenedor">
    <h1>Solicitar servicio</h1>

    <form action="{{ route('solicitudes.guardar') }}" method="POST" class="solicitud-form">
        @csrf

        <input type="hidden" name="profesional_id" value="{{ $profesional->id }}">

        <label>Profesional</label>
        <div class="campo-fijo">
            {{ $profesional->usuario->nombre }} {{ $profesional->usuario->apellido_paterno }}
        </div>

        <label for="servicio_id">Servicio</label>
        <select name="servicio_id" id="servicio_id" required>
            <option value="" disabled {{ old('servicio_id') ? '' : 'selected' }}>Selecciona un servicio</option>

            @foreach($profesional->servicios as $servicio)
                <option value="{{ $servicio->id }}" {{ old('servicio_id') == $servicio->id ? 'selected' : '' }}>
                    {{ $servicio->nombre }}
                </option>
            @endforeach
        </select>

        <label for="descripcion">Describe lo que necesitas</label>
        <textarea name="descripcion" id="descripcion" maxlength="1000" required>{{ old('descripcion') }}</textarea>

        @if($errors->any())
            <div class="errores">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="acciones">
            <a href="{{ route('perfil.publico', $profesional->usuario) }}" class="btn-secundario">Cancelar</a>
            <button type="submit" class="btn-principal">Enviar solicitud</button>
        </div>
    </form>
</main>

<footer class="pie">
    <p>© 2026 ArquiServi. Todos los derechos reservados.</p>
</footer>

</body>
</html>