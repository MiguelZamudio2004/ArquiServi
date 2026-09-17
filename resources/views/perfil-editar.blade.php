<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="{{ asset('css/perfil-editar.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png"> 
    <script src="{{ asset('js/perfil-editar.js') }}" defer></script>  
    <script src="{{ asset('js/perfil.js')}}" defer></script>
</head>
<body>
    <header class="encabezado">
        <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
        @auth
        <div class="acciones-usuario">
        <div class="perfil-container">
            <button type="button" class="perfil-boton" id="btnPerfil">
                <div class="perfil-avatar">@if(auth()->user()->foto_perfil)
        <img src="{{ asset('storage/' . auth()->user()->foto_perfil) }}" alt="Foto de perfil">
        @else
            {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
    @endif
</div>
                <span class="perfil-nombre">{{ auth()->user()->nombre }}</span>
                <span class="perfil-flecha">▼</span>
            </button>

            <div class="perfil-dropdown" id="perfilDropdown">
                <div class="perfil-info">
                    <strong>{{ auth()->user()->nombre }} {{ auth()->user()->apellido_paterno }}</strong>
                    <span>{{ auth()->user()->correo }}</span>
                </div>

                <a href="{{ route('perfil') }}" class="perfil-opcion">Mi Perfil </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="perfil-opcion cerrar-sesion">Cerrar sesión</button>
                </form>
            </div>
        </div>
        </div>
    </div>
    @endauth
    </header>

    <main class="editar-perfil">
    <h1>Editar perfil</h1>

    <form action="{{ route('perfil.actualizar') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="foto-actual" id="previewFoto">
            @if($usuario->foto_perfil)
                <img src="{{ asset('storage/' . $usuario->foto_perfil) }}" alt="Foto de perfil">
            @else
                <span>{{ strtoupper(substr($usuario->nombre, 0, 1)) }}</span>
            @endif
        </div>

        <label class="etiqueta">Foto de perfil</label>

        <div class="selector-archivo">
            <label for="foto_perfil" class="btn-archivo">Seleccionar imagen</label>
            <span id="nombreArchivo">Ningún archivo seleccionado</span>
        </div>

        <input type="file" name="foto_perfil" id="foto_perfil" accept="image/png,image/jpeg,image/webp" hidden>

        <label class="etiqueta" for="nombre">Nombre</label>
        <input class="form-control" type="text" name="nombre" id="nombre" value="{{ old('nombre', $usuario->nombre) }}" required>

        <label class="etiqueta" for="apellido_paterno">Apellido paterno</label>
        <input class="form-control" type="text" name="apellido_paterno" id="apellido_paterno" value="{{ old('apellido_paterno', $usuario->apellido_paterno) }}" required>

        <label class="etiqueta" for="apellido_materno">Apellido materno</label>
        <input class="form-control" type="text" name="apellido_materno" id="apellido_materno" value="{{ old('apellido_materno', $usuario->apellido_materno) }}">

        <label class="etiqueta" for="telefono">Teléfono</label>
        <input class="form-control" type="text" name="telefono" id="telefono" value="{{ old('telefono', $usuario->telefono) }}" required>

        <label class="etiqueta" for="ubicacion">Ubicación</label>
        <input class="form-control" type="text" name="ubicacion" id="ubicacion" value="{{ old('ubicacion', $usuario->ubicacion) }}">

        <label class="etiqueta" for="descripcion">Descripción</label>
        <textarea class="form-control descripcion" name="descripcion" id="descripcion" maxlength="500">{{ old('descripcion', $usuario->descripcion) }}</textarea>

        <div class="contador-descripcion">
            <span id="contadorDescripcion">0</span>/500
        </div>

        @if($errors->any())
            <div class="errores">
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

</body>
</html>