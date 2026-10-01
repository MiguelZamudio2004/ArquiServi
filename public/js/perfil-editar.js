const inputFoto = document.getElementById('foto_perfil');
const nombreArchivo = document.getElementById('nombreArchivo');
const previewFoto = document.getElementById('previewFoto');
const fotoInicial = document.getElementById('fotoInicial');

if (inputFoto && nombreArchivo && previewFoto) {
    inputFoto.addEventListener('change', function () {
        if (this.files.length === 0) {
            nombreArchivo.textContent =
                'Ningún archivo seleccionado';

            return;
        }

        const archivo = this.files[0];

        nombreArchivo.textContent = archivo.name;

        if (!archivo.type.startsWith('image/')) {
            return;
        }

        const url = URL.createObjectURL(archivo);

        previewFoto.src = url;
        previewFoto.style.display = 'block';

        if (fotoInicial) {
            fotoInicial.style.display = 'none';
        }

        previewFoto.onload = function () {
            URL.revokeObjectURL(url);
        };
    });
}

const descripcion = document.getElementById('descripcion');
const contadorDescripcion =
    document.getElementById('contadorDescripcion');

if (descripcion && contadorDescripcion) {
    contadorDescripcion.textContent =
        descripcion.value.length;

    descripcion.addEventListener('input', function () {
        contadorDescripcion.textContent =
            this.value.length;
    });
}

const inputPortafolio =
    document.getElementById('portafolio_fotos');

const previewPortafolio =
    document.getElementById('previewPortafolio');

if (inputPortafolio && previewPortafolio) {
    inputPortafolio.addEventListener('change', function () {
        const archivos = Array.from(this.files);

        if (archivos.length === 0) {
            return;
        }

        if (archivos.length > 3) {
            alert(
                'Solo puedes seleccionar un máximo de 3 fotos.'
            );

            this.value = '';

            return;
        }

        previewPortafolio.innerHTML = '';

        archivos.forEach(archivo => {
            if (!archivo.type.startsWith('image/')) {
                return;
            }

            const url = URL.createObjectURL(archivo);

            const imagen =
                document.createElement('img');

            imagen.src = url;
            imagen.alt =
                'Vista previa de foto del portafolio';

            previewPortafolio.appendChild(imagen);

            imagen.onload = function () {
                URL.revokeObjectURL(url);
            };
        });
    });
}