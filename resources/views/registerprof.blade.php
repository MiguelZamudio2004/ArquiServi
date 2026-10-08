<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Registro Profesional</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/registerprof.css') }}"
    >

    <link
        rel="icon"
        href="{{ asset('icono.png') }}"
        type="image/png"
    >
</head>

<body>
<header class="encabezado">
    <img
        src="{{ asset('encabezado2.png') }}"
        class="logo"
        alt="ArquiServi"
    >
</header>

<section class="form-register">
    <h2 id="subtitulo">
        Registro Profesional
    </h2>

    <form
        action="{{ route('registro.profesional.guardar') }}"
        method="POST"
    >
        @csrf

        <label
            class="etiqueta"
            for="profesion_id"
        >
            Profesión:
        </label>

        <select
            class="select"
            name="profesion_id"
            id="profesion_id"
            required
            autocomplete="off"
        >
            <option
                value=""
                disabled
                {{ old('profesion_id') ? '' : 'selected' }}
            >
                Seleccione una profesión
            </option>

            @foreach($profesiones as $profesion)
                <option
                    value="{{ $profesion->id }}"
                    {{ old('profesion_id') == $profesion->id ? 'selected' : '' }}
                >
                    {{ $profesion->nombre }}
                </option>
            @endforeach
        </select>

        <label class="etiqueta">
            Especialidades:
        </label>

        <p class="texto-ayuda">
            Puedes seleccionar una o varias especialidades.
        </p>

        @php
            $especialidadesSeleccionadas = array_map(
                'intval',
                old('especialidades', [])
            );
        @endphp

        <div
            class="especialidades-contenedor"
            id="especialidadesContenedor"
        >
            @foreach($profesiones as $profesion)
                @foreach($profesion->especialidades as $especialidad)
                    <label
                        class="especialidad-opcion"
                        data-profesion="{{ $profesion->id }}"
                        data-requiere-aprobacion="{{ $especialidad->requiere_aprobacion ? '1' : '0' }}"
                        for="especialidad_{{ $especialidad->id }}"
                    >
                        <input
                            type="checkbox"
                            name="especialidades[]"
                            id="especialidad_{{ $especialidad->id }}"
                            value="{{ $especialidad->id }}"
                            {{ in_array(
                                (int) $especialidad->id,
                                $especialidadesSeleccionadas,
                                true
                            ) ? 'checked' : '' }}
                        >

                        <span class="especialidad-contenido">
                            <span class="especialidad-check">
                                ✓
                            </span>

                            <span class="especialidad-nombre">
                                {{ $especialidad->nombre }}
                            </span>
                        </span>
                    </label>
                @endforeach
            @endforeach
        </div>

        <div
            class="aviso-aprobacion"
            id="avisoAprobacion"
            hidden
        >
            La especialidad Supervisor de obra requiere aprobación de un administrador. Tu perfil permanecerá pendiente hasta que la solicitud sea revisada.
        </div>

        <label
            class="etiqueta"
            for="anios_experiencia"
        >
            Años de experiencia:
        </label>

        <input
            class="form-control"
            type="number"
            name="anios_experiencia"
            id="anios_experiencia"
            min="0"
            max="80"
            value="{{ old('anios_experiencia') }}"
            placeholder="Ingrese el tiempo que tiene de experiencia"
            required
        >

        <label
            class="etiqueta"
            for="descripcion"
        >
            Descripción:
        </label>

        <textarea
            class="form-control"
            name="descripcion"
            id="descripcion"
            maxlength="500"
            placeholder="Describa su trabajo"
            required
        >{{ old('descripcion') }}</textarea>

        <label
            class="etiqueta"
            for="portafolio_url"
        >
            Link de su portafolio:
        </label>

        <input
            class="form-control"
            type="url"
            name="portafolio_url"
            id="portafolio_url"
            value="{{ old('portafolio_url') }}"
            placeholder="Ingrese el enlace de su portafolio"
        >

        <label
            class="etiqueta"
            for="zona_trabajo"
        >
            Zona o ciudad donde trabaja:
        </label>

        <input
            class="form-control"
            type="text"
            name="zona_trabajo"
            id="zona_trabajo"
            value="{{ old('zona_trabajo') }}"
            placeholder="Ingrese su zona o ciudad"
            required
        >

        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    <p>
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif

        <input
            type="submit"
            value="Registrarse"
            class="btn"
        >
    </form>
</section>

<footer class="pie">
    <p>
        © 2026 ArquiServi. Todos los derechos reservados.
    </p>
</footer>

<script>
    const profesion = document.getElementById('profesion_id');

    const opcionesEspecialidad = Array.from(
        document.querySelectorAll('.especialidad-opcion')
    );

    const avisoAprobacion = document.getElementById('avisoAprobacion');

    function cargarEspecialidades(limpiar = false) {
        const profesionId = profesion.value;

        opcionesEspecialidad.forEach(opcion => {
            const corresponde = opcion.dataset.profesion === profesionId;

            opcion.hidden = !corresponde;

            const checkbox = opcion.querySelector(
                'input[type="checkbox"]'
            );

            if (!corresponde && limpiar) {
                checkbox.checked = false;
            }
        });

        actualizarAvisoAprobacion();
    }

    function actualizarAvisoAprobacion() {
        const requiereAprobacion = opcionesEspecialidad.some(opcion => {
            if (opcion.hidden) {
                return false;
            }

            const checkbox = opcion.querySelector(
                'input[type="checkbox"]'
            );

            return (
                checkbox.checked &&
                opcion.dataset.requiereAprobacion === '1'
            );
        });

        avisoAprobacion.hidden = !requiereAprobacion;
    }

    profesion.addEventListener('change', function () {
        cargarEspecialidades(true);
    });

    opcionesEspecialidad.forEach(opcion => {
        const checkbox = opcion.querySelector(
            'input[type="checkbox"]'
        );

        checkbox.addEventListener(
            'change',
            actualizarAvisoAprobacion
        );
    });

    cargarEspecialidades(false);

    document.querySelectorAll('.select').forEach(select => {
        function actualizarEstado() {
            if (select.value !== '') {
                select.classList.add('seleccionado');
            } else {
                select.classList.remove('seleccionado');
            }
        }

        select.addEventListener(
            'change',
            actualizarEstado
        );

        actualizarEstado();
    });
</script>

</body>
</html>