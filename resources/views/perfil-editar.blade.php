<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Editar Perfil</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/perfil-editar.css') }}"
    >

    <link
        rel="icon"
        href="{{ asset('icono.png') }}"
        type="image/png"
    >
</head>

<body>
<header class="encabezado">
    <a href="{{ route('menu') }}">
        <img
            src="{{ asset('encabezado2.png') }}"
            class="logo"
            alt="ArquiServi"
        >
    </a>
</header>

<main class="editar-perfil">
    <h1>
        Editar perfil
    </h1>

    <form
        action="{{ route('perfil.actualizar') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        <div class="foto-actual">
            @if($usuario->foto_perfil)
                <img
                    id="previewFoto"
                    src="{{ asset('storage/' . $usuario->foto_perfil) }}"
                    alt="Foto de perfil"
                >
            @else
                <div
                    class="foto-inicial"
                    id="fotoInicial"
                >
                    {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                </div>

                <img
                    id="previewFoto"
                    src=""
                    alt="Vista previa"
                    style="display:none;"
                >
            @endif
        </div>

        <label class="etiqueta">
            Foto de perfil
        </label>

        <div class="selector-archivo">
            <label
                for="foto_perfil"
                class="btn-archivo"
            >
                Seleccionar imagen
            </label>

            <span id="nombreArchivo">
                Ningún archivo seleccionado
            </span>
        </div>

        <input
            type="file"
            name="foto_perfil"
            id="foto_perfil"
            accept="image/png,image/jpeg,image/webp"
            hidden
        >

        <label
            class="etiqueta"
            for="nombre"
        >
            Nombre:
        </label>

        <input
            class="form-control"
            type="text"
            name="nombre"
            id="nombre"
            value="{{ old('nombre', $usuario->nombre) }}"
            required
        >

        <label
            class="etiqueta"
            for="apellido_paterno"
        >
            Apellido paterno:
        </label>

        <input
            class="form-control"
            type="text"
            name="apellido_paterno"
            id="apellido_paterno"
            value="{{ old('apellido_paterno', $usuario->apellido_paterno) }}"
            required
        >

        <label
            class="etiqueta"
            for="apellido_materno"
        >
            Apellido materno:
        </label>

        <input
            class="form-control"
            type="text"
            name="apellido_materno"
            id="apellido_materno"
            value="{{ old('apellido_materno', $usuario->apellido_materno) }}"
        >

        <label
            class="etiqueta"
            for="telefono"
        >
            Teléfono:
        </label>

        <input
            class="form-control"
            type="text"
            name="telefono"
            id="telefono"
            value="{{ old('telefono', $usuario->telefono) }}"
        >

        <label
            class="etiqueta"
            for="ubicacion"
        >
            Ubicación:
        </label>

        <input
            class="form-control"
            type="text"
            name="ubicacion"
            id="ubicacion"
            value="{{ old('ubicacion', $usuario->ubicacion) }}"
        >

        <label
            class="etiqueta"
            for="descripcion_usuario"
        >
            Descripción del perfil:
        </label>

        <textarea
            class="form-control"
            name="descripcion_usuario"
            id="descripcion_usuario"
            maxlength="500"
        >{{ old('descripcion_usuario', $usuario->descripcion) }}</textarea>

        @if($usuario->rol->nombre === 'profesional')
            <h2>
                Información profesional
            </h2>

            @php
                $profesionActual = old(
                    'profesion_id',
                    $usuario->profesional?->profesiones->first()?->id
                );

                $especialidadesSeleccionadas = array_map(
                    'intval',
                    old(
                        'especialidades',
                        $usuario
                            ->profesional
                            ?->especialidades
                            ->pluck('id')
                            ->toArray()
                        ?? []
                    )
                );
            @endphp

            <label
                class="etiqueta"
                for="profesion_id"
            >
                Profesión:
            </label>

            <select
                class="form-control"
                name="profesion_id"
                id="profesion_id"
                required
            >
                <option value="">
                    Seleccione una profesión
                </option>

                @foreach($profesiones as $profesion)
                    <option
                        value="{{ $profesion->id }}"
                        {{ $profesionActual == $profesion->id ? 'selected' : '' }}
                    >
                        {{ $profesion->nombre }}
                    </option>
                @endforeach
            </select>

            <label class="etiqueta">
                Especialidades:
            </label>

            <div
                class="materiales"
                id="especialidadesContenedor"
            >
                @foreach($profesiones as $profesion)
                    @foreach($profesion->especialidades as $especialidad)
                        <label
                            class="material-opcion especialidad-opcion"
                            data-profesion="{{ $profesion->id }}"
                            data-requiere-aprobacion="{{ $especialidad->requiere_aprobacion ? '1' : '0' }}"
                        >
                            <input
                                type="checkbox"
                                name="especialidades[]"
                                value="{{ $especialidad->id }}"
                                {{ in_array(
                                    (int) $especialidad->id,
                                    $especialidadesSeleccionadas,
                                    true
                                ) ? 'checked' : '' }}
                            >

                            <span>
                                {{ $especialidad->nombre }}
                            </span>
                        </label>
                    @endforeach
                @endforeach
            </div>

            <div
                class="error"
                id="avisoAprobacion"
                hidden
            >
                <p>
                    La especialidad Supervisor de obra requiere aprobación administrativa. Si agregas esta especialidad, tu perfil profesional dejará de mostrarse públicamente hasta que sea aprobado.
                </p>
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
                value="{{ old('anios_experiencia', $usuario->profesional?->anios_experiencia) }}"
                required
            >

            <label
                class="etiqueta"
                for="descripcion_profesional"
            >
                Descripción profesional:
            </label>

            <textarea
                class="form-control"
                name="descripcion_profesional"
                id="descripcion_profesional"
                maxlength="500"
                required
            >{{ old('descripcion_profesional', $usuario->profesional?->descripcion) }}</textarea>

            <label
                class="etiqueta"
                for="portafolio_url"
            >
                Link del portafolio:
            </label>

            <input
                class="form-control"
                type="url"
                name="portafolio_url"
                id="portafolio_url"
                value="{{ old('portafolio_url', $usuario->profesional?->portafolio_url) }}"
            >

            <label
                class="etiqueta"
                for="portafolio_fotos"
            >
                Fotos del portafolio (máx. 3):
            </label>

            <input
                class="form-control"
                type="file"
                name="portafolio_fotos[]"
                id="portafolio_fotos"
                accept="image/png,image/jpeg,image/webp"
                multiple
            >

            <div
                id="previewPortafolio"
                class="preview-portafolio"
            >
                @if($usuario->portafolio_fotos)
                    @foreach($usuario->portafolio_fotos as $foto)
                        <img
                            src="{{ asset('storage/' . $foto) }}"
                            alt="Foto de portafolio"
                        >
                    @endforeach
                @endif
            </div>

            <label
                class="etiqueta"
                for="zona_trabajo_profesional"
            >
                Zona o ciudad donde trabaja:
            </label>

            <input
                class="form-control"
                type="text"
                name="zona_trabajo_profesional"
                id="zona_trabajo_profesional"
                value="{{ old('zona_trabajo_profesional', $usuario->profesional?->zona_trabajo) }}"
                required
            >

            <label class="etiqueta">
                Servicios que ofreces:
            </label>

            @php
                $serviciosSeleccionados = old(
                    'servicios',
                    $usuario->profesional?->servicios->pluck('id')->toArray() ?? []
                );
            @endphp

            <div class="materiales">
                @forelse($servicios as $servicio)
                    <label class="material-opcion">
                        <input
                            type="checkbox"
                            name="servicios[]"
                            value="{{ $servicio->id }}"
                            {{ in_array(
                                $servicio->id,
                                $serviciosSeleccionados
                            ) ? 'checked' : '' }}
                        >

                        <span>
                            {{ $servicio->nombre }}
                        </span>
                    </label>
                @empty
                    <p>
                        No hay servicios registrados.
                    </p>
                @endforelse
            </div>

        @elseif($usuario->rol->nombre === 'proveedor')
            <h2>
                Información del proveedor
            </h2>

            <label
                class="etiqueta"
                for="descripcion_proveedor"
            >
                Descripción de la empresa:
            </label>

            <textarea
                class="form-control"
                name="descripcion_proveedor"
                id="descripcion_proveedor"
                maxlength="500"
            >{{ old('descripcion_proveedor', $usuario->proveedor?->descripcion) }}</textarea>

            <label
                class="etiqueta"
                for="zona_trabajo_proveedor"
            >
                Zona o ciudad donde trabaja:
            </label>

            <input
                class="form-control"
                type="text"
                name="zona_trabajo_proveedor"
                id="zona_trabajo_proveedor"
                value="{{ old('zona_trabajo_proveedor', $usuario->proveedor?->zona_trabajo) }}"
                required
            >

            <label class="etiqueta">
                Materiales o productos que ofrece:
            </label>

            @php
                $materialesSeleccionados = old(
                    'materiales',
                    $usuario->proveedor?->materiales->pluck('id')->toArray() ?? []
                );
            @endphp

            <div class="materiales">
                @foreach($materiales as $material)
                    <label class="material-opcion">
                        <input
                            type="checkbox"
                            name="materiales[]"
                            value="{{ $material->id }}"
                            {{ in_array(
                                $material->id,
                                $materialesSeleccionados
                            ) ? 'checked' : '' }}
                        >

                        <span>
                            {{ $material->nombre }}
                        </span>
                    </label>
                @endforeach
            </div>

            <label
                class="etiqueta"
                for="portafolio_fotos"
            >
                Fotos del portafolio (máx. 3):
            </label>

            <input
                class="form-control"
                type="file"
                name="portafolio_fotos[]"
                id="portafolio_fotos"
                accept="image/png,image/jpeg,image/webp"
                multiple
            >

            <div
                id="previewPortafolio"
                class="preview-portafolio"
            >
                @if($usuario->portafolio_fotos)
                    @foreach($usuario->portafolio_fotos as $foto)
                        <img
                            src="{{ asset('storage/' . $foto) }}"
                            alt="Foto de portafolio"
                        >
                    @endforeach
                @endif
            </div>
        @endif

        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    <p>
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif

        <div class="acciones">
            <a
                href="{{ route('perfil') }}"
                class="btn-cancelar"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="btn-guardar"
            >
                Guardar cambios
            </button>
        </div>
    </form>
</main>

<footer class="pie">
    <div class="pie-contenido">
        <div class="pie-marca">
            <a
                href="{{ route('menu') }}"
                class="pie-logo-enlace"
                aria-label="ArquiServi"
            >
                <img
                    src="{{ asset('encabezado2.png') }}"
                    alt="ArquiServi"
                    class="pie-logo-arquiservi"
                >
            </a>

            <p class="pie-eslogan">
                Conectando personas, construyendo espacios.
            </p>

            <p class="pie-descripcion">
                Encuentra profesionales y proveedores para llevar tus proyectos a la realidad de forma sencilla, segura y confiable.
            </p>
        </div>

        <!-- COLUMNAS CENTRALES -->
        <div class="pie-columnas">
            <div class="pie-columna">
                <h3>
                    ArquiServi
                </h3>

                <nav class="pie-enlaces">
                    <a
                        href="{{ route('acerca') }}"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-circle-info"></i>

                        <span>
                            Acerca de
                        </span>
                    </a>

                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-briefcase"></i>

                        <span>
                            Servicios
                        </span>
                    </a>

                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-users"></i>

                        <span>
                            Profesionales
                        </span>
                    </a>

                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-store"></i>

                        <span>
                            Proveedores
                        </span>
                    </a>
                </nav>
            </div>

            <div class="pie-columna">
                <h3>
                    Ayuda
                </h3>

                <nav class="pie-enlaces">
                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-circle-question"></i>

                        <span>
                            Centro de ayuda
                        </span>
                    </a>

                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-envelope"></i>

                        <span>
                            Contacto
                        </span>
                    </a>

                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-shield-halved"></i>

                        <span>
                            Seguridad
                        </span>
                    </a>
                </nav>
            </div>

            <div class="pie-columna">
                <h3>
                    Legal
                </h3>

                <nav class="pie-enlaces">
                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-file-contract"></i>

                        <span>
                            Términos y condiciones
                        </span>
                    </a>

                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-lock"></i>

                        <span>
                            Privacidad
                        </span>
                    </a>

                    <a
                        href="#"
                        class="pie-enlace"
                    >
                        <i class="fa-solid fa-cookie-bite"></i>

                        <span>
                            Cookies
                        </span>
                    </a>
                </nav>
            </div>

            <div class="pie-redes">
                <span class="pie-subtitulo">
                    Síguenos
                </span>

                <div class="pie-redes-lista">
                    <a
                        href="#"
                        class="pie-red-social"
                        aria-label="Facebook"
                    >
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>

                    <a
                        href="#"
                        class="pie-red-social"
                        aria-label="Instagram"
                    >
                        <i class="fa-brands fa-instagram"></i>
                    </a>

                    <a
                        href="#"
                        class="pie-red-social"
                        aria-label="LinkedIn"
                    >
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>

                    <a
                        href="#"
                        class="pie-red-social"
                        aria-label="GitHub"
                    >
                        <i class="fa-brands fa-github"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="pie-derecha">
            <div class="pie-colaboradores">
                <span class="pie-subtitulo">
                    Colaboradores
                </span>

                <div class="pie-logos">
                    <img
                        src="{{ asset('footer/Logo_Labsol.png') }}"
                        alt="LABSOL Network"
                        class="logo-footer logo-labsol"
                    >

                    <img
                        src="{{ asset('footer/Logo_GPLv3.png') }}"
                        alt="GPLv3 Free Software"
                        class="logo-footer logo-gpl"
                    >
                </div>
            </div>
        </div>
    </div>

    <div class="pie-separador"></div>

    <div class="pie-inferior">
        <div class="pie-copyright">
            <p>
                © 2026 ArquiServi. Todos los derechos reservados.
            </p>
        </div>

        <div class="pie-inferior-enlaces">
            <a href="#">
                Aviso de privacidad
            </a>

            <span class="pie-punto"></span>

            <a href="#">
                Términos
            </a>

            <span class="pie-punto"></span>

            <a href="#">
                Contacto
            </a>
        </div>
    </div>
</footer>

<script
    src="{{ asset('js/perfil-editar.js') }}"
    defer
></script>

@if($usuario->rol->nombre === 'profesional')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const profesion = document.getElementById('profesion_id');

            const opcionesEspecialidad = Array.from(
                document.querySelectorAll('.especialidad-opcion')
            );

            const avisoAprobacion = document.getElementById('avisoAprobacion');

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

                if (avisoAprobacion) {
                    avisoAprobacion.hidden = !requiereAprobacion;
                }
            }

            function filtrarEspecialidades(limpiarSeleccion = false) {
                const profesionId = profesion.value;

                opcionesEspecialidad.forEach(opcion => {
                    const pertenece = opcion.dataset.profesion === profesionId;

                    opcion.hidden = !pertenece;

                    const checkbox = opcion.querySelector(
                        'input[type="checkbox"]'
                    );

                    if (limpiarSeleccion && !pertenece) {
                        checkbox.checked = false;
                    }
                });

                actualizarAvisoAprobacion();
            }

            profesion.addEventListener('change', function () {
                filtrarEspecialidades(true);
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

            filtrarEspecialidades(false);
        });
    </script>
@endif

</body>
</html>