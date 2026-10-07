<div
    class="arquiservi-confirmacion"
    id="arquiserviConfirmacion"
    aria-hidden="true"
>
    <div
        class="arquiservi-confirmacion-fondo"
        data-confirmacion-cerrar
    ></div>

    <div
        class="arquiservi-confirmacion-contenido"
        role="dialog"
        aria-modal="true"
        aria-labelledby="arquiserviConfirmacionTitulo"
        aria-describedby="arquiserviConfirmacionMensaje"
    >

        <button
            type="button"
            class="arquiservi-confirmacion-cerrar"
            data-confirmacion-cerrar
            aria-label="Cerrar"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div
            class="arquiservi-confirmacion-icono"
            id="arquiserviConfirmacionIcono"
        >
            <i class="fa-solid fa-circle-question"></i>
        </div>

        <span class="arquiservi-confirmacion-marca">
            ArquiServi
        </span>

        <h2 id="arquiserviConfirmacionTitulo">
            Confirmar acción
        </h2>

        <p id="arquiserviConfirmacionMensaje">
            ¿Deseas continuar con esta acción?
        </p>

        <div class="arquiservi-confirmacion-acciones">

            <button
                type="button"
                class="arquiservi-confirmacion-boton cancelar"
                data-confirmacion-cerrar
            >
                Cancelar
            </button>

            <button
                type="button"
                class="arquiservi-confirmacion-boton confirmar"
                id="arquiserviConfirmacionAceptar"
            >
                <i
                    class="fa-solid fa-check"
                    id="arquiserviConfirmacionBotonIcono"
                ></i>

                <span id="arquiserviConfirmacionBotonTexto">
                    Confirmar
                </span>
            </button>

        </div>

    </div>
</div>