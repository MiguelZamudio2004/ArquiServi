<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificación</title>
    <link rel="stylesheet" href="{{ asset('css/calificacion.css') }}">
    <script src="{{ asset('js/reseña.js')}}" defer></script>
</head>
<body>
    <div class="modal" id="modalCalificacion">
        <div class="modal-content">
        <h2 id="subtitulo">Califica tu experiencia</h2>
        <p class="parrafo">Comparte detalles sobre tu experiencia.</p>

        <!-- Sección de estrellas -->
        <div class="estrellas">
            <span class="estrella" data-value="1">&#9733;</span>
            <span class="estrella" data-value="2">&#9733;</span>
            <span class="estrella" data-value="3">&#9733;</span>
            <span class="estrella" data-value="4">&#9733;</span>
            <span class="estrella" data-value="5">&#9733;</span>
        </div>

        <textarea id="comentario" placeholder="Escribe tu opinión..."></textarea>

        <div class="acciones">
            <button id="cancelar">Cancelar</button>
            <button id="publicar">Publicar</button>
        </div>
        </div>
    </div>

</body>
</html>