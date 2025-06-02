@extends('layouts.app')

@section('content')
<!-- Mensaje de bienvenida -->
<div id="login-message" class="container mt-3" style="display: none;">
    <div class="alert alert-success alert-dismissible fade show">
        <span id="login-message-text"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Inmuebles Disponibles</h1>
        </div>
        <div class="col-md-4">
            <div class="d-flex justify-content-end">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary" onclick="filtrarInmuebles('todos')">Todos</button>
                    <button type="button" class="btn btn-outline-primary" onclick="filtrarInmuebles('piso')">Pisos</button>
                    <button type="button" class="btn btn-outline-primary" onclick="filtrarInmuebles('casa')">Casas</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="inmuebles-container">
        <!-- Los inmuebles se cargarán aquí dinámicamente -->
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando inmuebles...</p>
        </div>
    </div>
</div>

<!-- Toast de notificación -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="loginToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">Notificación</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-message">
            ¡Bienvenido!
        </div>
    </div>
</div>

<script>
    document.addEventListener('load', function() {
    // Mostrar mensaje de bienvenida si existe
    const loginMessage = localStorage.getItem('login_message');
    if (loginMessage) {
        // Mostrar como alerta
        document.getElementById('login-message-text').textContent = loginMessage;
        document.getElementById('login-message').style.display = 'block';

        // También mostrar como toast
        document.getElementById('toast-message').textContent = loginMessage;
        const toastEl = document.getElementById('loginToast');
        const toast = new bootstrap.Toast(toastEl);
        toast.show();

        // Eliminar el mensaje después de mostrarlo
        localStorage.removeItem('login_message');

        // Hacer que la alerta desaparezca automáticamente después de 5 segundos
        setTimeout(() => {
            const alertElement = document.querySelector('#login-message .alert');
            if (alertElement) {
                const bsAlert = new bootstrap.Alert(alertElement);
                bsAlert.close();
            }
        }, 5000);
    }

    // Cargar todos los inmuebles al iniciar
    cargarInmuebles();
});

async function cargarInmuebles(tipo = null) {
    try {
        let url = '/api/inmueble';
        if (tipo && tipo !== 'todos') {
            url += `?tipo=${tipo}`;
        }

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        const container = document.getElementById('inmuebles-container');
        container.innerHTML = '';

        if (Array.isArray(data) && data.length > 0) {
            data.forEach(inmueble => {
                container.appendChild(crearTarjetaInmueble(inmueble));
            });
        } else {
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No se encontraron inmuebles disponibles.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al cargar inmuebles:', error);
        document.getElementById('inmuebles-container').innerHTML = `
            <div class="col-12 text-center py-5">
                <p class="text-danger">Error al cargar los inmuebles. Por favor, intente nuevamente.</p>
            </div>
        `;
    }
}

function crearTarjetaInmueble(inmueble) {
    const col = document.createElement('div');
    col.className = 'col-md-4 mb-4';

    const imagenUrl = inmueble.foto
        ? inmueble.foto
        : 'https://via.placeholder.com/300x200?text=Sin+Imagen';

    // Definir el estilo para inmuebles destacados
    const esDestacado = inmueble.destacado == 1;
    const cardClass = esDestacado ? 'card inmueble-card h-100 border-warning' : 'card inmueble-card h-100';
    const bgHeaderClass = esDestacado ? 'bg-warning bg-opacity-25' : '';
    const badgeDestacado = esDestacado ? '<span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">Destacado</span>' : '';

    // Obtener el nombre del propietario del inmueble
    const nombreNegocio = inmueble.propietario ? inmueble.propietario.name : 'Negocio no disponible';

    col.innerHTML = `
        <div class="${cardClass}">
            ${badgeDestacado}
            <img src="${imagenUrl}" class="card-img-top inmueble-img" alt="${inmueble.tipo} en ${inmueble.direccion}">
            <div class="card-body ${bgHeaderClass}">
                <h5 class="card-title">${inmueble.titulo || (inmueble.tipo.charAt(0).toUpperCase() + inmueble.tipo.slice(1) + ' en ' + inmueble.direccion)}</h5>
                <p class="card-text">
                    <strong>Precio:</strong> €${inmueble.precio.toLocaleString()}<br>
                    <strong>Habitaciones:</strong> ${inmueble.habitacion}<br>
                    <strong>Metros cuadrados:</strong> ${inmueble.metro} m²
                </p>
                <p class="card-text">${inmueble.descripcion.substring(0, 100)}${inmueble.descripcion.length > 100 ? '...' : ''}</p>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="/inmuebles/${inmueble.id}" class="btn btn-sm btn-primary">Ver más detalles</a>
                    <small class="text-muted">Publicado por: ${nombreNegocio}</small>
                </div>
            </div>
        </div>
    `;

    return col;
}

function filtrarInmuebles(tipo) {
    cargarInmuebles(tipo);

    // Actualizar estilo de los botones
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });

    const botones = document.querySelectorAll('.btn-group .btn');
    if (tipo === 'todos') botones[0].classList.add('active');
    else if (tipo === 'piso') botones[1].classList.add('active');
    else if (tipo === 'casa') botones[2].classList.add('active');
}
</script>
@endsection
