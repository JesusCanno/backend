@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Mis Mensajes de Contacto</h4>
                    <a href="/mis-inmuebles" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Volver a mis inmuebles
                    </a>
                </div>
                <div class="card-body">
                    <div id="cargando" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p>Cargando mensajes...</p>
                    </div>

                    <div id="contactosContainer" class="table-responsive" style="display: none;">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Inmueble</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="contactosTableBody">
                                <!-- Los contactos se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <div id="sinContactos" class="alert alert-info text-center" style="display: none;">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <p>No has recibido mensajes de contacto todavía.</p>
                        <a href="/mis-inmuebles" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i> Volver a mis inmuebles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver mensaje completo -->
<div class="modal fade" id="verMensajeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detalle del Mensaje</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="mensajeContenido">
                <!-- El contenido del mensaje se cargará aquí -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnResponder">Responder por Email</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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

        // Cargar contactos al iniciar
        cargarContactos();

        async function cargarContactos() {
            try {
                document.getElementById('cargando').style.display = 'block';
                document.getElementById('contactosContainer').style.display = 'none';
                document.getElementById('sinContactos').style.display = 'none';

                const response = await fetch('/api/mis-contactos', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cargar contactos');
                }

                const contactos = await response.json();

                document.getElementById('cargando').style.display = 'none';

                if (contactos.length === 0) {
                    document.getElementById('sinContactos').style.display = 'block';
                    return;
                }

                mostrarContactos(contactos);
                document.getElementById('contactosContainer').style.display = 'block';

            } catch (error) {
                console.error('Error:', error);
                document.getElementById('cargando').style.display = 'none';
                alert('Error al cargar los contactos. Por favor, intenta de nuevo.');
            }
        }

        function mostrarContactos(contactos) {
            const tbody = document.getElementById('contactosTableBody');
            tbody.innerHTML = '';

            contactos.forEach(contacto => {
                const fecha = new Date(contacto.created_at).toLocaleString();
                const inmuebleTitulo = contacto.inmueble.titulo ||
                    `${contacto.inmueble.tipo.charAt(0).toUpperCase() + contacto.inmueble.tipo.slice(1)} en ${contacto.inmueble.direccion}`;

                const estadoClass = contacto.leido ? 'bg-success' : 'bg-warning text-dark';
                const estadoTexto = contacto.leido ? 'Leído' : 'No leído';

                const row = document.createElement('tr');
                row.className = contacto.leido ? '' : 'table-warning';

                row.innerHTML = `
                    <td>${fecha}</td>
                    <td>${inmuebleTitulo}</td>
                    <td>${contacto.nombre}</td>
                    <td><a href="mailto:${contacto.email}">${contacto.email}</a></td>
                    <td>${contacto.telefono || '-'}</td>
                    <td><span class="badge ${estadoClass}">${estadoTexto}</span></td>
                    <td>
                        <button class="btn btn-sm btn-info ver-mensaje" data-id="${contacto.id}"
                            data-mensaje="${contacto.mensaje.replace(/"/g, '&quot;')}"
                            data-nombre="${contacto.nombre}"
                            data-email="${contacto.email}"
                            data-telefono="${contacto.telefono || '-'}"
                            data-inmueble="${inmuebleTitulo}"
                            data-fecha="${fecha}"
                            data-leido="${contacto.leido}">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                    </td>
                `;

                tbody.appendChild(row);
            });

            // Configurar eventos para botones de ver mensaje
            document.querySelectorAll('.ver-mensaje').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const mensaje = this.getAttribute('data-mensaje');
                    const nombre = this.getAttribute('data-nombre');
                    const email = this.getAttribute('data-email');
                    const telefono = this.getAttribute('data-telefono');
                    const inmueble = this.getAttribute('data-inmueble');
                    const fecha = this.getAttribute('data-fecha');
                    const leido = this.getAttribute('data-leido') === 'true';

                    verMensaje(id, mensaje, nombre, email, telefono, inmueble, fecha, leido);
                });
            });
        }

        async function verMensaje(id, mensaje, nombre, email, telefono, inmueble, fecha, leido) {
            // Mostrar el modal con los detalles del mensaje
            document.getElementById('mensajeContenido').innerHTML = `
                <div class="mb-3">
                    <h6>Inmueble:</h6>
                    <p>${inmueble}</p>
                </div>
                <div class="mb-3">
                    <h6>Fecha:</h6>
                    <p>${fecha}</p>
                </div>
                <div class="mb-3">
                    <h6>De:</h6>
                    <p><strong>${nombre}</strong></p>
                </div>
                <div class="mb-3">
                    <h6>Email:</h6>
                    <p><a href="mailto:${email}">${email}</a></p>
                </div>
                <div class="mb-3">
                    <h6>Teléfono:</h6>
                    <p>${telefono}</p>
                </div>
                <div class="mb-3">
                    <h6>Mensaje:</h6>
                    <div class="p-3 bg-light rounded">${mensaje}</div>
                </div>
            `;

            // Configurar botón de responder
            document.getElementById('btnResponder').addEventListener('click', function() {
                window.location.href = `mailto:${email}?subject=Re: Consulta sobre ${inmueble}&body=Hola ${nombre},%0D%0A%0D%0AGracias por contactar conmigo sobre la propiedad "${inmueble}".`;
            });

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('verMensajeModal'));
            modal.show();

            // Si el mensaje no está leído, marcarlo como leído
            if (!leido) {
                try {
                    const response = await fetch(`/api/contacto/${id}/leido`, {
                        method: 'PUT',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`
                        }
                    });

                    if (response.ok) {
                        // Actualizar la vista después de marcar como leído
                        cargarContactos();
                    }
                } catch (error) {
                    console.error('Error al marcar como leído:', error);
                }
            }
        }
    });
</script>
@endsection
