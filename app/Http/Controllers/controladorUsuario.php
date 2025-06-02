<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash; //encriptar credenciales
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class controladorUsuario extends Controller
{

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol ?? 'cliente'
        ]);

        // Generamos un token después del registro
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => 'Usuario registrado exitosamente',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    //usuarios creados por admin
    public function crearUsuarioPorAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'rol' => 'required|in:admin,cliente,negocio'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol
        ]);

        return response()->json([
            "message" => "Usuario creado correctamente por admin",
            "user" => $user
        ], 201);
    }

    public function usuarios(Request $request)
    {
        if ($request->has("rol")) {
            $rol = $request->input("rol");

            // Verificar si el rol es válido (admin, cliente o negocio)
            if (in_array($rol, ['admin', 'cliente', 'negocio'])) {
                $usuarios = User::where('rol', $rol)->get();
            } else {
                return response()->json(["error" => "Rol no válido"], 400);//si no existe el rol fallo
            }
        } else {
            $usuarios = User::all(); // Si no se envía el filtro, devuelve todos los usuarios
        }

        return response()->json($usuarios);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            // Verificar las credenciales
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Credenciales incorrectas'
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            // Revocar los tokens anteriores si es necesario
            $user->tokens()->delete();

            // Crear un nuevo token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 1,
                'message' => 'Inicio de sesión exitoso',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function logout(Request $request)
    {
        // Revocar todos los tokens del usuario actual
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Sesión cerrada correctamente'
        ]);
    }

    public function perfil(Request $request)
    {
        return response()->json([
            'status' => 1,
            'user' => $request->user()
        ]);
    }

    public function actualizarPerfil(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'password' => 'sometimes|string|min:6',
            'current_password' => 'required_with:password|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            // Verificar la contraseña actual
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'La contraseña actual no es correcta'
                ], 403);
            }
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'status' => 1,
            'message' => 'Perfil actualizado correctamente',
            'user' => $user
        ]);
    }

    public function mostrarUsuario($id)
    {
        $usuario = User::findOrFail($id);

        return response()->json($usuario);
    }

    public function actualizarUsuario(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$id,
            'password' => 'sometimes|string|min:6',
            'rol' => 'sometimes|in:admin,cliente,negocio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('name')) {
            $usuario->name = $request->name;
        }

        if ($request->has('email')) {
            $usuario->email = $request->email;
        }

        if ($request->has('password')) {
            $usuario->password = Hash::make($request->password);
        }

        if ($request->has('rol')) {
            $usuario->rol = $request->rol;
        }

        $usuario->save();

        return response()->json([
            'status' => 1,
            'message' => 'Usuario actualizado correctamente',
            'user' => $usuario
        ]);
    }

    public function eliminarUsuario($id)
    {
        $usuario = User::findOrFail($id);

        // No permitir eliminar al propio usuario autenticado
        if (auth()->id() == $id) {
            return response()->json([
                'status' => 0,
                'message' => 'No puedes eliminar tu propio usuario'
            ], 403);
        }

        $usuario->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Usuario eliminado correctamente'
        ]);
    }
}
