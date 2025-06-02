<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inmueble;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class controladorInmueble extends Controller
{

    private function filtrarTipo(string $tipo){
        $inmueble = Inmueble::with('propietario:id,name,email')->where("tipo",$tipo)->get();
        return response()->json($inmueble,200);
    }

    public function index(Request $request){
        if(isset($request->tipo)){
            return $this->filtrarTipo($request->tipo);
        }
        $inmueble = Inmueble::with('propietario:id,name,email')->get();

        if($inmueble->isEmpty()){
            return response()->json([], 200);
        }
        return response()->json($inmueble,200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'tipo'=> 'required|in:piso,casa,local,terreno', //tipos de inmuebles
            'operacion' => 'required|in:venta,alquiler', // tipo de operación
            'titulo' => 'required|string|max:255',
            'foto'=> 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Validación para imágenes (5MB)
            'direccion'=> 'required|unique:inmueble', //para que no haya dos inmuebles iguales
            'precio'=> 'required|numeric|min:0',
            'habitacion'=> 'required|integer|min:0',
            'metro'=> 'required|numeric|min:0',
            'descripcion'=> 'required'
        ]);

        if($validator->fails()){
            $data =[
                'message'=> 'Error en la validacion de datos',
                'errors' => $validator->errors(),
                'status'=> 400
            ];
            return response()->json($data,400);
        }

        // Manejo de la imagen
        $rutaImagen = null;
        if ($request->hasFile('foto')) {
            $uploadedFileUrl = Cloudinary::upload($request->file('foto')->getRealPath(), [
                'folder' => 'inmuebles'
            ])->getSecurePath();
            $rutaImagen = $uploadedFileUrl;
        }

        $inmueble = Inmueble::create([
            'user_id' => $request->user()->id, // Asignar el usuario actual como propietario
            'tipo'=> $request->tipo,
            'operacion' => $request->operacion,
            'titulo' => $request->titulo,
            'foto'=> $rutaImagen,
            'direccion'=> $request->direccion,
            'precio'=> $request->precio,
            'habitacion'=> $request->habitacion,
            'metro'=> $request->metro,
            'descripcion'=> $request->descripcion,
            'destacado' => $request->destacado ?? false,
            'activo' => true
        ]);

        if(!$inmueble){
            $data = [
                'message'=> 'Error al crear inmueble',
                'error'=> $validator->errors(),
                'status'=> 500,
            ];
            return response()->json($data,500);
        }

        $data = [
            'inmueble'=> $inmueble,
            'status'=>201,
        ];
        return response()->json($data,201);
    }

    public function show($id){
        $inmueble = Inmueble::with('propietario:id,name,email')->find($id);

        if(!$inmueble){
            $data = [
                'message'=> 'Inmueble no encontrado',
                'status'=> 404
            ];
            return response()->json($data,404);
        }

        $data = [
            'inmueble'=> $inmueble,
            'status'=>200
        ];
        return response()->json($data,200);
    }

    public function destroy($id){
        $inmueble = Inmueble::find($id);

        if(!$inmueble){
            $data = [
                'message'=> 'Inmueble no encontrado',
                'status'=> 404
            ];
            return response()->json($data,404);
        }

        // Si hay imagen, la eliminamos
        if ($inmueble->foto && Storage::exists(str_replace('/storage', 'public', $inmueble->foto))) {
            Storage::delete(str_replace('/storage', 'public', $inmueble->foto));
        }

        $inmueble->delete();

        $data = [
            'message'=> "Inmueble Eliminado",
            'status'=>200
        ];
        return response()->json($data,200);
    }

    public function update(Request $request, $id){
        $inmueble = Inmueble::find($id);

        if(!$inmueble){
            $data = [
                'message'=> 'Inmueble no encontrado',
                'status'=> 404
            ];
            return response()->json($data,404);
        }

        // Verificar que el usuario sea el propietario del inmueble
        if ($request->user()->id !== $inmueble->user_id) {
            return response()->json([
                'message' => 'No tienes permiso para editar este inmueble',
                'status' => 403
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'tipo'=> 'required|in:piso,casa,local,terreno', //tipos de inmuebles
            'operacion' => 'required|in:venta,alquiler',
            'titulo' => 'required|string|max:255',
            'foto'=> 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Validación para imágenes (5MB)
            'direccion'=> 'required|unique:inmueble,direccion,'.$id, // Excluye el inmueble actual
            'precio'=> 'required|numeric|min:0',
            'habitacion'=> 'required|integer|min:0',
            'metro'=> 'required|numeric|min:0',
            'descripcion'=> 'required',
            'destacado' => 'nullable|boolean',
            'activo' => 'nullable|boolean'
        ]);

        if($validator->fails()){
            $data =[
                'message'=> 'Error en la validacion de datos',
                'errors' => $validator->errors(),
                'status'=> 400
            ];
            return response()->json($data,400);
        }

        // Manejo de la imagen
        if ($request->hasFile('foto')) {
            // Eliminar imagen anterior si existe (no aplicable en Cloudinary a menos que guardes el public_id)
            $uploadedFileUrl = Cloudinary::upload($request->file('foto')->getRealPath(), [
                'folder' => 'inmuebles'
            ])->getSecurePath();
            $inmueble->foto = $uploadedFileUrl;
        }

        $inmueble->tipo = $request->tipo;
        $inmueble->operacion = $request->operacion;
        $inmueble->titulo = $request->titulo;
        $inmueble->direccion = $request->direccion;
        $inmueble->precio = $request->precio;
        $inmueble->habitacion = $request->habitacion;
        $inmueble->metro = $request->metro;
        $inmueble->descripcion = $request->descripcion;
        $inmueble->destacado = $request->destacado;
        $inmueble->activo = $request->activo;
        $inmueble->save();

        $data = [
            'message'=> 'Inmueble actualizado correctamente',
            'inmueble'=> $inmueble,
            'status'=> 200
        ];
        return response()->json($data,200);
    }

    public function updatePartial(Request $request, $id){
        $inmueble = Inmueble::find($id);

        if(!$inmueble){
            $data = [
                'message'=> 'Inmueble no encontrado',
                'status'=> 404
            ];
            return response()->json($data,404);
        }

        $validator = Validator::make($request->all(), [
            'tipo'=> 'sometimes|in:piso,casa', //solo acepta pisos o casas
            'foto'=> 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Validación para imágenes (5MB)
            'direccion'=> 'sometimes|unique:inmueble,direccion,'.$id, // Excluye el inmueble actual
            'precio'=> 'sometimes|numeric|min:0',
            'habitacion'=> 'sometimes|integer|min:0',
            'metro'=> 'sometimes|numeric|min:0',
            'descripcion'=> 'sometimes|string'
        ]);

        if($validator->fails()){
            $data = [
                'message'=> 'Error en la validacion de datos',
                'error'=> $validator->errors(),
                'status'=> 400,
            ];
            return response()->json($data,400);
        }

        // Manejo de la imagen
        if ($request->hasFile('foto')) {
            // Eliminar imagen anterior si existe (no aplicable en Cloudinary a menos que guardes el public_id)
            $uploadedFileUrl = Cloudinary::upload($request->file('foto')->getRealPath(), [
                'folder' => 'inmuebles'
            ])->getSecurePath();
            $inmueble->foto = $uploadedFileUrl;
        }

        if($request->has("tipo")){
            $inmueble->tipo = $request->tipo;
        }

        if($request->has("precio")){
            $inmueble->precio = $request->precio;
        }

        if($request->has("direccion")){
            $inmueble->direccion = $request->direccion;
        }

        if($request->has("habitacion")){
            $inmueble->habitacion = $request->habitacion;
        }

        if($request->has("metro")){
            $inmueble->metro = $request->metro;
        }

        if($request->has("descripcion")){
            $inmueble->descripcion = $request->descripcion;
        }

        $inmueble->save();

        $data = [
            'message'=> 'Inmueble actualizado correctamente',
            'inmueble'=> $inmueble,
            'status'=> 200
        ];
        return response()->json($data,200);
    }

    // Método para obtener inmuebles propios del usuario
    public function misPropiedades(Request $request){
        $usuario = $request->user();

        if ($usuario->rol !== 'negocio') {
            return response()->json([
                'message' => 'No autorizado',
                'status' => 403
            ], 403);
        }

        $inmuebles = Inmueble::where('user_id', $usuario->id)->get();

        return response()->json($inmuebles, 200);
    }
}
