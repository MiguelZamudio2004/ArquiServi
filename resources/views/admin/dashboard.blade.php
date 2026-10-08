@extends('admin.layout')

@section('titulo', 'Dashboard')

@section('contenido')
<section class="admin-titulo-pagina">
    <span class="admin-mini-titulo">
        Panel de administración
    </span>

    <h1>
        Resumen de ArquiServi
    </h1>

    <p>
        Consulta el estado general de la plataforma y los registros
        más recientes.
    </p>
</section>

<section class="admin-estadisticas">
    <article class="admin-estadistica">
        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-user"></i>
        </div>

        <div>
            <span>
                Usuarios
            </span>

            <strong>
                {{ $usuarios }}
            </strong>
        </div>
    </article>

    <article class="admin-estadistica">
        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-user-tie"></i>
        </div>

        <div>
            <span>
                Profesionales
            </span>

            <strong>
                {{ $profesionales }}
            </strong>
        </div>
    </article>

    <article class="admin-estadistica">
        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-store"></i>
        </div>

        <div>
            <span>
                Proveedores
            </span>

            <strong>
                {{ $proveedores }}
            </strong>
        </div>
    </article>

    <article class="admin-estadistica">
        <div class="admin-estadistica-icono alerta">
            <i class="fa-solid fa-user-slash"></i>
        </div>

        <div>
            <span>
                No activas
            </span>

            <strong>
                {{ $cuentasNoActivas }}
            </strong>
        </div>
    </article>
</section>

<section class="admin-seccion">
    <div class="admin-seccion-cabecera">
        <div>
            <span class="admin-mini-titulo">
                Registros
            </span>

            <h2>
                Usuarios recientes
            </h2>

            <p>
                Últimas cuentas registradas en la plataforma.
            </p>
        </div>

        <a
            href="{{ route('admin.usuarios.index') }}"
            class="admin-enlace-secundario"
        >
            Ver todos

            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div class="admin-tabla-contenedor">
        <table class="admin-tabla">
            <thead>
                <tr>
                    <th>
                        Usuario
                    </th>

                    <th>
                        Rol
                    </th>

                    <th>
                        Estado
                    </th>

                    <th>
                        Registro
                    </th>

                    <th>
                        Acción
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse($usuariosRecientes as $usuario)
                    <tr>
                        <td>
                            <div class="tabla-usuario">
                                <div class="tabla-avatar">
                                    @if($usuario->foto_perfil)
                                        <img
                                            src="{{ asset('storage/' . $usuario->foto_perfil) }}"
                                            alt="Foto de perfil"
                                        >
                                    @else
                                        <i class="fa-solid fa-user"></i>
                                    @endif
                                </div>

                                <div class="tabla-usuario-datos">
                                    <strong>
                                        {{ $usuario->nombre }}
                                        {{ $usuario->apellido_paterno }}
                                    </strong>

                                    <span>
                                        {{ $usuario->correo }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="admin-etiqueta">
                                {{
                                    ucfirst(
                                        $usuario->rol->nombre
                                        ?? 'Sin rol'
                                    )
                                }}
                            </span>
                        </td>

                        <td>
                            <span class="admin-estado estado-{{ $usuario->estado }}">
                                {{ ucfirst($usuario->estado) }}
                            </span>
                        </td>

                        <td>
                            {{
                                $usuario
                                    ->created_at
                                    ->format('d/m/Y')
                            }}
                        </td>

                        <td>
                            <a
                                href="{{ route('admin.usuarios.mostrar', $usuario) }}"
                                class="admin-accion-tabla"
                            >
                                Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="5"
                            class="admin-tabla-vacia"
                        >
                            No hay usuarios registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="admin-resumen">
    <div class="admin-resumen-item">
        <span>
            Cuentas registradas
        </span>

        <strong>
            {{ $totalUsuarios }}
        </strong>
    </div>

    <div class="admin-resumen-separador"></div>

    <div class="admin-resumen-item">
        <span>
            Usuarios
        </span>

        <strong>
            {{ $usuarios }}
        </strong>
    </div>

    <div class="admin-resumen-separador"></div>

    <div class="admin-resumen-item">
        <span>
            Profesionales
        </span>

        <strong>
            {{ $profesionales }}
        </strong>
    </div>

    <div class="admin-resumen-separador"></div>

    <div class="admin-resumen-item">
        <span>
            Proveedores
        </span>

        <strong>
            {{ $proveedores }}
        </strong>
    </div>
</section>
@endsection