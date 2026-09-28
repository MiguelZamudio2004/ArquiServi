// Definimos los criterios según el rol
const criteriosPorRol = {
    profesional: ["Calidad de trabajo", "Comunicación", "Profesionalismo"],
    cliente: ["Claridad en requerimientos", "Trato", "Cumplimiento de pagos"],
    proveedor: ["Calidad del material", "Tiempos de entrega", "Comunicación"]
};

// Objeto para almacenar las calificaciones
let calificaciones = {};
let rolActual = "profesional"; // Cambia dinámicamente según el usuario logueado

// Función para cargar criterios en el modal
function cargarCriterios(rol) {
    const modalContent = document.querySelector('.modal-content');

  // Elimina criterios previos si ya se cargaron
    document.querySelectorAll('.criterio').forEach(el => el.remove());

  // Genera dinámicamente los criterios
    criteriosPorRol[rol].forEach(criterio => {
    const div = document.createElement('div');
    div.classList.add('criterio');
    div.innerHTML = `
        <label>${criterio}</label>
        <div class="estrellas" data-criterio="${criterio}">
            <span class="estrella" data-value="1">&#9733;</span>
            <span class="estrella" data-value="2">&#9733;</span>
            <span class="estrella" data-value="3">&#9733;</span>
            <span class="estrella" data-value="4">&#9733;</span>
            <span class="estrella" data-value="5">&#9733;</span>
        </div>
    `;
    modalContent.insertBefore(div, document.getElementById('comentario'));
    });

  // Reinicia calificaciones
    calificaciones = {};
    criteriosPorRol[rol].forEach(c => calificaciones[c] = 0);

    activarEstrellas();
}

// Función para activar interacción con estrellas
function activarEstrellas() {
    document.querySelectorAll('.estrellas').forEach(estrellasDiv => {
        const criterio = estrellasDiv.getAttribute('data-criterio');
        const estrellas = estrellasDiv.querySelectorAll('.estrella');

    estrellas.forEach(estrella => {
        estrella.addEventListener('click', () => {
        // Resetear todas las estrellas
        estrellas.forEach(e => e.classList.remove('active'));

        // Activar hasta la estrella seleccionada
        const valor = estrella.getAttribute('data-value');
        for (let i = 0; i < valor; i++) {
            estrellas[i].classList.add('active');
        }

        // Guardar la calificación
        calificaciones[criterio] = valor;
        console.log(calificaciones);
        });
    });
    });
}

// Botón cancelar
document.getElementById('cancelar').addEventListener('click', () => {
    document.getElementById('comentario').value = "";
    document.querySelectorAll('.estrella').forEach(e => e.classList.remove('active'));
    console.log("Calificación cancelada");
});

// Botón publicar
document.getElementById('publicar').addEventListener('click', () => {
    const comentario = document.getElementById('comentario').value;
    const datos = {
        rol: rolActual,
        ...calificaciones,
        comentario: comentario
    };
    console.log("Datos enviados:", datos);

  // Aquí podrías hacer un fetch/axios POST al backend Laravel
  // fetch('/calificacion', { method: 'POST', body: JSON.stringify(datos) })
});

// Inicializar con el rol actual
cargarCriterios(rolActual);
