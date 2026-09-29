const btnNotificaciones = document.getElementById('btnNotificaciones');
const dropdownNotificaciones = document.getElementById('notificacionesDropdown');

const csrfToken =
    document.querySelector('meta[name="csrf-token"]')?.content ||
    document.getElementById('csrf-notificaciones')?.value;

if (btnNotificaciones && dropdownNotificaciones) {
    btnNotificaciones.addEventListener('click', function (event) {
        event.stopPropagation();

        dropdownNotificaciones.classList.toggle('activo');

        const perfilDropdown = document.getElementById('perfilDropdown');

        if (perfilDropdown) {
            perfilDropdown.classList.remove('activo');
        }
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
        if (!csrfToken || !this.dataset.url) {
            return;
        }

        try {
            const respuesta = await fetch(this.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (!respuesta.ok) {
                return;
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

            if (datos.redirect_url) {
                window.location.href = datos.redirect_url;
            }

        } catch (error) {
            return;
        }
    });
});