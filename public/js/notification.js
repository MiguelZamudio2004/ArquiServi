document.addEventListener('DOMContentLoaded', function () {
    const boton = document.getElementById('btnNotificaciones');
    const dropdown = document.getElementById('notificacionesDropdown');
    const csrf = document.getElementById('csrf-notificaciones');

    if (!boton || !dropdown) {
        return;
    }

    boton.addEventListener('click', function (event) {
        event.stopPropagation();
        dropdown.classList.toggle('activo');
    });

    dropdown.addEventListener('click', function (event) {
        event.stopPropagation();
    });

    document.addEventListener('click', function () {
        dropdown.classList.remove('activo');
    });

    document.querySelectorAll('.notificacion').forEach((notificacion) => {
        notificacion.addEventListener('click', async function () {
            const url = this.dataset.url;

            if (!url) {
                return;
            }

            try {
                const respuesta = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf ? csrf.value : '',
                        'Accept': 'application/json'
                    }
                });

                if (!respuesta.ok) {
                    return;
                }

                const datos = await respuesta.json();

                this.classList.remove('no-leida');
                this.classList.add('leida');

                const indicador = this.querySelector(
                    '.indicador-no-leida'
                );

                if (indicador) {
                    indicador.remove();
                }

                actualizarContador(datos.unread_count);

                if (datos.redirect_url) {
                    window.location.href = datos.redirect_url;
                }
            } catch (error) {
                console.error(error);
            }
        });
    });

    function actualizarContador(cantidad) {
        const contador = document.getElementById(
            'contadorNotificaciones'
        );

        if (cantidad <= 0) {
            if (contador) {
                contador.remove();
            }

            return;
        }

        if (contador) {
            contador.textContent = cantidad;
        }
    }
});