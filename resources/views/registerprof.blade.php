<!DOCTYPE html>
<html lang="es">

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

        <form action="{{ route('registro.profesional.guardar') }}" method="POST">
            @csrf

            <label class="etiqueta" for="profesion_id">Profesión:</label>
            <select class="select" name="profesion_id" id="profesion_id" required autocomplete="off">
                <option value="" selected disabled>Seleccione una profesión</option>

                @foreach($profesiones as $profesion)
                    <option value="{{ $profesion->id }}">
                        {{ $profesion->nombre }}
                    </option>
                @endforeach
            </select>

            <label class="etiqueta" for="especialidad_id">Especialidad:</label>
            <select class="select" name="especialidad_id" id="especialidad_id" required autocomplete="off">
                <option value="" selected disabled>Seleccione una especialidad</option>

                @foreach($profesiones as $profesion)
                    @foreach($profesion->especialidades as $especialidad)
                        <option value="{{ $especialidad->id }}" data-profesion="{{ $profesion->id }}">
                            {{ $especialidad->nombre }}
                        </option>
                    @endforeach
                @endforeach
            </select>

            <label class="etiqueta" for="anios_experiencia">Años de experiencia:</label>
            <input class="form-control" type="number" name="anios_experiencia" id="anios_experiencia"
                min="0" max="80" value="{{ old('anios_experiencia') }}"
                placeholder="Ingrese el tiempo que tiene de experiencia" required>

            <label class="etiqueta" for="descripcion">Descripción:</label>
            <textarea class="form-control" name="descripcion" id="descripcion"
                maxlength="500" placeholder="Describa su trabajo" required>{{ old('descripcion') }}</textarea>

            <label class="etiqueta" for="portafolio_url">Link de su portafolio:</label>
            <input class="form-control" type="url" name="portafolio_url" id="portafolio_url"
                value="{{ old('portafolio_url') }}" placeholder="Ingrese el enlace de su portafolio">

            <label class="etiqueta" for="zona_trabajo">Zona o ciudad donde trabaja:</label>
            <input class="form-control" type="text" name="zona_trabajo" id="zona_trabajo"
                value="{{ old('zona_trabajo') }}" placeholder="Ingrese su zona o ciudad" required>

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

    <script>
        const profesion = document.getElementById('profesion_id');
        const especialidad = document.getElementById('especialidad_id');
        const opciones = Array.from(especialidad.querySelectorAll('option[data-profesion]'));

        function cargarEspecialidades() {
            const profesionId = profesion.value;
            const especialidadActual = especialidad.value;

            opciones.forEach(opcion => {
                opcion.hidden = opcion.dataset.profesion !== profesionId;
            });

            const seleccionada = especialidad.querySelector(`option[value="${especialidadActual}"]`);

            if (!seleccionada || seleccionada.dataset.profesion !== profesionId) {
                especialidad.value = '';
            }
        }

        profesion.addEventListener('change', function () {
            especialidad.value = '';
            cargarEspecialidades();
        });

        cargarEspecialidades();
    </script>

    <script>
        document.querySelectorAll('.select').forEach(select => {
            select.addEventListener('change', () => {
                if (select.value !== '') {
                    select.classList.add('seleccionado');
                } else {
                    select.classList.remove('seleccionado');
                }
            });
        });
    </script>

</body>
</html>