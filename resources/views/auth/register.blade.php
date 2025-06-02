@extends('layouts.app')

@section('content')
<div id="login-message" class="container mt-3" style="display: none;">
    <div class="alert alert-success alert-dismissible fade show">
        <span id="login-message-text"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Registro de Usuario</h4>
                </div>
                <div class="card-body">
                    <div id="error-container" class="alert alert-danger d-none"></div>

                    <form id="register-form">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                            <div class="form-text">La contraseña debe tener al menos 6 caracteres.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Registrarse</button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <p>¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="loginToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">Inicio de sesión exitoso</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-message">
            ¡Bienvenido de nuevo!
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener el parámetro redirect de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const redirectUrl = urlParams.get('redirect');

    // Mostrar mensaje de inicio de sesión si existe
    const loginMessage = localStorage.getItem('login_message');
    if (loginMessage) {
        document.getElementById('login-message-text').textContent = loginMessage;
        document.getElementById('login-message').style.display = 'block';

        // Eliminar el mensaje después de mostrarlo
        localStorage.removeItem('login_message');

        // Opcionalmente, hacer que el mensaje desaparezca automáticamente después de 5 segundos
        setTimeout(() => {
            const alertElement = document.querySelector('#login-message .alert');
            if (alertElement) {
                const alert = bootstrap.Alert.getOrCreateInstance(alertElement);
                alert.close();
            }
        }, 5000);
    }

    const registerForm = document.getElementById('register-form');
    const errorContainer = document.getElementById('error-container');

    registerForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await fetch('/api/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name, email, password })
            });

            const data = await response.json();

            if (data.status === 1) {
                // Guardar el token en localStorage
                localStorage.setItem('auth_token', data.access_token);
                localStorage.setItem('user_data', JSON.stringify(data.user));

                // Guardar un mensaje de bienvenida en localStorage
                localStorage.setItem('login_message', `¡Bienvenido/a, ${data.user.name}! Te has registrado correctamente.`);

                // Redireccionar a la página especificada o a la de inmuebles por defecto
                window.location.href = redirectUrl || '/inmuebles';
            } else {
                // Mostrar errores
                if (data.errors) {
                    let errorMessage = '';
                    for (const key in data.errors) {
                        errorMessage += data.errors[key].join('<br>') + '<br>';
                    }
                    errorContainer.innerHTML = errorMessage;
                } else {
                    errorContainer.textContent = data.message || 'Error al registrarse';
                }
                errorContainer.classList.remove('d-none');
            }

        } catch (error) {
            errorContainer.textContent = 'Ha ocurrido un error. Intente nuevamente.';
            errorContainer.classList.remove('d-none');
        }
    });

    // Actualizar el enlace de login para mantener el parámetro redirect
    if (redirectUrl) {
        const loginLink = document.querySelector('a[href="{{ route("login") }}"]');
        if (loginLink) {
            loginLink.href = `/login?redirect=${encodeURIComponent(redirectUrl)}`;
        }
    }

    // Mostrar notificación toast si existe mensaje de inicio de sesión
    const loginMessageToast = localStorage.getItem('login_message');
    if (loginMessageToast) {
        document.getElementById('toast-message').textContent = loginMessageToast;
        const toast = new bootstrap.Toast(document.getElementById('loginToast'));
        toast.show();

        // Eliminar el mensaje después de mostrarlo
        localStorage.removeItem('login_message');
    }
});
</script>
@endsection
