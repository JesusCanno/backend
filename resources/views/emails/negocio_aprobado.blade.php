<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cuenta de negocio aprobada</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { background: #fff; max-width: 600px; margin: 40px auto; border-radius: 10px; box-shadow: 0 2px 8px #e0e0e0; overflow: hidden; }
        .header { background: #2563eb; color: #fff; padding: 32px 24px 16px 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 2rem; }
        .content { padding: 24px; }
        .label { color: #2563eb; font-weight: bold; margin-top: 16px; }
        .value { margin-bottom: 12px; font-size: 1.1rem; }
        .footer { background: #f1f5f9; color: #64748b; text-align: center; padding: 16px; font-size: 0.95rem; }
        .badge { display: inline-block; background: #22c55e; color: #065f46; border-radius: 6px; padding: 2px 10px; font-size: 0.95rem; font-weight: bold; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="badge">¡Enhorabuena!</span>
            <h1>Tu cuenta de negocio ha sido aprobada</h1>
        </div>
        <div class="content">
            <p>Hola <b>{{ $user->name }}</b>,</p>
            <p>Nos complace informarte de que tu solicitud para una cuenta de negocio en <b>Vivius</b> ha sido <span style="color:#22c55e;font-weight:bold;">aprobada</span>.</p>
            <div class="label">Tus credenciales de acceso:</div>
            <div class="value"><b>Email:</b> {{ $user->email }}</div>
            <div class="value"><b>Contraseña temporal:</b> {{ $password }}</div>
            <p>Por motivos de seguridad, te recomendamos cambiar tu contraseña tras el primer acceso.</p>
            <p>Ya puedes acceder a tu panel de negocio y empezar a publicar propiedades y gestionar tus clientes.</p>
            <a href="{{ url('/') }}" style="display:inline-block;margin-top:18px;padding:10px 24px;background:#2563eb;color:#fff;border-radius:6px;text-decoration:none;font-weight:bold;">Acceder a Vivius</a>
        </div>
        <div class="footer">
            Este email se ha generado automáticamente desde Vivius.<br>
            Si tienes cualquier duda, contacta con nuestro equipo de soporte.<br>
            <b>¡Bienvenido a Vivius!</b>
        </div>
    </div>
</body>
</html>
