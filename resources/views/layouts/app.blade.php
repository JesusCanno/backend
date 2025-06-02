<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Inmobiliaria') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <style>
        .navbar-brand {
            font-weight: bold;
        }

        .inmueble-card {
            transition: transform 0.3s;
        }

        .inmueble-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .inmueble-img {
            height: 200px;
            object-fit: cover;
        }

        .footer {
            margin-top: 3rem;
            padding: 2rem 0;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-building me-2"></i>
                Inmobiliaria
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/inmuebles">Inmuebles</a>
                    </li>
                    <li class="nav-item dropdown" id="userDropdown" style="display: none;">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Panel
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/perfil">Mi Perfil</a></li>
                            <li id="adminMenu" style="display: none;"><a class="dropdown-item" href="/admin/usuarios">Gestionar Usuarios</a></li>
                            <li id="negocioMenu" style="display: none;"><a class="dropdown-item" href="/inmuebles/create">Agregar Inmueble</a></li>
                            <li id="negocioMenu2" style="display: none;"><a class="dropdown-item" href="/mis-inmuebles">Mis Inmuebles</a></li>
                            <li id="negocioMenu3" style="display: none;"><a class="dropdown-item" href="/mis-contactos">Mis Contactos</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto" id="authNav">
                    <li class="nav-item" id="loginButton">
                        <a class="nav-link" href="/login">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item" id="registerButton">
                        <a class="nav-link" href="/register">Registrarse</a>
                    </li>
                    <li class="nav-item" id="logoutButton" style="display: none;">
                        <a class="nav-link" href="#" id="logout">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Inmobiliaria</h5>
                    <p>Tu agencia inmobiliaria de confianza.</p>
                </div>
                <div class="col-md-4">
                    <h5>Enlaces</h5>
                    <ul class="list-unstyled">
                        <li><a href="/">Inicio</a></li>
                        <li><a href="/inmuebles">Inmuebles</a></li>
                        <li id="contactoLink" style="display: none;"><a href="/contacto">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contacto</h5>
                    <address>
                        <i class="fas fa-map-marker-alt me-2"></i> Calle Principal 123<br>
                        <i class="fas fa-phone me-2"></i> +34 123 456 789<br>
                        <i class="fas fa-envelope me-2"></i> info@inmobiliaria.com
                    </address>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; {{ date('Y') }} Inmobiliaria. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para gestionar la autenticación -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('auth_token');
            const userData = localStorage.getItem('user_data') ? JSON.parse(localStorage.getItem('user_data')) : null;

            if (token && userData) {
                // Usuario autenticado
                document.getElementById('loginButton').style.display = 'none';
                document.getElementById('registerButton').style.display = 'none';
                document.getElementById('logoutButton').style.display = 'block';
                document.getElementById('userDropdown').style.display = 'block';
                document.getElementById('contactoLink').style.display = 'block';

                // Mostrar menús según el rol
                if (userData.rol === 'admin') {
                    document.getElementById('adminMenu').style.display = 'block';
                }

                if (userData.rol === 'negocio') {
                    document.getElementById('negocioMenu').style.display = 'block';
                    document.getElementById('negocioMenu2').style.display = 'block';
                    document.getElementById('negocioMenu3').style.display = 'block';
                }
            } else {
                // Usuario no autenticado
                document.getElementById('loginButton').style.display = 'block';
                document.getElementById('registerButton').style.display = 'block';
                document.getElementById('logoutButton').style.display = 'none';
                document.getElementById('userDropdown').style.display = 'none';
                document.getElementById('contactoLink').style.display = 'none';

                // Asegurar que los enlaces de login/register tengan la URL actual como redirect
                const currentUrl = window.location.href;
                const loginPath = '/login?redirect=' + encodeURIComponent(currentUrl);
                const registerPath = '/register?redirect=' + encodeURIComponent(currentUrl);

                const loginBtn = document.getElementById('loginButton').querySelector('a');
                const registerBtn = document.getElementById('registerButton').querySelector('a');

                if (loginBtn && !currentUrl.includes('/login')) {
                    loginBtn.href = loginPath;
                }

                if (registerBtn && !currentUrl.includes('/register')) {
                    registerBtn.href = registerPath;
                }
            }

            // Manejar el cierre de sesión
            document.getElementById('logout').addEventListener('click', async function(e) {
                e.preventDefault();

                try {
                    const response = await fetch('/api/logout', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        }
                    });

                    // Limpiar localStorage
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('user_data');

                    // Redireccionar a la página principal
                    window.location.href = '/';
                } catch (error) {
                    console.error('Error al cerrar sesión:', error);
                }
            });
        });
    </script>
</body>
</html>
