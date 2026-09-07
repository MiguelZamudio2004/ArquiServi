<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validación de código</title>
</head>
<body>
    <section>
        <form action="{{ route('recuperacion.validar') }}" method="POST">
            @csrf
    <h2>Validar</h2>
    <label>Hemos enviado un código a tu correo electrónico. Por favor, ingresa el código para continuar:</label>
    <input type="text" name="codigo[]" maxlength="1" inputmode="numeric" required>
    <input type="text" name="codigo[]" maxlength="1" inputmode="numeric" required>
    <input type="text" name="codigo[]" maxlength="1" inputmode="numeric" required>
    <input type="text" name="codigo[]" maxlength="1" inputmode="numeric" required>
    <input type="text" name="codigo[]" maxlength="1" inputmode="numeric" required>
    <input type="text" name="codigo[]" maxlength="1" inputmode="numeric" required>

    @error('codigo')
        <span class="error">{{ $message }}</span>
    @enderror

    <p>No recibiste ningun codigo? <a href="#">Reenviar código</a></p>
    <button>Validar código</button>
</form>
    </section>
</body>
</html>