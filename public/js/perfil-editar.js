const inputFoto = document.getElementById('foto_perfil');
const nombreArchivo = document.getElementById('nombreArchivo');
const previewFoto = document.getElementById('previewFoto');

if (inputFoto && nombreArchivo && previewFoto) {
    inputFoto.addEventListener('change', function () {
        if (this.files.length === 0) {
            nombreArchivo.textContent = 'Ningún archivo seleccionado';
            return;
        }

        const archivo = this.files[0];
        nombreArchivo.textContent = archivo.name;

        if (!archivo.type.startsWith('image/')) return;

        const url = URL.createObjectURL(archivo);

        previewFoto.innerHTML = '';

        const imagen = document.createElement('img');
        imagen.src = url;
        imagen.alt = 'Vista previa de foto de perfil';

        previewFoto.appendChild(imagen);

        imagen.onload = function () {
            URL.revokeObjectURL(url);
        };
    });
}

const descripcion = document.getElementById('descripcion');
const contadorDescripcion = document.getElementById('contadorDescripcion');

if (descripcion && contadorDescripcion) {
    contadorDescripcion.textContent = descripcion.value.length;

    descripcion.addEventListener('input', function () {
        contadorDescripcion.textContent = this.value.length;
    });
}