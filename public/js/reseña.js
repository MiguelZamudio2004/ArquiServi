const criteriosPorTipo = {
    profesional: [
        {
            clave: 'calidad_trabajo',
            nombre: 'Calidad del trabajo'
        },
        {
            clave: 'puntualidad',
            nombre: 'Puntualidad'
        },
        {
            clave: 'comunicacion',
            nombre: 'Comunicación'
        },
        {
            clave: 'profesionalismo',
            nombre: 'Profesionalismo'
        },
        {
            clave: 'cumplimiento',
            nombre: 'Cumplimiento de lo acordado'
        }
    ],

    cliente: [
        {
            clave: 'claridad_requerimientos',
            nombre: 'Claridad en los requerimientos'
        },
        {
            clave: 'comunicacion',
            nombre: 'Comunicación'
        },
        {
            clave: 'trato',
            nombre: 'Trato'
        },
        {
            clave: 'responsabilidad',
            nombre: 'Responsabilidad'
        },
        {
            clave: 'cumplimiento',
            nombre: 'Cumplimiento de lo acordado'
        }
    ],

    proveedor: [
        {
            clave: 'calidad_materiales',
            nombre: 'Calidad de los materiales'
        },
        {
            clave: 'cumplimiento_entrega',
            nombre: 'Cumplimiento en la entrega'
        },
        {
            clave: 'comunicacion',
            nombre: 'Comunicación'
        },
        {
            clave: 'atencion',
            nombre: 'Atención'
        },
        {
            clave: 'cumplimiento',
            nombre: 'Cumplimiento de lo acordado'
        }
    ]
};

let calificaciones = {};
let botonCalificacionActivo = null;

document.addEventListener('click', async function(event) {
    const boton =
        event.target.closest(
            '.btn-abrir-calificacion'
        );

    if (!boton) {
        return;
    }

    const url =
        boton.dataset.url;

    if (!url) {
        return;
    }

    botonCalificacionActivo =
        boton;

    try {
        const respuesta =
            await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With':
                        'XMLHttpRequest'
                }
            });

        if (!respuesta.ok) {
            const mensaje =
                await respuesta.text();

            throw new Error(
                mensaje ||
                'No se pudo abrir la calificación.'
            );
        }

        const html =
            await respuesta.text();

        const contenedor =
            document.getElementById(
                'modalCalificacionContenedor'
            );

        if (!contenedor) {
            return;
        }

        contenedor.innerHTML =
            html;

        document.body.classList.add(
            'modal-abierto'
        );

        inicializarCalificacion();

    } catch (error) {
        alert(
            'No fue posible abrir la ventana de calificación.'
        );
    }
});

