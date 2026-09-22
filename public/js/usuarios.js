const profesion = document.getElementById('profesion_id');
const especialidad = document.getElementById('especialidad_id');

if (profesion && especialidad) {
    const opciones = Array.from(
        especialidad.querySelectorAll('option[data-profesion]')
    );

    function actualizarEspecialidades(cambio = false) {
        const profesionId = profesion.value;

        if (cambio) especialidad.value = '';

        opciones.forEach(opcion => {
            opcion.hidden = profesionId && opcion.dataset.profesion !== profesionId;
        });
    }

    profesion.addEventListener('change', function () {
        actualizarEspecialidades(true);
    });

    actualizarEspecialidades();
}