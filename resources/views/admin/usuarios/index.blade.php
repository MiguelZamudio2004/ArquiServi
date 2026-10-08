@extends('admin.layout')

@section('titulo', 'Usuarios')

@section('contenido')
<section class="admin-titulo-pagina">
    <span class="admin-mini-titulo">
        Administración
    </span>

    <h1>
        Gestión de usuarios
    </h1>

    <p>
        Consulta, busca y administra las cuentas registradas
        en ArquiServi.
    </p>
</section>

<section class="admin-estadisticas usuarios-estadisticas">
    <article class="admin-estadistica">
        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-users"></i>
        </div>

        <div>
            <span>
                Total
            </span>

            <strong>
                {{ $totalUsuarios }}
            </strong>
        </div>
    </article>

    <article class="admin-estadistica">
        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-user-check"></i>
        </div>

        <div>
            <span>
                Activos
            </span>

            <strong>
                {{ $totalActivos }}
            </strong>
        </div>
    </article>

    <article class="admin-estadistica">
        <div class="admin-estadistica-icono neutral">
            <i class="fa-solid fa-user-clock"></i>
        </div>

        <div>
            <span>
                Inactivos
            </span>

            <strong>
                {{ $totalInactivos }}
            </strong>
        </div>
    </article>

    <article class="admin-estadistica">
        <div class="admin-estadistica-icono alerta">
            <i class="fa-solid fa-user-slash"></i>
        </div>

        <div>
            <span>
                Suspendidos
            </span>

            <strong>
                {{ $totalSuspendidos }}
            </strong>
        </div>
    </article>
</section>

<section class="admin-seccion">
    <form
        action="{{ route('admin.usuarios.index') }}"
        method="GET"
        class="admin-filtros"
    >
        <div class="admin-buscador">
            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                placeholder="Buscar por nombre, correo o teléfono"
            >
        </div>

        <select name="rol">
            <option value="">
                Todos los roles
            </option>

            <option
                value="usuario"
                {{ $rol === 'usuario' ? 'selected' : '' }}
            >
                Usuario
            </option>

            <option
                value="profesional"
                {{ $rol === 'profesional' ? 'selected' : '' }}
            >
                Profesional
            </option>

            <option
                value="proveedor"
                {{ $rol === 'proveedor' ? 'selected' : '' }}
            >
                Proveedor
            </option>
        </select>

        <select name="estado">
            <option value="">
                Todos los estados
            </option>

            <option
                value="activo"
                {{ $estado === 'activo' ? 'selected' : '' }}
            >
                Activo
            </option>

            <option
                value="inactivo"
                {{ $estado === 'inactivo' ? 'selected' : '' }}
            >
                Inactivo
            </option>

            <option
                value="suspendido"
                {{ $estado === 'suspendido' ? 'selected' : '' }}
            >
                Suspendido
            </option>
        </select>

        <button
            type="submit"
            class="admin-boton admin-boton-principal"
        >
            <i class="fa-solid fa-filter"></i>
            Buscar
        </button>

        @if(
            $buscar !== '' ||
            $rol ||
            $estado
        )
            <a
                href="{{ route('admin.usuarios.index') }}"
                class="admin-boton admin-boton-secundario"
            >
                Limpiar
            </a>
        @endif
    </form>

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
                        Teléfono
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
                @forelse($usuarios as $usuario)
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
                                        {{ $usuario->apellido_materno }}
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
                            {{ $usuario->telefono ?: 'Sin teléfono' }}
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
                            colspan="6"
                            class="admin-tabla-vacia"
                        >
                            No se encontraron usuarios.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($usuarios->hasPages())
        <div class="admin-paginacion">
            @if($usuarios->onFirstPage())
                <span class="admin-pagina-boton deshabilitado">
                    <i class="fa-solid fa-chevron-left"></i>
                </span>
            @else
                <a
                    href="{{ $usuarios->previousPageUrl() }}"
                    class="admin-pagina-boton"
                >
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            <span class="admin-pagina-texto">
                Página {{ $usuarios->currentPage() }}
                de {{ $usuarios->lastPage() }}
            </span>

            @if($usuarios->hasMorePages())
                <a
                    href="{{ $usuarios->nextPageUrl() }}"
                    class="admin-pagina-boton"
                >
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="admin-pagina-boton deshabilitado">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
            @endif
        </div>
    @endif
</section>
@endsection