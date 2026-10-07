@extends('admin.layout')

@section('titulo', 'Proveedores')

@section('contenido')

<section class="admin-titulo-pagina">

    <span class="admin-mini-titulo">
        Administración
    </span>

    <h1>
        Gestión de proveedores
    </h1>

    <p>
        Consulta los proveedores registrados,
        los materiales que ofrecen y el estado
        de sus cuentas dentro de ArquiServi.
    </p>

</section>

<section class="admin-estadisticas">

    <article class="admin-estadistica">

        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-store"></i>
        </div>

        <div>

            <span>
                Total
            </span>

            <strong>
                {{ $totalProveedores }}
            </strong>

        </div>

    </article>

    <article class="admin-estadistica">

        <div class="admin-estadistica-icono">
            <i class="fa-solid fa-store-slash"></i>
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
            <i class="fa-solid fa-clock"></i>
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
            <i class="fa-solid fa-ban"></i>
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
        action="{{ route('admin.proveedores.index') }}"
        method="GET"
        class="admin-filtros proveedores-filtros"
    >

        <div class="admin-buscador">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                placeholder="Buscar proveedor..."
            >

        </div>

        <select name="material">

            <option value="">
                Todos los materiales
            </option>

            @foreach($materiales as $material)

                <option
                    value="{{ $material->id }}"
                    {{ (string) $materialId === (string) $material->id ? 'selected' : '' }}
                >
                    {{ $material->nombre }}
                </option>

            @endforeach

        </select>

        <select name="estado_cuenta">

            <option value="">
                Todas las cuentas
            </option>

            @foreach($estadosCuenta as $valor => $texto)

                <option
                    value="{{ $valor }}"
                    {{ $estadoCuenta === $valor ? 'selected' : '' }}
                >
                    {{ $texto }}
                </option>

            @endforeach

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
            $materialId ||
            $estadoCuenta
        )

            <a
                href="{{ route('admin.proveedores.index') }}"
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
                        Proveedor
                    </th>

                    <th>
                        Materiales
                    </th>

                    <th>
                        Zona de trabajo
                    </th>

                    <th>
                        Disponibles
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

                @forelse($proveedores as $proveedor)

                    <tr>

                        <td>

                            <div class="tabla-usuario">

                                <div class="tabla-avatar">

                                    @if($proveedor->usuario->foto_perfil)

                                        <img
                                            src="{{ asset('storage/' . $proveedor->usuario->foto_perfil) }}"
                                            alt="Foto de perfil"
                                        >

                                    @else

                                        <i class="fa-solid fa-store"></i>

                                    @endif

                                </div>

                                <div class="tabla-usuario-datos">

                                    <strong>
                                        {{ $proveedor->usuario->nombre }}
                                        {{ $proveedor->usuario->apellido_paterno }}
                                    </strong>

                                    <span>
                                        {{ $proveedor->usuario->correo }}
                                    </span>

                                </div>

                            </div>

                        </td>

                        <td>

                            @if($proveedor->materiales->isNotEmpty())

                                <div class="tabla-materiales">

                                    @foreach($proveedor->materiales->take(3) as $material)

                                        <span class="admin-etiqueta">
                                            {{ $material->nombre }}
                                        </span>

                                    @endforeach

                                    @if($proveedor->materiales->count() > 3)

                                        <span class="tabla-materiales-mas">

                                            +{{
                                                $proveedor
                                                    ->materiales
                                                    ->count() - 3
                                            }}

                                        </span>

                                    @endif

                                </div>

                            @else

                                <span class="texto-muted">
                                    Sin materiales
                                </span>

                            @endif

                        </td>

                        <td>

                            {{ $proveedor->zona_trabajo }}

                        </td>

                        <td>

                            {{
                                $proveedor
                                    ->materiales
                                    ->filter(
                                        function ($material) {
                                            return (bool) $material
                                                ->pivot
                                                ->disponible;
                                        }
                                    )
                                    ->count()
                            }}

                            de

                            {{
                                $proveedor
                                    ->materiales
                                    ->count()
                            }}

                        </td>

                        <td>

                            <span class="admin-estado estado-{{ $proveedor->usuario->estado }}">

                                {{ ucfirst($proveedor->usuario->estado) }}

                            </span>

                        </td>

                        <td>

                            {{ $proveedor->created_at->format('d/m/Y') }}

                        </td>

                        <td>

                            <a
                                href="{{ route('admin.proveedores.mostrar', $proveedor) }}"
                                class="admin-accion-tabla"
                            >
                                Ver
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="admin-tabla-vacia"
                        >
                            No se encontraron proveedores.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    @if($proveedores->hasPages())

        <div class="admin-paginacion">

            @if($proveedores->onFirstPage())

                <span class="admin-pagina-boton deshabilitado">

                    <i class="fa-solid fa-chevron-left"></i>

                </span>

            @else

                <a
                    href="{{ $proveedores->previousPageUrl() }}"
                    class="admin-pagina-boton"
                >

                    <i class="fa-solid fa-chevron-left"></i>

                </a>

            @endif

            <span class="admin-pagina-texto">

                Página {{ $proveedores->currentPage() }}
                de {{ $proveedores->lastPage() }}

            </span>

            @if($proveedores->hasMorePages())

                <a
                    href="{{ $proveedores->nextPageUrl() }}"
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