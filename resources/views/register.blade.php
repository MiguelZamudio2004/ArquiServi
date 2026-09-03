<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>
<body>
    <section>
    <h2>Nueva Cuenta</h2>
    <label>Nombre:</label>
    <input type="text" placeholder="Ingrese su nombre">
    <label>Apellido paterno:</label>
    <input type="text" placeholder="Ingrese su apellido paterno">
    <label>Apellido materno:</label>
    <input type="text" placeholder="Ingrese su apellido materno">
    <label>Correo electrónico:</label>
    <input type="email" placeholder="Ingrese su correo electrónico">
    <label>Número de teléfono:</label>
    <input type="number" placeholder="Ingrese su número de teléfono">
    <label>Rol:</label>
    <select>
        <option disabled selected>Seleccione un rol</option>
        <option value="cliente">Cliente</option>
        <option value="profesional">Profesional</option>
        <option value="Proveedor">Proveedor</option>
    </select>
    <label>Contraseña:</label>
    <input type="password" placeholder="Ingrese su contraseña">
    <button>Siguiente</button>
    </section>

</body>
</html>