@php
    $nombreEvaluado = trim(
        ($evaluado->nombre ?? '') . ' ' .
        ($evaluado->apellido_paterno ?? '') . ' ' .
        ($evaluado->apellido_materno ?? '')
    );

    $fotoEvaluado = $evaluado->foto_perfil ?? null;

    $inicialEvaluado = mb_strtoupper(
        mb_substr($nombreEvaluado, 0, 1)
    );
@endphp

<div
    class="modal-calificacion"
    id="modalCalificacion"
    data-tipo="{{ $tipo }}"
    data-submit-url="{{ route('solicitudes.calificar.guardar', $solicitud) }}"
    data-csrf="{{ csrf_token() }}">

    <div class="modal-calificacion-contenido">

        <section class="persona-evaluada">

            <div class="persona-foto">
                @if($fotoEvaluado)
                    <img
                        src="{{ asset('storage/' . $fotoEvaluado) }}"
                        alt="Foto de {{ $nombreEvaluado }}">
                @else
                    <span>{{ $inicialEvaluado }}</span>
                @endif
            </div>

            <div class="persona-info">
                <span class="evaluando-texto">
                    Estás calificando a
                </span>

                <h3>{{ $nombreEvaluado }}</h3>

                <span
                    class="tipo-persona"
                    id="tipoPersona">
                    {{ ucfirst($tipo) }}
                </span>
            </div>

        </section>

        <div class="separador-calificacion"></div>

        <div class="encabezado-calificacion">
            <h2 id="subtitulo">
                Califica tu experiencia
            </h2>

            <p class="parrafo-calificacion">
                Evalúa cada aspecto según tu experiencia.
            </p>
        </div>

        <div class="separador-calificacion"></div>

        <section class="criterios-seccion">

            <h3>Aspectos a evaluar</h3>

            <div id="criteriosContainer"></div>

        </section>

        <div class="separador-calificacion"></div>

        <section class="comentario-seccion">

            <label for="comentario">
                Cuéntanos tu experiencia
            </label>

            <textarea
                id="comentario"
                maxlength="500"
                placeholder="Escribe tu opinión..."
            ></textarea>

            <div class="contador-comentario">
                <span id="contadorCaracteres">0</span>/500
            </div>

        </section>

        <div
            id="mensajeError"
            class="mensaje-error">
        </div>

        <div class="acciones-modal">

            <button
                type="button"
                id="cancelarCalificacion">
                Cancelar
            </button>

            <button
                type="button"
                id="publicarCalificacion">
                Publicar
            </button>

        </div>

    </div>

</div>