const btnNotificaciones = document.getElementById('btnNotificaciones');
const dropdownNotificaciones = document.getElementById('notificacionesDropdown');
const csrfToken = document.getElementById('csrf-notificaciones');

if (btnNotificaciones && dropdownNotificaciones) {
    btnNotificaciones.addEventListener('click', function (event) {
        event.stopPropagation();
        dropdownNotificaciones.classList.toggle('activo');
    });

    dropdownNotificaciones.addEventListener('click', function (event) {
        event.stopPropagation();
    });

    document.addEventListener('click', function () {
        dropdownNotificaciones.classList.remove('activo');
    });
}

document.querySelectorAll('.notificacion').forEach(function (notificacion) {
    notificacion.addEventListener('click', async function () {
        if (!this.classList.contains('no-leida')) {
            return;
        }

        try {
            const respuesta = await fetch(this.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.value,
                    'Accept': 'application/json'
                }
            });

            if (!respuesta.ok) {
                throw new Error('No se pudo marcar la notificación como leída.');
            }

            const datos = await respuesta.json();

            this.classList.remove('no-leida');
            this.classList.add('leida');

            const indicador = this.querySelector('.indicador-no-leida');

            if (indicador) {
                indicador.remove();
            }

            const contador = document.getElementById('contadorNotificaciones');

            if (contador) {
                if (datos.no_leidas > 0) {
                    contador.textContent = datos.no_leidas;
                } else {
                    contador.remove();
                }
            }
        } catch (error) {
            console.error(error);
        }
    });
});