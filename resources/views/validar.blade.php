<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validación de código</title>
    <link rel="stylesheet" href="{{ asset('css/validar.css') }}">
    <link rel="icon" href="{{ asset('icono.png') }}" type="image/png">
</head>
<body>
    <header class="encabezado">
    <img src="{{ asset('encabezado2.png') }}" class="logo" alt="ArquiServi">
    </header>

    <section class="form-validar">
        <form action="{{ route('recuperacion.validar') }}" method="POST">
            @csrf
            <h2 id="subtitulo">Validar</h2>
            <label class="etiqueta">Hemos enviado un código a tu correo electrónico. Por favor, ingresa el código para continuar:</label>
            <section class="codigo-container">
                <input class="codigo" type="text" name="codigo[]" maxlength="1" inputmode="numeric" autocomplete="off">
                <input class="codigo" type="text" name="codigo[]" maxlength="1" inputmode="numeric" autocomplete="off">
                <input class="codigo" type="text" name="codigo[]" maxlength="1" inputmode="numeric" autocomplete="off">
                <input class="codigo" type="text" name="codigo[]" maxlength="1" inputmode="numeric" autocomplete="off">
                <input class="codigo" type="text" name="codigo[]" maxlength="1" inputmode="numeric" autocomplete="off">
                <input class="codigo" type="text" name="codigo[]" maxlength="1" inputmode="numeric" autocomplete="off">
            </section>

    @error('codigo')
        <p class="error">{{ $message }}</p>
    @enderror

            
            <button class="btn">Validar código</button>
        </form>

        <form action="{{ route('recuperacion.reenviar') }}" method="POST" class="reenviar-form">
            @csrf
            <p class="text-alter">No recibiste ningún código?</p>
            <button type="submit" class="reenviar-codigo">Reenviar código</button>
            
        </form>

    </section>

    <script>
        const inputs = document.querySelectorAll('.codigo-container input');

        inputs.forEach((input, index) => {

            input.addEventListener('input', function () {

                this.value = this.value.replace(/[^0-9]/g, '');

                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }

            });

            input.addEventListener('keydown', function (event) {

                
                if (
                    event.key === 'Backspace' &&
                    this.value === '' &&
                    index > 0
                ) {
                    inputs[index - 1].focus();
                }

            });

        });
    </script>

</body>

<footer class="pie">
    <p>© 2026 ArquiServi. Todos los derechos reservados.</p>
</footer>

</html>