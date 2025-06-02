@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Mis Inmuebles</h4>
                    <a href="/inmuebles/create" class="btn btn-light">
                        <i class="fas fa-plus"></i> Nuevo Inmueble
                    </a>
                </div>
                <div class="card-body">
                    <div id="cargando" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p>Cargando inmuebles...</p>
                    </div>

                    <div id="filtros" class="mb-4" style="display: none;">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" id="busqueda" class="form-control" placeholder="Buscar por título...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select id="filtroTipo" class="form-select">
                                    <option value="">Todos los tipos</option>
                                    <option value="casa">Casas</option>
                                    <option value="piso">Pisos</option>
                                    <option value="local">Locales</option>
                                    <option value="terreno">Terrenos</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="filtroOperacion" class="form-select">
                                    <option value="">Todas las operaciones</option>
                                    <option value="venta">Venta</option>
                                    <option value="alquiler">Alquiler</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button id="limpiarFiltros" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="contenedorInmuebles" class="row" style="display: none;">
                        <!-- Aquí se cargarán los inmuebles dinámicamente -->
                    </div>

                    <div id="sinInmuebles" class="alert alert-info text-center" style="display: none;">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <p>No tienes inmuebles publicados.</p>
                        <a href="/inmuebles/create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Añadir mi primer inmueble
                        </a>
                    </div>

                    <div id="noResultados" class="alert alert-warning text-center" style="display: none;">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <p>No se encontraron inmuebles con los filtros seleccionados.</p>
                        <button id="resetFiltros" class="btn btn-outline-primary">
                            <i class="fas fa-sync me-2"></i> Reiniciar filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="eliminarInmuebleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el inmueble <strong id="tituloInmuebleEliminar"></strong>?</p>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
                <input type="hidden" id="idInmuebleEliminar">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('auth_token');
        const userData = JSON.parse(localStorage.getItem('user_data'));

        // Verificar si el usuario está autenticado y es de tipo negocio
        if (!token || !userData || userData.rol !== 'negocio') {
            window.location.href = '/';
            return;
        }

        let inmuebles = []; // Para almacenar todos los inmuebles cargados

        // Cargar inmuebles al iniciar
        cargarInmuebles();

        // Configurar eventos de filtrado
        document.getElementById('busqueda').addEventListener('input', aplicarFiltros);
        document.getElementById('filtroTipo').addEventListener('change', aplicarFiltros);
        document.getElementById('filtroOperacion').addEventListener('change', aplicarFiltros);
        document.getElementById('limpiarFiltros').addEventListener('click', limpiarFiltros);
        document.getElementById('resetFiltros').addEventListener('click', limpiarFiltros);

        // Configurar evento para eliminar inmueble
        document.getElementById('btnConfirmarEliminar').addEventListener('click', eliminarInmueble);

        // Función para cargar inmuebles
        async function cargarInmuebles() {
            try {
                document.getElementById('cargando').style.display = 'block';
                document.getElementById('contenedorInmuebles').style.display = 'none';
                document.getElementById('sinInmuebles').style.display = 'none';
                document.getElementById('noResultados').style.display = 'none';
                document.getElementById('filtros').style.display = 'none';

                const response = await fetch('/api/mis-inmuebles', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cargar inmuebles');
                }

                inmuebles = await response.json();

                document.getElementById('cargando').style.display = 'none';

                if (inmuebles.length === 0) {
                    document.getElementById('sinInmuebles').style.display = 'block';
                    return;
                }

                document.getElementById('filtros').style.display = 'block';
                mostrarInmuebles(inmuebles);

            } catch (error) {
                console.error('Error:', error);
                document.getElementById('cargando').style.display = 'none';
                alert('Error al cargar los inmuebles. Por favor, intenta de nuevo.');
            }
        }

        // Función para mostrar inmuebles en la interfaz
        function mostrarInmuebles(inmueblesToShow) {
            const contenedor = document.getElementById('contenedorInmuebles');
            contenedor.innerHTML = '';

            if (inmueblesToShow.length === 0) {
                document.getElementById('noResultados').style.display = 'block';
                document.getElementById('contenedorInmuebles').style.display = 'none';
                return;
            }

            document.getElementById('noResultados').style.display = 'none';

            inmueblesToShow.forEach(inmueble => {
                const card = document.createElement('div');
                card.className = 'col-md-6 col-lg-4 mb-4';

                const imagen = inmueble.foto || 'https://via.placeholder.com/300x200?text=Sin+Imagen';
                const precio = new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(inmueble.precio);
                const operacion = inmueble.operacion === 'venta' ? 'Venta' : 'Alquiler';
                const tipo = inmueble.tipo.charAt(0).toUpperCase() + inmueble.tipo.slice(1);

                // Estilos para destacados
                const esDestacado = inmueble.destacado == 1;
                const cardClass = esDestacado ? 'card inmueble-card h-100 border-warning' : 'card inmueble-card h-100';
                const cardBodyClass = esDestacado ? 'card-body bg-warning bg-opacity-10' : 'card-body';
                const badgeDestacado = esDestacado ? '<span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">Destacado</span>' : '';
                const activoClass = inmueble.activo == 1 ? '' : 'opacity-50';

                card.innerHTML = `
                    <div class="${cardClass} ${activoClass}">
                        ${badgeDestacado}
                        <img src="${imagen}" class="card-img-top inmueble-img" alt="${inmueble.titulo || tipo}">
                        <div class="${cardBodyClass}">
                            <h5 class="card-title">${inmueble.titulo || tipo + ' en ' + inmueble.direccion}</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-primary">${tipo}</span>
                                <span class="badge ${inmueble.operacion === 'alquiler' ? 'bg-info' : 'bg-success'}">${operacion}</span>
                                ${inmueble.activo == 0 ? '<span class="badge bg-danger">Inactivo</span>' : ''}
                            </div>
                            <p class="card-text">${inmueble.descripcion.substring(0, 100)}${inmueble.descripcion.length > 100 ? '...' : ''}</p>
                            <p class="card-text"><strong>${precio}</strong></p>
                            <div class="btn-group w-100" role="group">
                                <a href="/inmuebles/${inmueble.id}" class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="/inmuebles/editar/${inmueble.id}" class="btn btn-outline-success">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-eliminar"
                                    data-id="${inmueble.id}" data-titulo="${inmueble.titulo || tipo + ' en ' + inmueble.direccion}">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                contenedor.appendChild(card);
            });

            // Configurar eventos para botones de eliminar
            document.querySelectorAll('.btn-eliminar').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const titulo = this.getAttribute('data-titulo');
                    abrirModalEliminar(id, titulo);
                });
            });

            document.getElementById('contenedorInmuebles').style.display = 'flex';
        }

        // Función para aplicar filtros
        function aplicarFiltros() {
            const textoBusqueda = document.getElementById('busqueda').value.toLowerCase();
            const tipoSeleccionado = document.getElementById('filtroTipo').value;
            const operacionSeleccionada = document.getElementById('filtroOperacion').value;

            const inmueblesFiltrados = inmuebles.filter(inmueble => {
                const cumpleBusqueda = textoBusqueda === '' ||
                    inmueble.titulo.toLowerCase().includes(textoBusqueda) ||
                    inmueble.descripcion.toLowerCase().includes(textoBusqueda);

                const cumpleTipo = tipoSeleccionado === '' || inmueble.tipo === tipoSeleccionado;
                const cumpleOperacion = operacionSeleccionada === '' || inmueble.operacion === operacionSeleccionada;

                return cumpleBusqueda && cumpleTipo && cumpleOperacion;
            });

            mostrarInmuebles(inmueblesFiltrados);
        }

        // Función para limpiar filtros
        function limpiarFiltros() {
            document.getElementById('busqueda').value = '';
            document.getElementById('filtroTipo').value = '';
            document.getElementById('filtroOperacion').value = '';

            mostrarInmuebles(inmuebles);
        }

        // Función para abrir el modal de eliminación
        function abrirModalEliminar(id, titulo) {
            document.getElementById('idInmuebleEliminar').value = id;
            document.getElementById('tituloInmuebleEliminar').textContent = titulo;

            const modal = new bootstrap.Modal(document.getElementById('eliminarInmuebleModal'));
            modal.show();
        }

        // Función para eliminar un inmueble
        async function eliminarInmueble() {
            try {
                const id = document.getElementById('idInmuebleEliminar').value;

                const response = await fetch(`/api/inmueble/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (!response.ok) {
                    const data = await response.json();
                    throw new Error(data.message || 'Error al eliminar el inmueble');
                }

                // Cerrar modal y recargar inmuebles
                bootstrap.Modal.getInstance(document.getElementById('eliminarInmuebleModal')).hide();
                alert('Inmueble eliminado correctamente');

                // Recargar la lista de inmuebles
                cargarInmuebles();

            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Error al eliminar el inmueble');
            }
        }
    });
</script>
@endsection