function inicializarCalificacion() {
    const modal =
        document.getElementById(
            'modalCalificacion'
        );

    if (!modal) {
        return;
    }

    const criteriosContainer =
        document.getElementById(
            'criteriosContainer'
        );

    const comentario =
        document.getElementById(
            'comentario'
        );

    const contadorCaracteres =
        document.getElementById(
            'contadorCaracteres'
        );

    const botonCancelar =
        document.getElementById(
            'cancelarCalificacion'
        );

    const botonPublicar =
        document.getElementById(
            'publicarCalificacion'
        );

    const mensajeError =
        document.getElementById(
            'mensajeError'
        );

    const subtitulo =
        document.getElementById(
            'subtitulo'
        );

    const tipoPersona =
        document.getElementById(
            'tipoPersona'
        );

    const submitUrl =
        modal.dataset.submitUrl;

    const csrf =
        modal.dataset.csrf;

    let tipoEvaluado =
        modal.dataset.tipo ||
        'profesional';

    const tiposPermitidos = [
        'profesional',
        'cliente',
        'proveedor'
    ];

    if (
        !tiposPermitidos.includes(
            tipoEvaluado
        )
    ) {
        tipoEvaluado =
            'profesional';
    }

    calificaciones = {};

    configurarInterfaz(
        tipoEvaluado,
        subtitulo,
        tipoPersona
    );

    cargarCriterios(
        tipoEvaluado,
        criteriosContainer
    );

    activarEstrellas(
        mensajeError
    );

    if (
        comentario &&
        contadorCaracteres
    ) {
        comentario.addEventListener(
            'input',
            function() {
                contadorCaracteres.textContent =
                    comentario.value.length;
            }
        );
    }

    if (botonCancelar) {
        botonCancelar.addEventListener(
            'click',
            function() {
                cerrarCalificacion();
            }
        );
    }

    if (botonPublicar) {
        botonPublicar.addEventListener(
            'click',
            async function() {
                if (
                    !validarCalificacion(
                        mensajeError
                    )
                ) {
                    return;
                }

                botonPublicar.disabled =
                    true;

                botonPublicar.textContent =
                    'Publicando...';

                try {
                    const respuesta =
                        await fetch(
                            submitUrl,
                            {
                                method: 'POST',
                                headers: {
                                    'Content-Type':
                                        'application/json',

                                    'Accept':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        csrf,

                                    'X-Requested-With':
                                        'XMLHttpRequest'
                                },

                                body:
                                    JSON.stringify({
                                        criterios:
                                            calificaciones,

                                        comentario:
                                            comentario
                                                .value
                                                .trim()
                                    })
                            }
                        );

                    const datos =
                        await respuesta.json();

                    if (!respuesta.ok) {
                        let mensaje =
                            datos.message ||
                            'No fue posible guardar la calificación.';

                        if (datos.errors) {
                            const errores =
                                Object.values(
                                    datos.errors
                                ).flat();

                            if (
                                errores.length > 0
                            ) {
                                mensaje =
                                    errores[0];
                            }
                        }

                        throw new Error(
                            mensaje
                        );
                    }

                    cerrarCalificacion();

                    if (
                        botonCalificacionActivo
                    ) {
                        botonCalificacionActivo
                            .classList
                            .remove(
                                'btn-abrir-calificacion'
                            );

                        botonCalificacionActivo
                            .classList
                            .add(
                                'btn-calificado'
                            );

                        botonCalificacionActivo
                            .textContent =
                            'Calificado';

                        botonCalificacionActivo
                            .disabled =
                            true;
                    }

                } catch (error) {
                    mostrarError(
                        mensajeError,
                        error.message
                    );

                    botonPublicar.disabled =
                        false;

                    botonPublicar.textContent =
                        'Publicar';
                }
            }
        );
    }

    modal.addEventListener(
        'click',
        function(event) {
            if (
                event.target === modal
            ) {
                cerrarCalificacion();
            }
        }
    );
}

function configurarInterfaz(
    tipoEvaluado,
    subtitulo,
    tipoPersona
) {
    const titulos = {
        profesional:
            'Califica al profesional',

        cliente:
            'Califica al cliente',

        proveedor:
            'Califica al proveedor'
    };

    const tipos = {
        profesional:
            'Profesional',

        cliente:
            'Cliente',

        proveedor:
            'Proveedor'
    };

    if (subtitulo) {
        subtitulo.textContent =
            titulos[tipoEvaluado];
    }

    if (tipoPersona) {
        tipoPersona.textContent =
            tipos[tipoEvaluado];
    }
}

function cargarCriterios(
    tipoEvaluado,
    criteriosContainer
) {
    if (!criteriosContainer) {
        return;
    }

    criteriosContainer.innerHTML =
        '';

    criteriosPorTipo[
        tipoEvaluado
    ].forEach(criterio => {

        calificaciones[
            criterio.clave
        ] = 0;

        const contenedor =
            document.createElement(
                'div'
            );

        contenedor.classList.add(
            'criterio'
        );

        const label =
            document.createElement(
                'label'
            );

        label.textContent =
            criterio.nombre;

        const estrellas =
            document.createElement(
                'div'
            );

        estrellas.classList.add(
            'estrellas'
        );

        estrellas.dataset.criterio =
            criterio.clave;

        for (
            let valor = 1;
            valor <= 5;
            valor++
        ) {
            const estrella =
                document.createElement(
                    'button'
                );

            estrella.type =
                'button';

            estrella.classList.add(
                'estrella'
            );

            estrella.dataset.value =
                valor;

            estrella.setAttribute(
                'aria-label',
                `${valor} estrellas`
            );

            estrella.textContent =
                '★';

            estrella.style.setProperty(
                '--relleno',
                '0%'
            );

            estrellas.appendChild(
                estrella
            );
        }

        contenedor.appendChild(
            label
        );

        contenedor.appendChild(
            estrellas
        );

        criteriosContainer
            .appendChild(
                contenedor
            );
    });
}

