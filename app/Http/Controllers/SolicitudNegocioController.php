<?php

namespace App\Http\Controllers;

use App\Models\SolicitudNegocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NuevaSolicitudNegocio;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Mail\NegocioAprobado;

class SolicitudNegocioController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'companyName' => 'required|string|max:255',
            'contactPerson' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
            'businessType' => 'nullable|string|max:255',
            'employees' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'services' => 'nullable|array',
        ]);

        $solicitud = SolicitudNegocio::create([
            'company_name' => $data['companyName'],
            'contact_person' => $data['contactPerson'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'business_type' => $data['businessType'] ?? null,
            'employees' => $data['employees'] ?? null,
            'description' => $data['description'] ?? null,
            'services' => isset($data['services']) ? json_encode($data['services']) : null,
        ]);

        // Buscar el email del admin
        $admin = User::where('rol', 'admin')->first();
        if ($admin) {
            Mail::to($admin->email)->send(new NuevaSolicitudNegocio($solicitud));
        }

        return response()->json(['message' => 'Solicitud enviada correctamente'], 201);
    }

    public function aprobar($id)
    {
        $solicitud = SolicitudNegocio::findOrFail($id);
        // Generar contraseña aleatoria
        $password = Str::random(10);
        // Crear usuario negocio
        $user = \App\Models\User::create([
            'name' => $solicitud->contact_person,
            'email' => $solicitud->email,
            'password' => Hash::make($password),
            'rol' => 'negocio',
        ]);
        // Enviar email de bienvenida con contraseña
        Mail::to($user->email)->send(new NegocioAprobado($user, $password));
        // Eliminar la solicitud
        $solicitud->delete();
        return response()->json(['message' => 'Usuario de negocio creado y notificado.'], 200);
    }

    public function rechazar($id)
    {
        $solicitud = SolicitudNegocio::findOrFail($id);
        $solicitud->delete();
        return response()->json(['message' => 'Solicitud rechazada y eliminada.'], 200);
    }

    public function index()
    {
        $solicitudes = \App\Models\SolicitudNegocio::orderBy('created_at', 'desc')->get();
        return response()->json($solicitudes);
    }
}
