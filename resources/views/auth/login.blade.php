@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Iniciar Sesión</h4>
                </div>
                <div class="card-body">
                    <div id="error-container" class="alert alert-danger d-none"></div>

                    <form id="login-form">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <p>¿No tienes una cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const errorContainer = document.getElementById('error-container');

    // Obtener el parámetro redirect de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const redirectUrl = urlParams.get('redirect');

    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            if (data.status === 1) {
                // Guardar el token en localStorage
                localStorage.setItem('auth_token', data.access_token);
                localStorage.setItem('user_data', JSON.stringify(data.user));

                // Guardar un mensaje de bienvenida en localStorage
                localStorage.setItem('login_message', `¡Bienvenido/a, ${data.user.name}! Has iniciado sesión correctamente.`);

                // Redireccionar a la página especificada o a la de inmuebles por defecto
                window.location.href = redirectUrl || '/inmuebles';
            } else {
                errorContainer.textContent = data.message;
                errorContainer.classList.remove('d-none');
            }

        } catch (error) {
            errorContainer.textContent = 'Ha ocurrido un error. Intente nuevamente.';
            errorContainer.classList.remove('d-none');
        }
    });

    // Actualizar el enlace de registro para mantener el parámetro redirect
    if (redirectUrl) {
        const registerLink = document.querySelector('a[href="{{ route("register") }}"]');
        if (registerLink) {
            registerLink.href = `/register?redirect=${encodeURIComponent(redirectUrl)}`;
        }
    }
});
</script>
@endsection
