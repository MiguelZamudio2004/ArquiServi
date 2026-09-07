<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña</title>
</head>
<body>
    <section>
        <form action="{{ route('recuperacion.cambiar') }}" method="POST">
            @csrf 
        <h2>Nueva Contraseña</h2>
        <label>Ingrese su nueva contraseña:</label>
        <input type="password" placeholder="Nueva contraseña" name="password" required>

        @error('password')
            <span class="error">{{ $message }}</span>
        @enderror

        <label>Confirme su nueva contraseña:</label>
        <input type="password" placeholder="Confirmar contraseña" name="password_confirmation" required>
        <button>Cambiar contraseña</button>
    </form>
    </section>
    
</body>
</html>