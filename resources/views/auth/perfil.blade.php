@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Mi Perfil</h4>
                </div>
                <div class="card-body">
                    <div id="cargando" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p>Cargando perfil...</p>
                    </div>

                    <div id="contenidoPerfil" style="display: none;">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="bg-light rounded-circle mx-auto d-flex justify-content-center align-items-center" style="width: 100px; height: 100px;">
                                        <i class="fas fa-user fa-3x text-primary"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <h5 id="nombreUsuario">Nombre de Usuario</h5>
                                <p class="text-muted">
                                    <span class="badge" id="rolUsuario">Rol</span>
                                </p>
                                <p><i class="fas fa-envelope me-2"></i> <span id="emailUsuario">email@ejemplo.com</span></p>
                                <p><i class="fas fa-calendar me-2"></i> Miembro desde: <span id="fechaRegistro">01/01/2023</span></p>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#editarPerfilModal">
                                <i class="fas fa-edit me-2"></i> Editar Perfil
                            </button>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#cambiarPasswordModal">
                                <i class="fas fa-key me-2"></i> Cambiar Contraseña
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de inmuebles para usuarios de negocio -->
            <div id="seccionInmuebles" class="card mt-4" style="display: none;">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Mis Inmuebles</h4>
                    <a href="/inmuebles/create" class="btn btn-light">
                        <i class="fas fa-plus"></i> Nuevo Inmueble
                    </a>
                </div>
                <div class="card-body">
                    <div id="cargandoInmuebles" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p>Cargando inmuebles...</p>
                    </div>

                    <div id="listaInmuebles" class="row" style="display: none;">
                        <!-- Aquí se cargarán los inmuebles dinámicamente -->
                    </div>

                    <div id="sinInmuebles" class="alert alert-info" style="display: none;">
                        No tienes inmuebles publicados. <a href="/inmuebles/create">Publica tu primer inmueble</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar perfil -->
<div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-labelledby="editarPerfilModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editarPerfilModalLabel">Editar Perfil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarPerfil">
                    <div class="mb-3">
                        <label for="editarNombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="editarNombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="editarEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editarEmail" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarPerfil">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar contraseña -->
