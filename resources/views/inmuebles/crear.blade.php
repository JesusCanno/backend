@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Agregar Nuevo Inmueble</h4>
                </div>
                <div class="card-body">
                    <div id="message-container" class="alert d-none"></div>

                    <form id="crear-inmueble-form" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="titulo" class="form-label">Título del inmueble</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                                <div class="form-text">Ej: "Piso luminoso en el centro"</div>
                            </div>
                            <div class="col-md-6">
                                <label for="tipo" class="form-label">Tipo de Inmueble</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="" selected disabled>Seleccionar tipo...</option>
                                    <option value="piso">Piso</option>
                                    <option value="casa">Casa</option>
                                    <option value="local">Local comercial</option>
                                    <option value="terreno">Terreno</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="operacion" class="form-label">Operación</label>
                                <select class="form-select" id="operacion" name="operacion" required>
                                    <option value="venta" selected>Venta</option>
                                    <option value="alquiler">Alquiler</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="precio" class="form-label">Precio (€)</label>
                                <input type="number" class="form-control" id="precio" name="precio" min="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="habitacion" class="form-label">Habitaciones</label>
                                <input type="number" class="form-control" id="habitacion" name="habitacion" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label for="metro" class="form-label">Metros cuadrados</label>
                                <input type="number" class="form-control" id="metro" name="metro" min="0" step="0.01" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto del Inmueble</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            <div class="form-text">Formatos permitidos: JPG, PNG, GIF (máx. 2MB)</div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="destacado" name="destacado">
                            <label class="form-check-label" for="destacado">Destacar este inmueble</label>
                            <div class="form-text">Los inmuebles destacados aparecen en posiciones preferentes</div>
                        </div>

                        <div class="mt-4">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Guardar Inmueble</button>
                                <a href="/inmuebles" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('crear-inmueble-form');
    const messageContainer = document.getElementById('message-container');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Verificar autenticación
        const token = localStorage.getItem('auth_token');
        if (!token) {
            window.location.href = '/login';
            return;
        }

        // Crear FormData para enviar los datos incluyendo la imagen
        const formData = new FormData(form);

        // Manejar el checkbox destacado
        if (!form.destacado.checked) {
            formData.set('destacado', '0');
        } else {
            formData.set('destacado', '1');
        }

        try {
            const response = await fetch('/api/inmueble', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                // Éxito
                messageContainer.classList.remove('d-none', 'alert-danger');
                messageContainer.classList.add('alert-success');
                messageContainer.textContent = 'Inmueble creado correctamente';

                // Redireccionar después de 2 segundos
                setTimeout(() => {
                    window.location.href = '/mis-inmuebles';
                }, 2000);
            } else {
                // Error
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
                    messageContainer.textContent = data.message || 'Error al crear el inmueble';
                }
            }

        } catch (error) {
            console.error('Error:', error);
            messageContainer.classList.remove('d-none', 'alert-success');
            messageContainer.classList.add('alert-danger');
            messageContainer.textContent = 'Error de conexión. Intente nuevamente.';
        }
    });
});
</script>
@endsection
