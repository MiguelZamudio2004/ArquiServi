<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="{{ asset('css/perfil-editar.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
</head>

<body>

    <header class="encabezado">
        <a href="{{ route('menu') }}">
            <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
        </a>
    </header>

    <main class="editar-perfil">

        <h1>Editar perfil</h1>

        <form action="{{ route('perfil.actualizar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="foto-actual">
                @if($usuario->foto_perfil)
                    <img id="previewFoto" src="{{ asset('storage/' . $usuario->foto_perfil) }}" alt="Foto de perfil">
                @else
                    <div class="foto-inicial" id="fotoInicial">
                        {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                    </div>

                    <img id="previewFoto" src="" alt="Vista previa" style="display:none;">
                @endif
            </div>

            <label class="etiqueta">Foto de perfil</label>

            <div class="selector-archivo">
                <label for="foto_perfil" class="btn-archivo">Seleccionar imagen</label>
                <span id="nombreArchivo">Ningún archivo seleccionado</span>
            </div>

            <input type="file" name="foto_perfil" id="foto_perfil" accept="image/png,image/jpeg,image/webp" hidden>

            <label class="etiqueta" for="nombre">Nombre:</label>
            <input class="form-control" type="text" name="nombre" id="nombre" value="{{ old('nombre', $usuario->nombre) }}" required>

            <label class="etiqueta" for="apellido_paterno">Apellido paterno:</label>
            <input class="form-control" type="text" name="apellido_paterno" id="apellido_paterno" value="{{ old('apellido_paterno', $usuario->apellido_paterno) }}" required>

            <label class="etiqueta" for="apellido_materno">Apellido materno:</label>
            <input class="form-control" type="text" name="apellido_materno" id="apellido_materno" value="{{ old('apellido_materno', $usuario->apellido_materno) }}">

            <label class="etiqueta" for="telefono">Teléfono:</label>
            <input class="form-control" type="text" name="telefono" id="telefono" value="{{ old('telefono', $usuario->telefono) }}">

            <label class="etiqueta" for="ubicacion">Ubicación:</label>
            <input class="form-control" type="text" name="ubicacion" id="ubicacion" value="{{ old('ubicacion', $usuario->ubicacion) }}">

            <label class="etiqueta" for="descripcion_usuario">Descripción del perfil:</label>
            <textarea class="form-control" name="descripcion_usuario" id="descripcion_usuario" maxlength="500">{{ old('descripcion_usuario', $usuario->descripcion) }}</textarea>

            @if($usuario->rol->nombre === 'profesional')

                <h2>Información profesional</h2>

                @php
                    $profesionActual = old('profesion_id', $usuario->profesional?->profesiones->first()?->id);
                    $especialidadActual = old('especialidad_id', $usuario->profesional?->especialidades->first()?->id);
                @endphp

                <label class="etiqueta" for="profesion_id">Profesión:</label>
                <select class="form-control" name="profesion_id" id="profesion_id" required>
                    <option value="">Seleccione una profesión</option>

                    @foreach($profesiones as $profesion)
                        <option value="{{ $profesion->id }}" {{ $profesionActual == $profesion->id ? 'selected' : '' }}>
                            {{ $profesion->nombre }}
                        </option>
                    @endforeach
                </select>

                <label class="etiqueta" for="especialidad_id">Especialidad:</label>
                <select class="form-control" name="especialidad_id" id="especialidad_id" required>
                    <option value="">Seleccione una especialidad</option>

                    @foreach($profesiones as $profesion)
                        @foreach($profesion->especialidades as $especialidad)
                            <option value="{{ $especialidad->id }}"
                                data-profesion="{{ $profesion->id }}"
                                {{ $especialidadActual == $especialidad->id ? 'selected' : '' }}>
                                {{ $especialidad->nombre }}
                            </option>
                        @endforeach
                    @endforeach
                </select>

                <label class="etiqueta" for="anios_experiencia">Años de experiencia:</label>
                <input class="form-control" type="number" name="anios_experiencia" id="anios_experiencia" min="0" max="80"
                    value="{{ old('anios_experiencia', $usuario->profesional?->anios_experiencia) }}" required>

                <label class="etiqueta" for="descripcion_profesional">Descripción profesional:</label>
                <textarea class="form-control" name="descripcion_profesional" id="descripcion_profesional" maxlength="500" required>{{ old('descripcion_profesional', $usuario->profesional?->descripcion) }}</textarea>

                <label class="etiqueta" for="portafolio_url">Link del portafolio:</label>
                <input class="form-control" type="url" name="portafolio_url" id="portafolio_url"
                    value="{{ old('portafolio_url', $usuario->profesional?->portafolio_url) }}">

                <label class="etiqueta" for="zona_trabajo_profesional">Zona o ciudad donde trabaja:</label>
                <input class="form-control" type="text" name="zona_trabajo_profesional" id="zona_trabajo_profesional"
                    value="{{ old('zona_trabajo_profesional', $usuario->profesional?->zona_trabajo) }}" required>

                    <label class="etiqueta">Servicios que ofreces:</label>

@php
    $serviciosSeleccionados = old('servicios', $usuario->profesional?->servicios->pluck('id')->toArray() ?? []);
@endphp

<div class="materiales">
    @forelse($servicios as $servicio)
        <label class="material-opcion">
            <input type="checkbox" name="servicios[]" value="{{ $servicio->id }}" {{ in_array($servicio->id, $serviciosSeleccionados) ? 'checked' : '' }}>
            <span>{{ $servicio->nombre }}</span>
        </label>
    @empty
        <p>No hay servicios registrados.</p>
    @endforelse
</div>
    @elseif($usuario->rol->nombre === 'proveedor')

                <h2>Información del proveedor</h2>

                <label class="etiqueta" for="descripcion_proveedor">Descripción de la empresa:</label>
                <textarea class="form-control" name="descripcion_proveedor" id="descripcion_proveedor" maxlength="500">{{ old('descripcion_proveedor', $usuario->proveedor?->descripcion) }}</textarea>

                <label class="etiqueta" for="zona_trabajo_proveedor">Zona o ciudad donde trabaja:</label>
                <input class="form-control" type="text" name="zona_trabajo_proveedor" id="zona_trabajo_proveedor"
                    value="{{ old('zona_trabajo_proveedor', $usuario->proveedor?->zona_trabajo) }}" required>

                <label class="etiqueta">Materiales o productos que ofrece:</label>

                @php
                    $materialesSeleccionados = old(
                        'materiales',
                        $usuario->proveedor?->materiales->pluck('id')->toArray() ?? []
                    );
                @endphp

                <div class="materiales">
                    @foreach($materiales as $material)
                        <label class="material-opcion">
                            <input type="checkbox" name="materiales[]" value="{{ $material->id }}"
                                {{ in_array($material->id, $materialesSeleccionados) ? 'checked' : '' }}>

                            <span>{{ $material->nombre }}</span>
                        </label>
                    @endforeach
                </div>

            @endif

            @if($errors->any())
                <div class="error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="acciones">
                <a href="{{ route('perfil') }}" class="btn-cancelar">Cancelar</a>
                <button type="submit" class="btn-guardar">Guardar cambios</button>
            </div>

        </form>

    </main>

    <footer class="pie">
        <p>© 2026 ArquiServi. Todos los derechos reservados.</p>
    </footer>

    <script src="{{ asset('js/perfil-editar.js') }}" defer></script>

    @if($usuario->rol->nombre === 'profesional')
        <script>
            const profesion = document.getElementById('profesion_id');
            const especialidad = document.getElementById('especialidad_id');
            const opcionesEspecialidad = Array.from(especialidad.querySelectorAll('option[data-profesion]'));

            function cargarEspecialidades(cambio = false) {
                const profesionId = profesion.value;

                if (cambio) especialidad.value = '';

                opcionesEspecialidad.forEach(opcion => {
                    opcion.hidden = opcion.dataset.profesion !== profesionId;
                });

                const seleccionada = especialidad.options[especialidad.selectedIndex];

                if (seleccionada && seleccionada.dataset.profesion && seleccionada.dataset.profesion !== profesionId) {
                    especialidad.value = '';
                }
            }

            profesion.addEventListener('change', function () {
                cargarEspecialidades(true);
            });

            cargarEspecialidades();
        </script>
    @endif

</body>

</html>