function activarEstrellas(
    mensajeError
) {
    document
        .querySelectorAll(
            '#modalCalificacion .estrellas'
        )
        .forEach(estrellasDiv => {

            const criterio =
                estrellasDiv
                    .dataset
                    .criterio;

            if (!criterio) {
                return;
            }

            const estrellas =
                estrellasDiv
                    .querySelectorAll(
                        '.estrella'
                    );

            estrellas.forEach(
                estrella => {

                    estrella
                        .addEventListener(
                            'mousemove',
                            function(event) {
                                const valor =
                                    obtenerValorEstrella(
                                        estrella,
                                        event
                                    );

                                actualizarEstrellas(
                                    estrellas,
                                    valor
                                );
                            }
                        );

                    estrella
                        .addEventListener(
                            'click',
                            function(event) {
                                const valor =
                                    obtenerValorEstrella(
                                        estrella,
                                        event
                                    );

                                calificaciones[
                                    criterio
                                ] = valor;

                                actualizarEstrellas(
                                    estrellas,
                                    valor
                                );

                                ocultarError(
                                    mensajeError
                                );
                            }
                        );
                }
            );

            estrellasDiv
                .addEventListener(
                    'mouseleave',
                    function() {
                        actualizarEstrellas(
                            estrellas,
                            calificaciones[
                                criterio
                            ] || 0
                        );
                    }
                );
        });
}

function obtenerValorEstrella(
    estrella,
    event
) {
    const rect =
        estrella
            .getBoundingClientRect();

    const posicion =
        event.clientX -
        rect.left;

    const mitad =
        rect.width / 2;

    const valorBase =
        Number(
            estrella.dataset.value
        );

    if (posicion <= mitad) {
        return valorBase - 0.5;
    }

    return valorBase;
}

function actualizarEstrellas(
    estrellas,
    valor
) {
    estrellas.forEach(
        estrella => {

            const valorEstrella =
                Number(
                    estrella.dataset.value
                );

            let relleno = 0;

            if (
                valor >=
                valorEstrella
            ) {
                relleno = 100;

            } else if (
                valor >=
                valorEstrella - 0.5
            ) {
                relleno = 50;
            }

            estrella.style
                .setProperty(
                    '--relleno',
                    `${relleno}%`
                );
        }
    );
}

function validarCalificacion(
    mensajeError
) {
    const valores =
        Object.values(
            calificaciones
        );

    if (
        valores.length === 0
    ) {
        mostrarError(
            mensajeError,
            'No hay aspectos disponibles para evaluar.'
        );

        return false;
    }

    const incompleta =
        valores.some(
            valor =>
                valor === 0
        );

    if (incompleta) {
        mostrarError(
            mensajeError,
            'Califica todos los aspectos antes de publicar.'
        );

        return false;
    }

    ocultarError(
        mensajeError
    );

    return true;
}

function mostrarError(
    elemento,
    mensaje
) {
    if (!elemento) {
        return;
    }

    elemento.textContent =
        mensaje;

    elemento.classList.add(
        'activo'
    );
}

function ocultarError(
    elemento
) {
    if (!elemento) {
        return;
    }

    elemento.textContent =
        '';

    elemento.classList.remove(
        'activo'
    );
}

function cerrarCalificacion() {
    const contenedor =
        document.getElementById(
            'modalCalificacionContenedor'
        );

    if (contenedor) {
        contenedor.innerHTML =
            '';
    }

    document.body
        .classList
        .remove(
            'modal-abierto'
        );

    calificaciones = {};
}