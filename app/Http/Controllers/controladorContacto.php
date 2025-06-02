<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contacto;
use App\Models\Inmueble;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class controladorContacto extends Controller
{
    /**
     * Almacenar un nuevo contacto en la base de datos
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inmueble_id' => 'required|exists:inmueble,id',
            'propietario_id' => 'required|exists:users,id',
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'mensaje' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Verificar que el propietario sea el dueño del inmueble
        $inmueble = Inmueble::find($request->inmueble_id);
        if ($inmueble->user_id != $request->propietario_id) {
            return response()->json([
                'message' => 'El propietario indicado no es dueño del inmueble',
                'status' => 400
            ], 400);
        }

        $contacto = Contacto::create([
            'inmueble_id' => $request->inmueble_id,
            'propietario_id' => $request->propietario_id,
            'nombre' => $request->nombre,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'mensaje' => $request->mensaje,
            'leido' => false
        ]);

        return response()->json([
            'message' => 'Contacto enviado correctamente',
            'contacto' => $contacto,
            'status' => 201
        ], 201);
    }

    /**
     * Listar los contactos recibidos por un propietario
     */
    public function index(Request $request)
    {
        // Solo los usuarios con rol de negocio pueden ver sus contactos
        if ($request->user()->rol !== 'negocio') {
            return response()->json([
                'message' => 'No autorizado',
                'status' => 403
            ], 403);
        }

        $contactos = Contacto::with(['inmueble'])
            ->where('propietario_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($contactos, 200);
    }

    /**
     * Marcar un contacto como leído
     */
    public function marcarLeido(Request $request, $id)
    {
        $contacto = Contacto::find($id);

        if (!$contacto) {
            return response()->json([
                'message' => 'Contacto no encontrado',
                'status' => 404
            ], 404);
        }

        // Verificar que el usuario sea el propietario del contacto
        if ($contacto->propietario_id != $request->user()->id) {
            return response()->json([
                'message' => 'No autorizado',
                'status' => 403
            ], 403);
        }

        $contacto->leido = true;
        $contacto->save();

        return response()->json([
            'message' => 'Contacto marcado como leído',
            'contacto' => $contacto,
            'status' => 200
        ], 200);
    }
}
