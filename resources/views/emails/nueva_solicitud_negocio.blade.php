<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva solicitud de cuenta empresarial</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { background: #fff; max-width: 600px; margin: 40px auto; border-radius: 10px; box-shadow: 0 2px 8px #e0e0e0; overflow: hidden; }
        .header { background: #2563eb; color: #fff; padding: 32px 24px 16px 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 2rem; }
        .content { padding: 24px; }
        .label { color: #2563eb; font-weight: bold; margin-top: 16px; }
        .value { margin-bottom: 12px; font-size: 1.1rem; }
        .footer { background: #f1f5f9; color: #64748b; text-align: center; padding: 16px; font-size: 0.95rem; }
        .badge { display: inline-block; background: #facc15; color: #92400e; border-radius: 6px; padding: 2px 10px; font-size: 0.95rem; font-weight: bold; margin-bottom: 16px; }
        ul { margin: 0 0 12px 20px; padding: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="badge">Nueva solicitud de negocio</span>
            <h1>Solicitud de cuenta empresarial</h1>
        </div>
        <div class="content">
            <div class="label">Nombre de la empresa:</div>
            <div class="value">{{ $solicitud->company_name }}</div>

            <div class="label">Persona de contacto:</div>
            <div class="value">{{ $solicitud->contact_person }}</div>

            <div class="label">Email:</div>
            <div class="value">{{ $solicitud->email }}</div>

            @if($solicitud->phone)
                <div class="label">Teléfono:</div>
                <div class="value">{{ $solicitud->phone }}</div>
            @endif

            @if($solicitud->business_type)
                <div class="label">Tipo de negocio:</div>
                <div class="value">{{ $solicitud->business_type }}</div>
            @endif

            @if($solicitud->employees)
                <div class="label">Nº de empleados:</div>
                <div class="value">{{ $solicitud->employees }}</div>
            @endif

            @if($solicitud->description)
                <div class="label">Descripción:</div>
                <div class="value">{{ $solicitud->description }}</div>
            @endif

            @if($solicitud->services)
                <div class="label">Servicios de interés:</div>
                <ul>
                    @foreach(json_decode($solicitud->services, true) as $servicio)
                        <li>{{ $servicio }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="footer">
            Este email se ha generado automáticamente desde Vivius. Por favor, revisa la solicitud y responde al solicitante si procede.<br>
            <b>Fecha de solicitud:</b> {{ $solicitud->created_at->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
