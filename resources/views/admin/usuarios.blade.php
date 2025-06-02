@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Gestión de Usuarios</h4>
                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
                        <i class="fas fa-plus"></i> Nuevo Usuario
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active" id="btnTodos">Todos</button>
                            <button type="button" class="btn btn-outline-primary" id="btnAdmin">Administradores</button>
                            <button type="button" class="btn btn-outline-primary" id="btnNegocio">Negocios</button>
                            <button type="button" class="btn btn-outline-primary" id="btnCliente">Clientes</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Fecha Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaUsuarios">
                                <!-- Los datos de usuarios se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <div id="cargando" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p>Cargando usuarios...</p>
                    </div>

                    <div id="sinResultados" class="alert alert-info" style="display: none;">
                        No se encontraron usuarios con los criterios seleccionados.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear usuario -->
<div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="crearUsuarioModalLabel">Crear Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCrearUsuario">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol</label>
                        <select class="form-select" id="rol" required>
                            <option value="cliente">Cliente</option>
                            <option value="negocio">Negocio</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarUsuario">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar usuario -->
<div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarUsuario">
                    <input type="hidden" id="editarId">
                    <div class="mb-3">
                        <label for="editarNombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="editarNombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="editarEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editarEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="editarPassword" class="form-label">Contraseña (dejar en blanco para mantener)</label>
                        <input type="password" class="form-control" id="editarPassword">
                    </div>
                    <div class="mb-3">
                        <label for="editarRol" class="form-label">Rol</label>
                        <select class="form-select" id="editarRol" required>
                            <option value="cliente">Cliente</option>
                            <option value="negocio">Negocio</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnActualizarUsuario">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="eliminarUsuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar al usuario <span id="nombreUsuarioEliminar"></span>?</p>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
                <input type="hidden" id="idUsuarioEliminar">
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

        // Verificar si el usuario es administrador
        if (!token || !userData || userData.rol !== 'admin') {
            window.location.href = '/';
            return;
        }

        let filtroActual = '';

        // Cargar usuarios al iniciar
        cargarUsuarios();

        // Configurar filtros
        document.getElementById('btnTodos').addEventListener('click', function() {
            setFiltroActivo(this);
            filtroActual = '';
            cargarUsuarios();
        });

        document.getElementById('btnAdmin').addEventListener('click', function() {
            setFiltroActivo(this);
            filtroActual = 'admin';
            cargarUsuarios('admin');
        });

        document.getElementById('btnNegocio').addEventListener('click', function() {
            setFiltroActivo(this);
            filtroActual = 'negocio';
            cargarUsuarios('negocio');
        });

        document.getElementById('btnCliente').addEventListener('click', function() {
            setFiltroActivo(this);
            filtroActual = 'cliente';
            cargarUsuarios('cliente');
        });

        // Crear usuario
        document.getElementById('btnGuardarUsuario').addEventListener('click', crearUsuario);

        // Actualizar usuario
        document.getElementById('btnActualizarUsuario').addEventListener('click', actualizarUsuario);

        // Confirmar eliminación
        document.getElementById('btnConfirmarEliminar').addEventListener('click', eliminarUsuario);

        // Función para cargar usuarios
        async function cargarUsuarios(rol = '') {
            try {
                document.getElementById('cargando').style.display = 'block';
                document.getElementById('sinResultados').style.display = 'none';
                document.getElementById('tablaUsuarios').innerHTML = '';

                let url = '/api/usuarios';
                if (rol) {
                    url += `?rol=${rol}`;
                }

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cargar usuarios');
                }

                const usuarios = await response.json();

                document.getElementById('cargando').style.display = 'none';

                if (usuarios.length === 0) {
                    document.getElementById('sinResultados').style.display = 'block';
                    return;
                }

                const tabla = document.getElementById('tablaUsuarios');

                usuarios.forEach(usuario => {
                    const fechaRegistro = new Date(usuario.created_at).toLocaleDateString();

                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                        <td>${usuario.id}</td>
                        <td>${usuario.name}</td>
                        <td>${usuario.email}</td>
                        <td>
                            <span class="badge bg-${getRolBadgeClass(usuario.rol)}">
                                ${usuario.rol.charAt(0).toUpperCase() + usuario.rol.slice(1)}
                            </span>
                        </td>
                        <td>${fechaRegistro}</td>
                        <td>
                            <button class="btn btn-sm btn-primary btn-editar" data-id="${usuario.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-eliminar" data-id="${usuario.id}" data-nombre="${usuario.name}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;

                    tabla.appendChild(fila);
                });

                // Configurar eventos para botones de editar y eliminar
                document.querySelectorAll('.btn-editar').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        abrirModalEditar(id);
                    });
                });

                document.querySelectorAll('.btn-eliminar').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const nombre = this.getAttribute('data-nombre');
                        abrirModalEliminar(id, nombre);
                    });
                });

            } catch (error) {
                console.error('Error:', error);
                document.getElementById('cargando').style.display = 'none';
                alert('Error al cargar los usuarios. Por favor, intenta de nuevo.');
            }
        }

        // Función para marcar el filtro activo
        function setFiltroActivo(boton) {
            document.querySelectorAll('.btn-group .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            boton.classList.add('active');
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

        // Función para crear un usuario
        async function crearUsuario() {
            try {
                const nombre = document.getElementById('nombre').value;
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const rol = document.getElementById('rol').value;

                const response = await fetch('/api/admin/users', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({
                        name: nombre,
                        email: email,
                        password: password,
                        rol: rol
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al crear usuario');
                }

                // Cerrar modal y recargar usuarios
                bootstrap.Modal.getInstance(document.getElementById('crearUsuarioModal')).hide();
                document.getElementById('formCrearUsuario').reset();
                alert('Usuario creado correctamente');
                cargarUsuarios(filtroActual);

            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Error al crear el usuario');
            }
        }

        // Función para abrir el modal de edición
        async function abrirModalEditar(id) {
            try {
                const response = await fetch(`/api/users/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al obtener datos del usuario');
                }

                const usuario = await response.json();

                document.getElementById('editarId').value = usuario.id;
                document.getElementById('editarNombre').value = usuario.name;
                document.getElementById('editarEmail').value = usuario.email;
                document.getElementById('editarPassword').value = '';
                document.getElementById('editarRol').value = usuario.rol;

                const modal = new bootstrap.Modal(document.getElementById('editarUsuarioModal'));
                modal.show();

            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar los datos del usuario');
            }
        }

        // Función para actualizar un usuario
        async function actualizarUsuario() {
            try {
                const id = document.getElementById('editarId').value;
                const nombre = document.getElementById('editarNombre').value;
                const email = document.getElementById('editarEmail').value;
                const password = document.getElementById('editarPassword').value;
                const rol = document.getElementById('editarRol').value;

                const userData = {
                    name: nombre,
                    email: email,
                    rol: rol
                };

                if (password) {
                    userData.password = password;
                }

                const response = await fetch(`/api/users/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(userData)
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al actualizar usuario');
                }

                // Cerrar modal y recargar usuarios
                bootstrap.Modal.getInstance(document.getElementById('editarUsuarioModal')).hide();
                alert('Usuario actualizado correctamente');
                cargarUsuarios(filtroActual);

            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Error al actualizar el usuario');
            }
        }

        // Función para abrir el modal de eliminación
        function abrirModalEliminar(id, nombre) {
            document.getElementById('idUsuarioEliminar').value = id;
            document.getElementById('nombreUsuarioEliminar').textContent = nombre;

            const modal = new bootstrap.Modal(document.getElementById('eliminarUsuarioModal'));
            modal.show();
        }

        // Función para eliminar un usuario
        async function eliminarUsuario() {
            try {
                const id = document.getElementById('idUsuarioEliminar').value;

                const response = await fetch(`/api/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (!response.ok) {
                    const data = await response.json();
                    throw new Error(data.message || 'Error al eliminar usuario');
                }

                // Cerrar modal y recargar usuarios
                bootstrap.Modal.getInstance(document.getElementById('eliminarUsuarioModal')).hide();
                alert('Usuario eliminado correctamente');
                cargarUsuarios(filtroActual);

            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Error al eliminar el usuario');
            }
        }
    });
</script>
@endsection
