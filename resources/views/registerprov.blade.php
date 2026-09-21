<!DOCTYPE html>
<html lang="es">
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

        <form action="{{ route('registro.proveedor.guardar') }}" method="POST">
            @csrf

            <label class="etiqueta" for="descripcion">Descripción de la empresa:</label>
            <textarea class="form-control" name="descripcion" id="descripcion" maxlength="500" placeholder="Ingrese la descripción de su empresa">{{ old('descripcion') }}</textarea>

            <label class="etiqueta" for="zona_trabajo">Zona o ciudad donde trabaja:</label>
            <input class="form-control" type="text" name="zona_trabajo" id="zona_trabajo" value="{{ old('zona_trabajo') }}" placeholder="Ingrese su zona o ciudad" required>

            <label class="etiqueta">Materiales o productos que ofrece:</label>

            <div class="materiales">
                @forelse($materiales as $material)
                    <label class="material-opcion">
                        <input type="checkbox" name="materiales[]" value="{{ $material->id }}" {{ in_array($material->id, old('materiales', [])) ? 'checked' : '' }}>
                        <span>{{ $material->nombre }}</span>
                    </label>
                @empty
                    <p>No hay materiales registrados.</p>
                @endforelse
            </div>

            @if($errors->any())
                <div class="error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <input type="submit" value="Registrarse" class="btn">
        </form>
    </section>

    <footer class="pie">
        <p>© 2026 ArquiServi. Todos los derechos reservados.</p>
    </footer>
</body>
</html>