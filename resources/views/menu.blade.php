<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
</head>

<body>
    <h2> Bienvenido a ArquiServi {{ Auth::user()->nombre }}</h2>
    <label> ¿Qué servicio necesitas hoy? </label>
    <select>
        <option disabled selected> Selecciona tu servicio requerido </option>
    </select>
    <button>Ver catálogo completo de servicios</button>
    <label>Inicia sesión para ver detalles de tu cuenta</label>
    <button><a href="{{ route('login') }}">Iniciar sesión</a></button>


    <table>
        <tr>
            <th>Nombre</th>
            <th>Especialidad</th>
            <th>Calificación</th>
            <th> </th>
        </tr>
        <tr>
            <td> Esteban Hernandez</td>
            <td>Muebles</td>
            <td>4.5</td>
            <td><button>Ver perfil</button></td>
        </tr>
        <tr>
            <td>Raul Jimenez</td>
            <td>Cocinas integrales</td>
            <td>3.2</td>
            <td><button>Ver perfil</button></td>
        </tr>
        <tr>
            <td>Jorge Reyes</td>
            <td>Closets</td>
            <td>4.8</td>
            <td><button>Ver perfil</button></td>
        </tr>
        <tr>
            <td>Maria Contreras</td>
            <td>Puertas de madera</td>
            <td>4.0</td>
            <td><button>Ver perfil</button></td>
        </tr>
    </table>
</body>
</html>