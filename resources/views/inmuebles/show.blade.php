@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div id="detalles-inmueble">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando detalles del inmueble...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inmuebleId = {{ $id }};
    cargarDetallesInmueble(inmuebleId);
});

async function cargarDetallesInmueble(id) {
    try {
        const response = await fetch(`/api/inmueble/${id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.status === 200 && data.inmueble) {
            mostrarDetallesInmueble(data.inmueble);
        } else {
            document.getElementById('detalles-inmueble').innerHTML = `
                <div class="text-center py-5">
                    <p class="text-danger">No se encontró el inmueble solicitado.</p>
                    <a href="/inmuebles" class="btn btn-primary mt-3">Volver a la lista</a>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('detalles-inmueble').innerHTML = `
            <div class="text-center py-5">
                <p class="text-danger">Error al cargar los detalles. Por favor, intente nuevamente.</p>
                <a href="/inmuebles" class="btn btn-primary mt-3">Volver a la lista</a>
            </div>
        `;
    }
}

function mostrarDetallesInmueble(inmueble) {
    const imagenUrl = inmueble.foto
        ? inmueble.foto
        : 'https://via.placeholder.com/800x600?text=Sin+Imagen';

    // Comprobar si el usuario tiene rol de negocio
    const userData = localStorage.getItem('user_data') ? JSON.parse(localStorage.getItem('user_data')) : null;
    const token = localStorage.getItem('auth_token');
    const esNegocio = userData && userData.rol === 'negocio';

    // Determinar si el inmueble está destacado
    const esDestacado = inmueble.destacado == 1;
    const headerClass = esDestacado ? 'bg-warning text-dark' : 'bg-primary text-white';
    const badgeDestacado = esDestacado ?
        '<span class="badge bg-warning text-dark ms-2">Destacado</span>' : '';

    // Obtener el nombre del propietario
    const nombreNegocio = inmueble.propietario ? inmueble.propietario.name : 'Negocio no disponible';

    // Crear botones de editar y eliminar solo si el usuario tiene rol de negocio
    const botonesAdministracion = esNegocio ? `
        <div class="mt-3 mb-4">
            <h5 class="mb-3">Opciones de administrador</h5>
            <div class="d-flex gap-2">
                <a href="/inmuebles/editar/${inmueble.id}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Editar inmueble
                </a>
                <button class="btn btn-danger" onclick="eliminarInmueble(${inmueble.id})">
                    <i class="fas fa-trash me-1"></i> Eliminar inmueble
                </button>
            </div>
        </div>
    ` : '';

    document.getElementById('detalles-inmueble').innerHTML = `
        <div class="card-header ${headerClass}">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">${inmueble.titulo || (inmueble.tipo.charAt(0).toUpperCase() + inmueble.tipo.slice(1) + ' en ' + inmueble.direccion)}</h4>
                ${badgeDestacado}
            </div>
        </div>
        <div class="card-body">
            ${botonesAdministracion}

            <div class="row">
                <div class="col-md-8 mb-4">
                    <img src="${imagenUrl}" class="img-fluid rounded" alt="${inmueble.tipo} en ${inmueble.direccion}">
                </div>
                <div class="col-md-4">
                    <div class="price-tag mb-3">
                        <h3 class="text-primary">€${inmueble.precio.toLocaleString()}</h3>
                        <span class="badge ${inmueble.operacion === 'alquiler' ? 'bg-info' : 'bg-success'} mb-2">
                            ${inmueble.operacion === 'alquiler' ? 'Alquiler' : 'Venta'}
                        </span>
                    </div>
                    <div class="propietario-info mb-3 p-2 border rounded bg-light">
                        <h6 class="mb-1">Publicado por:</h6>
                        <p class="mb-0"><strong>${nombreNegocio}</strong></p>
                    </div>
                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Tipo:</span>
                            <strong>${inmueble.tipo.charAt(0).toUpperCase() + inmueble.tipo.slice(1)}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Habitaciones:</span>
                            <strong>${inmueble.habitacion}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Metros cuadrados:</span>
                            <strong>${inmueble.metro} m²</strong>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-4">
                <h5>Descripción</h5>
                <p>${inmueble.descripcion}</p>
            </div>

            <div class="mt-4">
                <h5>Ubicación</h5>
                <p>${inmueble.direccion}</p>
            </div>

            <div class="mt-4 d-grid gap-2 d-md-flex justify-content-md-start">
                <a href="/inmuebles" class="btn btn-outline-secondary">Volver a la lista</a>
                <button class="btn btn-primary" onclick="contactar()">Contactar</button>
            </div>
        </div>
    `;

    // Añadir el modal de contacto
    document.body.insertAdjacentHTML('beforeend', `
        <div class="modal fade" id="contactarModal" tabindex="-1" aria-labelledby="contactarModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="contactarModalLabel">Contactar con ${nombreNegocio}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formContacto">
                            <input type="hidden" id="inmuebleId" value="${inmueble.id}">
                            <input type="hidden" id="propietarioId" value="${inmueble.propietario ? inmueble.propietario.id : ''}">

                            <div class="mb-3">
                                <label for="nombreContacto" class="form-label">Nombre completo</label>
                                <input type="text" class="form-control" id="nombreContacto" required>
                            </div>

                            <div class="mb-3">
                                <label for="emailContacto" class="form-label">Email</label>
                                <input type="email" class="form-control" id="emailContacto" required>
                            </div>

                            <div class="mb-3">
                                <label for="telefonoContacto" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefonoContacto">
                            </div>

                            <div class="mb-3">
                                <label for="mensajeContacto" class="form-label">Mensaje</label>
                                <textarea class="form-control" id="mensajeContacto" rows="4" required>Hola, estoy interesado/a en este ${inmueble.tipo} en ${inmueble.direccion}. Me gustaría recibir más información.</textarea>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="politicaPrivacidad" required>
                                <label class="form-check-label" for="politicaPrivacidad">
                                    Acepto la política de privacidad y el tratamiento de mis datos
                                </label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btnEnviarContacto">Enviar mensaje</button>
                    </div>
                </div>
            </div>
        </div>
    `);

    // Configurar el evento para enviar el formulario
    document.getElementById('btnEnviarContacto').addEventListener('click', async function() {
        const form = document.getElementById('formContacto');

        // Verificar que todos los campos requeridos estén completos
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Mostrar indicador de carga
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';
        this.disabled = true;

        // Preparar datos para enviar
        const formData = {
            inmueble_id: document.getElementById('inmuebleId').value,
            propietario_id: document.getElementById('propietarioId').value,
            nombre: document.getElementById('nombreContacto').value,
            email: document.getElementById('emailContacto').value,
            telefono: document.getElementById('telefonoContacto').value,
            mensaje: document.getElementById('mensajeContacto').value
        };

        try {
            // Enviar datos al backend
            const response = await fetch('/api/contacto', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (response.ok) {
                // Mostrar mensaje de éxito
                document.querySelector('#contactarModal .modal-body').innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                        <h5>¡Mensaje enviado correctamente!</h5>
                        <p>El propietario ${nombreNegocio} se pondrá en contacto contigo lo antes posible.</p>
                    </div>
                `;

                // Cambiar el botón de enviar
                document.getElementById('btnEnviarContacto').textContent = 'Cerrar';
                document.getElementById('btnEnviarContacto').disabled = false;
                document.getElementById('btnEnviarContacto').addEventListener('click', function() {
                    bootstrap.Modal.getInstance(document.getElementById('contactarModal')).hide();
                });
            } else {
                // Mostrar errores si los hay
                let errorMessage = data.message || 'Error al enviar el mensaje';
                if (data.errors) {
                    errorMessage += '<ul>';
                    for (const key in data.errors) {
                        errorMessage += `<li>${data.errors[key].join(', ')}</li>`;
                    }
                    errorMessage += '</ul>';
                }

                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerHTML = errorMessage;

                form.insertAdjacentElement('beforebegin', alertDiv);

                // Restablecer botón
                this.innerHTML = 'Enviar mensaje';
                this.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);

            // Mostrar mensaje de error
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger';
            alertDiv.textContent = 'Error de conexión. Por favor, intente nuevamente.';

            form.insertAdjacentElement('beforebegin', alertDiv);

            // Restablecer botón
            this.innerHTML = 'Enviar mensaje';
            this.disabled = false;
        }
    });
}

function contactar() {
    // Obtener datos del usuario autenticado (si existe)
    const token = localStorage.getItem('auth_token');
    const userData = localStorage.getItem('user_data') ? JSON.parse(localStorage.getItem('user_data')) : null;

    // Verificar si el usuario está autenticado
    if (!token || !userData) {
        // Usuario no autenticado, mostrar modal de login
        mostrarModalLogin();
        return;
    }

    // Usuario autenticado, mostrar el modal de contacto
    const modal = new bootstrap.Modal(document.getElementById('contactarModal'));
    modal.show();

    // Autocompletar datos del usuario
    document.getElementById('nombreContacto').value = userData.name || '';
    document.getElementById('emailContacto').value = userData.email || '';

    // Añadir mensaje informativo
    const infoDiv = document.createElement('div');
    infoDiv.className = 'alert alert-info mt-2 mb-3';
    infoDiv.innerHTML = '<small><i class="fas fa-info-circle me-1"></i> Hemos completado tus datos automáticamente. Puedes modificarlos si lo deseas.</small>';
    document.getElementById('formContacto').insertAdjacentElement('afterbegin', infoDiv);
}

// Función para mostrar modal de login
function mostrarModalLogin() {
    // Crear el modal de login si no existe
    if (!document.getElementById('loginModal')) {
        document.body.insertAdjacentHTML('beforeend', `
            <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Iniciar sesión para contactar</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-4">
                                <i class="fas fa-user-lock text-primary fa-3x mb-3"></i>
                                <h5>Necesitas iniciar sesión</h5>
                                <p>Para contactar con el propietario necesitas iniciar sesión o registrarte.</p>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="/login?redirect=${encodeURIComponent(window.location.href)}" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar sesión
                                </a>
                                <a href="/register?redirect=${encodeURIComponent(window.location.href)}" class="btn btn-outline-primary">
                                    <i class="fas fa-user-plus me-2"></i>Registrarse
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    // Mostrar el modal
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();
}

// Función para eliminar el inmueble
async function eliminarInmueble(id) {
    if (!confirm('¿Estás seguro de que quieres eliminar este inmueble? Esta acción no se puede deshacer.')) {
        return;
    }

    const token = localStorage.getItem('auth_token');

    try {
        const response = await fetch(`/api/inmueble/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });

        const data = await response.json();

        if (data.status === 200) {
            alert('Inmueble eliminado correctamente');
            window.location.href = '/inmuebles';
        } else {
            alert(`Error: ${data.message || 'No se pudo eliminar el inmueble'}`);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al eliminar el inmueble. Por favor, intente nuevamente.');
    }
}
</script>
@endsection
