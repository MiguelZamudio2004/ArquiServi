document.addEventListener('DOMContentLoaded', function () {
    const carrusel = document.getElementById('portafolioCarrusel');

    if (!carrusel) {
        return;
    }

    const slides = Array.from(
        carrusel.querySelectorAll('.portafolio-slide')
    );

    if (slides.length === 0) {
        return;
    }

    const botonAnterior =
        document.getElementById('portafolioAnterior');

    const botonSiguiente =
        document.getElementById('portafolioSiguiente');

    const contadorActual =
        document.getElementById('portafolioActual');

    const trabajoActual =
        document.getElementById('portafolioTrabajoActual');

    const modal =
        document.getElementById('portafolioModal');

    const modalFondo =
        document.getElementById('portafolioModalFondo');

    const cerrarModal =
        document.getElementById('cerrarPortafolio');

    const imagenGrande =
        document.getElementById('imagenPortafolioGrande');

    const botonesImagen = Array.from(
        carrusel.querySelectorAll('.portafolio-imagen-boton')
    );

    let indiceActual = 0;
    let inicioTouchX = 0;
    let finTouchX = 0;
    let animando = false;

    function normalizarIndice(indice) {
        if (indice < 0) {
            return slides.length - 1;
        }

        if (indice >= slides.length) {
            return 0;
        }

        return indice;
    }

    function actualizarInformacion() {
        if (contadorActual) {
            contadorActual.textContent =
                indiceActual + 1;
        }

        if (trabajoActual) {
            trabajoActual.textContent =
                indiceActual + 1;
        }
    }

    function cambiarImagen(nuevoIndice, direccion = 1) {
        if (
            animando ||
            slides.length <= 1
        ) {
            return;
        }

        nuevoIndice = normalizarIndice(
            nuevoIndice
        );

        if (nuevoIndice === indiceActual) {
            return;
        }

        animando = true;

        const slideActual =
            slides[indiceActual];

        const slideNuevo =
            slides[nuevoIndice];

        slideActual.classList.remove(
            'saliendo-izquierda',
            'saliendo-derecha'
        );

        slideNuevo.classList.remove(
            'saliendo-izquierda',
            'saliendo-derecha'
        );

        if (direccion > 0) {
            slideActual.classList.add(
                'saliendo-izquierda'
            );
        } else {
            slideActual.classList.add(
                'saliendo-derecha'
            );
        }

        slideActual.classList.remove(
            'activo'
        );

        slideNuevo.classList.add(
            'activo'
        );

        indiceActual = nuevoIndice;

        actualizarInformacion();

        window.setTimeout(function () {
            slideActual.classList.remove(
                'saliendo-izquierda',
                'saliendo-derecha'
            );

            animando = false;
        }, 430);
    }

    function siguienteImagen() {
        cambiarImagen(
            indiceActual + 1,
            1
        );
    }

    function anteriorImagen() {
        cambiarImagen(
            indiceActual - 1,
            -1
        );
    }

    if (botonSiguiente) {
        botonSiguiente.addEventListener(
            'click',
            siguienteImagen
        );
    }

    if (botonAnterior) {
        botonAnterior.addEventListener(
            'click',
            anteriorImagen
        );
    }

    carrusel.addEventListener(
        'touchstart',
        function (event) {
            if (
                event.changedTouches.length === 0
            ) {
                return;
            }

            inicioTouchX =
                event.changedTouches[0].clientX;
        },
        {
            passive: true
        }
    );

    carrusel.addEventListener(
        'touchend',
        function (event) {
            if (
                event.changedTouches.length === 0 ||
                slides.length <= 1
            ) {
                return;
            }

            finTouchX =
                event.changedTouches[0].clientX;

            const diferencia =
                finTouchX - inicioTouchX;

            if (Math.abs(diferencia) < 50) {
                return;
            }

            if (diferencia < 0) {
                siguienteImagen();
            } else {
                anteriorImagen();
            }
        },
        {
            passive: true
        }
    );

    function abrirModal(imagen) {
        if (
            !modal ||
            !imagenGrande ||
            !imagen
        ) {
            return;
        }

        imagenGrande.src = imagen;

        imagenGrande.alt =
            'Imagen ampliada del portafolio';

        modal.classList.add('activo');

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'modal-portafolio-abierto'
        );
    }

    function cerrarVisor() {
        if (!modal) {
            return;
        }

        modal.classList.remove('activo');

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.classList.remove(
            'modal-portafolio-abierto'
        );

        if (imagenGrande) {
            imagenGrande.src = '';
        }
    }

    botonesImagen.forEach(function (boton) {
        boton.addEventListener(
            'click',
            function () {
                abrirModal(
                    this.dataset.imagen
                );
            }
        );
    });

    if (cerrarModal) {
        cerrarModal.addEventListener(
            'click',
            cerrarVisor
        );
    }

    if (modalFondo) {
        modalFondo.addEventListener(
            'click',
            cerrarVisor
        );
    }

    document.addEventListener(
        'keydown',
        function (event) {
            if (
                modal &&
                modal.classList.contains('activo')
            ) {
                if (event.key === 'Escape') {
                    cerrarVisor();
                }

                return;
            }

            if (event.key === 'ArrowRight') {
                siguienteImagen();
            }

            if (event.key === 'ArrowLeft') {
                anteriorImagen();
            }
        }
    );

    actualizarInformacion();
});