document.addEventListener('DOMContentLoaded', function () {
    const btnPerfil = document.getElementById('btnPerfil');
    const perfilDropdown = document.getElementById('perfilDropdown');

    if (!btnPerfil || !perfilDropdown) return;

    btnPerfil.addEventListener('click', function (event) {
        event.stopPropagation();
        perfilDropdown.classList.toggle('activo');
    });

    perfilDropdown.addEventListener('click', function (event) {
        event.stopPropagation();
    });

    document.addEventListener('click', function () {
        perfilDropdown.classList.remove('activo');
    });
});