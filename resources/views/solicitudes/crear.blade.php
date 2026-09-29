<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nueva solicitud - ArquiServi</title>

    <link rel="stylesheet" href="{{ asset('css/solicitudes.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
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

<main class="solicitudes-contenedor">

    <h1>
        Nueva solicitud
    </h1>

    @if($errors->any())

        <div class="errores">

            @foreach($errors->all() as $error)

                <p>
                    {{ $error }}
                </p>

            @endforeach

        </div>

    @endif

    <section class="solicitud-formulario">

        <div class="solicitud-destinatario">

            <span>
                {{ $destinatario->rol->nombre === 'proveedor' ? 'Proveedor' : 'Profesional' }}
            </span>

            <strong>
                {{ $destinatario->nombre }}
                {{ $destinatario->apellido_paterno }}
            </strong>

        </div>

        <form
            action="{{ route('solicitudes.guardar') }}"
            method="POST"
        >

            @csrf

            <input
                type="hidden"
                name="destinatario_id"
                value="{{ $destinatario->id }}"
            >

            @if($destinatario->rol->nombre === 'profesional')

                <div class="campo">

                    <label for="servicio_id">
                        Servicio
                    </label>

                    <select
                        name="servicio_id"
                        id="servicio_id"
                        required
                    >

                        <option value="">
                            Selecciona un servicio
                        </option>

                        @foreach($destinatario->profesional->servicios as $servicio)

                            <option
                                value="{{ $servicio->id }}"
                                {{ old('servicio_id') == $servicio->id ? 'selected' : '' }}
                            >
                                {{ $servicio->nombre }}
                            </option>

                        @endforeach

                    </select>

                </div>

            @endif

            @if($destinatario->rol->nombre === 'proveedor')

                <div class="campo">

                    <label for="material_id">
                        Material o producto
                    </label>

                    <select
                        name="material_id"
                        id="material_id"
                        required
                    >

                        <option value="">
                            Selecciona un material o producto
                        </option>

                        @foreach($destinatario->proveedor->materiales as $material)

                            @if($material->pivot->disponible)

                                <option
                                    value="{{ $material->id }}"
                                    {{ old('material_id') == $material->id ? 'selected' : '' }}
                                >
                                    {{ $material->nombre }}
                                </option>

                            @endif

                        @endforeach

                    </select>

                </div>

            @endif

            <div class="campo">

                <label for="descripcion">
                    Descripción
                </label>

                <textarea
                    name="descripcion"
                    id="descripcion"
                    maxlength="1000"
                    required
                    placeholder="Describe lo que necesitas..."
                >{{ old('descripcion') }}</textarea>

            </div>

            <div class="acciones">

                <a
                    href="{{ route('perfil.publico', $destinatario) }}"
                    class="btn-volver"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn-enviar"
                >
                    Enviar solicitud
                </button>

            </div>

        </form>

    </section>

</main>

<footer class="pie">
    <p>
        © 2026 ArquiServi. Todos los derechos reservados.
    </p>
</footer>

</body>
</html>