<div class="modal fade" id="cambiarPasswordModal" tabindex="-1" aria-labelledby="cambiarPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="cambiarPasswordModalLabel">Cambiar Contraseña</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCambiarPassword">
                    <div class="mb-3">
                        <label for="actualPassword" class="form-label">Contraseña Actual</label>
                        <input type="password" class="form-control" id="actualPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuevaPassword" class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="nuevaPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmarPassword" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="confirmarPassword" required>
                    </div>
                </form>
                <div class="alert alert-danger" id="errorPassword" style="display: none;">
                    Las contraseñas no coinciden.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnCambiarPassword">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('auth_token');
        const userData = JSON.parse(localStorage.getItem('user_data'));

        if (!token) {
            window.location.href = '/login';
            return;
        }

        // Cargar datos del perfil
        cargarPerfil();

        // Si el usuario es tipo negocio, cargar sus inmuebles
        if (userData && userData.rol === 'negocio') {
            document.getElementById('seccionInmuebles').style.display = 'block';
            cargarInmuebles();
        }

        // Configurar eventos
        document.getElementById('btnGuardarPerfil').addEventListener('click', actualizarPerfil);
        document.getElementById('btnCambiarPassword').addEventListener('click', cambiarPassword);

        // Función para cargar el perfil
        async function cargarPerfil() {
            try {
                document.getElementById('cargando').style.display = 'block';
                document.getElementById('contenidoPerfil').style.display = 'none';

                const response = await fetch('/api/perfil', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cargar perfil');
                }

                const data = await response.json();
                const usuario = data.user;

                // Actualizar la interfaz con los datos del usuario
                document.getElementById('nombreUsuario').textContent = usuario.name;
                document.getElementById('emailUsuario').textContent = usuario.email;

                const rolElement = document.getElementById('rolUsuario');
                rolElement.textContent = usuario.rol.charAt(0).toUpperCase() + usuario.rol.slice(1);
                rolElement.className = `badge bg-${getRolBadgeClass(usuario.rol)}`;

                const fechaRegistro = new Date(usuario.created_at).toLocaleDateString();
                document.getElementById('fechaRegistro').textContent = fechaRegistro;

                // Rellenar los campos del formulario de edición
                document.getElementById('editarNombre').value = usuario.name;
                document.getElementById('editarEmail').value = usuario.email;

                document.getElementById('cargando').style.display = 'none';
                document.getElementById('contenidoPerfil').style.display = 'block';

            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar el perfil. Por favor, intenta de nuevo.');
                document.getElementById('cargando').style.display = 'none';
            }
        }

        // Función para obtener el color de la insignia según el rol
        function getRolBadgeClass(rol) {
            switch (rol) {
                case 'admin':
                    return 'danger';
                case 'negocio':
                    return 'success';
                case 'cliente':
                    return 'info';
                default:
                    return 'secondary';
            }
        }

        // Función para cargar los inmuebles (solo para usuarios de negocio)
        async function cargarInmuebles() {
            try {
                document.getElementById('cargandoInmuebles').style.display = 'block';
                document.getElementById('listaInmuebles').style.display = 'none';
                document.getElementById('sinInmuebles').style.display = 'none';

                const response = await fetch('/api/mis-inmuebles', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cargar inmuebles');
                }

                const inmuebles = await response.json();

                document.getElementById('cargandoInmuebles').style.display = 'none';

                if (inmuebles.length === 0) {
                    document.getElementById('sinInmuebles').style.display = 'block';
                    return;
                }

                const contenedor = document.getElementById('listaInmuebles');
                contenedor.innerHTML = '';

                inmuebles.forEach(inmueble => {
                    const card = document.createElement('div');
                    card.className = 'col-md-6 col-lg-4 mb-4';

                    const imagen = inmueble.imagen || 'https://via.placeholder.com/300x200?text=Sin+Imagen';
                    const precio = new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(inmueble.precio);

                    card.innerHTML = `
                        <div class="card inmueble-card h-100">
                            <img src="${imagen}" class="card-img-top inmueble-img" alt="${inmueble.titulo}">
                            <div class="card-body">
                                <h5 class="card-title">${inmueble.titulo}</h5>
                                <p class="card-text">${inmueble.descripcion.substring(0, 100)}...</p>
                                <p class="card-text"><strong>${precio}</strong></p>
                                <div class="d-flex justify-content-between">
                                    <a href="/inmuebles/${inmueble.id}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="/inmuebles/editar/${inmueble.id}" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;

                    contenedor.appendChild(card);
                });

                document.getElementById('listaInmuebles').style.display = 'flex';

            } catch (error) {
                console.error('Error:', error);
                document.getElementById('cargandoInmuebles').style.display = 'none';
                alert('Error al cargar los inmuebles. Por favor, intenta de nuevo.');
            }
        }

        // Función para actualizar el perfil
        async function actualizarPerfil() {
            try {
                const nombre = document.getElementById('editarNombre').value;
                const email = document.getElementById('editarEmail').value;

                if (!nombre || !email) {
                    alert('Por favor, completa todos los campos');
                    return;
                }

                const response = await fetch('/api/perfil', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({
                        name: nombre,
                        email: email
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al actualizar perfil');
                }

                // Actualizar datos en localStorage
                const userDataUpdated = {
                    ...userData,
                    name: nombre,
                    email: email
                };
                localStorage.setItem('user_data', JSON.stringify(userDataUpdated));

                // Cerrar el modal y recargar el perfil
                bootstrap.Modal.getInstance(document.getElementById('editarPerfilModal')).hide();
                alert('Perfil actualizado correctamente');
                cargarPerfil();

            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Error al actualizar el perfil');
            }
        }

        // Función para cambiar contraseña
        async function cambiarPassword() {
            try {
                const actualPassword = document.getElementById('actualPassword').value;
                const nuevaPassword = document.getElementById('nuevaPassword').value;
                const confirmarPassword = document.getElementById('confirmarPassword').value;

                if (!actualPassword || !nuevaPassword || !confirmarPassword) {
                    alert('Por favor, completa todos los campos');
                    return;
                }

                if (nuevaPassword !== confirmarPassword) {
                    document.getElementById('errorPassword').style.display = 'block';
                    return;
                }

                document.getElementById('errorPassword').style.display = 'none';

                const response = await fetch('/api/perfil', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({
                        current_password: actualPassword,
                        password: nuevaPassword
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al cambiar contraseña');
                }

                // Cerrar el modal y recargar el perfil
                bootstrap.Modal.getInstance(document.getElementById('cambiarPasswordModal')).hide();
                document.getElementById('formCambiarPassword').reset();
                alert('Contraseña actualizada correctamente');

            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Error al cambiar la contraseña');
            }
        }
    });
</script>
@endsection
