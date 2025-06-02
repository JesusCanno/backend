@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Editar Inmueble</h4>
                </div>
                <div class="card-body">
                    <div id="message-container" class="alert d-none"></div>
                    <div id="form-container">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando datos del inmueble...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inmuebleId = {{ $id }};
    const token = localStorage.getItem('auth_token');
    const userData = localStorage.getItem('user_data') ? JSON.parse(localStorage.getItem('user_data')) : null;

    // Verificar si el usuario está autenticado y tiene rol de negocio
    if (!token || !userData || userData.rol !== 'negocio') {
        document.getElementById('form-container').innerHTML = `
            <div class="text-center py-4">
                <div class="alert alert-danger">
                    No tienes permiso para editar inmuebles. Debes iniciar sesión con una cuenta de tipo "negocio".
                </div>
                <a href="/inmuebles/${inmuebleId}" class="btn btn-primary mt-3">Volver a detalles</a>
            </div>
        `;
        return;
    }

    cargarDatosInmueble(inmuebleId);
});

async function cargarDatosInmueble(id) {
    const token = localStorage.getItem('auth_token');

    try {
        const response = await fetch(`/api/inmueble/${id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });

        const data = await response.json();

        if (data.status === 200 && data.inmueble) {
            mostrarFormularioEdicion(data.inmueble);
        } else {
            document.getElementById('form-container').innerHTML = `
                <div class="text-center py-4">
                    <div class="alert alert-danger">
                        No se encontró el inmueble solicitado o no tienes permiso para editarlo.
                    </div>
                    <a href="/inmuebles" class="btn btn-primary mt-3">Volver a la lista</a>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('form-container').innerHTML = `
            <div class="text-center py-4">
                <div class="alert alert-danger">
                    Error al cargar los datos del inmueble. Por favor, intente nuevamente.
                </div>
                <a href="/inmuebles/${id}" class="btn btn-primary mt-3">Volver a detalles</a>
            </div>
        `;
    }
}

function mostrarFormularioEdicion(inmueble) {
    document.getElementById('form-container').innerHTML = `
        <form id="editar-inmueble-form" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="titulo" class="form-label">Título del inmueble</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" value="${inmueble.titulo || ''}" required>
                </div>
                <div class="col-md-6">
                    <label for="tipo" class="form-label">Tipo de Inmueble</label>
                    <select class="form-select" id="tipo" name="tipo" required>
                        <option value="piso" ${inmueble.tipo === 'piso' ? 'selected' : ''}>Piso</option>
                        <option value="casa" ${inmueble.tipo === 'casa' ? 'selected' : ''}>Casa</option>
                        <option value="local" ${inmueble.tipo === 'local' ? 'selected' : ''}>Local comercial</option>
                        <option value="terreno" ${inmueble.tipo === 'terreno' ? 'selected' : ''}>Terreno</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="operacion" class="form-label">Operación</label>
                    <select class="form-select" id="operacion" name="operacion" required>
                        <option value="venta" ${inmueble.operacion === 'venta' || !inmueble.operacion ? 'selected' : ''}>Venta</option>
                        <option value="alquiler" ${inmueble.operacion === 'alquiler' ? 'selected' : ''}>Alquiler</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="precio" class="form-label">Precio (€)</label>
                    <input type="number" class="form-control" id="precio" name="precio" value="${inmueble.precio}" min="0" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion" value="${inmueble.direccion}" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="habitacion" class="form-label">Habitaciones</label>
                    <input type="number" class="form-control" id="habitacion" name="habitacion" value="${inmueble.habitacion}" min="0" required>
                </div>
                <div class="col-md-6">
                    <label for="metro" class="form-label">Metros cuadrados</label>
                    <input type="number" class="form-control" id="metro" name="metro" value="${inmueble.metro}" min="0" step="0.01" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required>${inmueble.descripcion}</textarea>
            </div>

            <div class="mb-3">
                <label for="foto" class="form-label">Foto del Inmueble</label>
                ${inmueble.foto ? `
                    <div class="mb-2">
                        <img src="${inmueble.foto}" alt="Imagen actual" class="img-thumbnail" style="max-height: 150px">
                        <p class="form-text">Imagen actual. Sube una nueva imagen solo si deseas cambiarla.</p>
                    </div>
                ` : ''}
                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                <div class="form-text">Formatos permitidos: JPG, PNG, GIF (máx. 2MB)</div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="destacado" name="destacado" ${inmueble.destacado == 1 ? 'checked' : ''}>
                <label class="form-check-label" for="destacado">Destacar este inmueble</label>
                <div class="form-text">Los inmuebles destacados aparecen en posiciones preferentes</div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="activo" name="activo" ${inmueble.activo == 1 ? 'checked' : ''}>
                <label class="form-check-label" for="activo">Inmueble activo</label>
                <div class="form-text">Desactiva esta opción para ocultar temporalmente el inmueble sin eliminarlo</div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="/mis-inmuebles" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    `;

    // Agregar el evento submit al formulario
    document.getElementById('editar-inmueble-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const messageContainer = document.getElementById('message-container');
        const token = localStorage.getItem('auth_token');
        const formData = new FormData(this);

        // Manejar los checkboxes
        formData.set('destacado', this.destacado.checked ? '1' : '0');
        formData.set('activo', this.activo.checked ? '1' : '0');

        try {
            const response = await fetch(`/api/inmueble/${inmueble.id}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                messageContainer.classList.remove('d-none', 'alert-danger');
                messageContainer.classList.add('alert-success');
                messageContainer.textContent = 'Inmueble actualizado correctamente';

                // Redireccionar después de 2 segundos
                setTimeout(() => {
                    window.location.href = `/mis-inmuebles`;
                }, 2000);
            } else {
                messageContainer.classList.remove('d-none', 'alert-success');
                messageContainer.classList.add('alert-danger');

                if (data.errors) {
                    let errorMessage = 'Se encontraron los siguientes errores:<ul>';
                    for (const key in data.errors) {
                        errorMessage += `<li>${data.errors[key].join(', ')}</li>`;
                    }
                    errorMessage += '</ul>';
                    messageContainer.innerHTML = errorMessage;
                } else {
                    messageContainer.textContent = data.message || 'Error al actualizar el inmueble';
                }
            }
        } catch (error) {
            console.error('Error:', error);
            messageContainer.classList.remove('d-none', 'alert-success');
            messageContainer.classList.add('alert-danger');
            messageContainer.textContent = 'Error de conexión. Intente nuevamente.';
        }
    });
}
</script>
@endsection
