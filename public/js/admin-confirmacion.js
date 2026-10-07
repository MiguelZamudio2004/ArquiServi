document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById(
        'arquiserviConfirmacion'
    );

    if (!modal) {
        return;
    }

    const titulo = document.getElementById(
        'arquiserviConfirmacionTitulo'
    );

    const mensaje = document.getElementById(
        'arquiserviConfirmacionMensaje'
    );

    const icono = document.getElementById(
        'arquiserviConfirmacionIcono'
    );

    const botonAceptar = document.getElementById(
        'arquiserviConfirmacionAceptar'
    );

    const botonTexto = document.getElementById(
        'arquiserviConfirmacionBotonTexto'
    );

    const botonIcono = document.getElementById(
        'arquiserviConfirmacionBotonIcono'
    );

    const botonesCerrar = modal.querySelectorAll(
        '[data-confirmacion-cerrar]'
    );

    let formularioActual = null;
    let elementoAnterior = null;

    const configuraciones = {
        aprobar: {
            clase: 'tipo-aprobar',
            iconoModal: 'fa-circle-check',
            iconoBoton: 'fa-check'
        },

        rechazar: {
            clase: 'tipo-rechazar',
            iconoModal: 'fa-circle-xmark',
            iconoBoton: 'fa-xmark'
        },

        advertencia: {
            clase: 'tipo-advertencia',
            iconoModal: 'fa-triangle-exclamation',
            iconoBoton: 'fa-check'
        },

        normal: {
            clase: 'tipo-normal',
            iconoModal: 'fa-circle-question',
            iconoBoton: 'fa-check'
        }
    };

    const limpiarTipos = () => {
        modal.classList.remove(
            'tipo-aprobar',
            'tipo-rechazar',
            'tipo-advertencia',
            'tipo-normal'
        );
    };

    const cerrarModal = (
        devolverFoco = true
    ) => {
        modal.classList.remove('activo');

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.classList.remove(
            'arquiservi-modal-abierto'
        );

        if (
            devolverFoco &&
            elementoAnterior
        ) {
            elementoAnterior.focus();
        }

        formularioActual = null;
        elementoAnterior = null;
    };

    const abrirModal = (
        formulario
    ) => {
        formularioActual = formulario;

        elementoAnterior =
            document.activeElement;

        const tipo =
            formulario.dataset.confirmTipo
            || 'normal';

        const configuracion =
            configuraciones[tipo]
            || configuraciones.normal;

        const tituloTexto =
            formulario.dataset.confirmTitulo
            || 'Confirmar acción';

        const mensajeTexto =
            formulario.dataset.confirmMensaje
            || '¿Deseas continuar con esta acción?';

        const botonTextoValor =
            formulario.dataset.confirmBoton
            || 'Confirmar';

        limpiarTipos();

        modal.classList.add(
            configuracion.clase
        );

        titulo.textContent =
            tituloTexto;

        mensaje.textContent =
            mensajeTexto;

        botonTexto.textContent =
            botonTextoValor;

        icono.innerHTML =
            `<i class="fa-solid ${configuracion.iconoModal}"></i>`;

        botonIcono.className =
            `fa-solid ${configuracion.iconoBoton}`;

        modal.classList.add('activo');

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'arquiservi-modal-abierto'
        );

        window.setTimeout(() => {
            botonAceptar.focus();
        }, 100);
    };

    document.addEventListener(
        'submit',
        (event) => {
            const formulario =
                event.target.closest(
                    '.js-confirm-form'
                );

            if (!formulario) {
                return;
            }

            event.preventDefault();

            if (!formulario.checkValidity()) {
                formulario.reportValidity();
                return;
            }

            abrirModal(formulario);
        }
    );

    botonAceptar.addEventListener(
        'click',
        () => {
            if (!formularioActual) {
                return;
            }

            const formulario =
                formularioActual;

            cerrarModal(false);

            HTMLFormElement
                .prototype
                .submit
                .call(formulario);
        }
    );

    botonesCerrar.forEach(
        (boton) => {
            boton.addEventListener(
                'click',
                () => {
                    cerrarModal();
                }
            );
        }
    );

    document.addEventListener(
        'keydown',
        (event) => {
            if (
                event.key === 'Escape' &&
                modal.classList.contains(
                    'activo'
                )
            ) {
                cerrarModal();
            }
        }
    );
});