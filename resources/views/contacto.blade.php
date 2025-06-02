@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Contacto</h4>
                </div>
                <div class="card-body">
                    <form id="contactForm">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre completo</label>
                            <input type="text" class="form-control" id="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono">
                        </div>
                        <div class="mb-3">
                            <label for="asunto" class="form-label">Asunto</label>
                            <input type="text" class="form-control" id="asunto" required>
                        </div>
                        <div class="mb-3">
                            <label for="mensaje" class="form-label">Mensaje</label>
                            <textarea class="form-control" id="mensaje" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar mensaje</button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Información de contacto</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-map-marker-alt me-2"></i> Dirección</h5>
                            <p>Calle Principal 123<br>Ciudad, CP 12345</p>

                            <h5><i class="fas fa-phone me-2"></i> Teléfono</h5>
                            <p>+34 123 456 789</p>

                            <h5><i class="fas fa-envelope me-2"></i> Email</h5>
                            <p>info@inmobiliaria.com</p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-clock me-2"></i> Horario de atención</h5>
                            <p>
                                Lunes a Viernes: 9:00 - 18:00<br>
                                Sábados: 10:00 - 14:00<br>
                                Domingos: Cerrado
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('contactForm');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            alert('Mensaje enviado correctamente. Nos pondremos en contacto contigo pronto.');
            form.reset();
        });
    });
</script>
@endsection
