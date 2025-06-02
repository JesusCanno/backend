<?php

use App\Http\Controllers\controladorInmueble;
use App\Http\Controllers\controladorUsuario;
use App\Http\Controllers\controladorContacto;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\SolicitudNegocioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas públicas de inmuebles
Route::get('/inmueble', [controladorInmueble::class, 'index']);
Route::get('/inmueble/{id}', [controladorInmueble::class,'show']);

// Rutas de autenticación
Route::post('/login', [controladorUsuario::class, 'login']);
Route::post('/register', [controladorUsuario::class, 'register']);

// Ruta pública para enviar un contacto
Route::post('/contacto', [controladorContacto::class, 'store']);

// Rutas protegidas por autenticación
Route::middleware('auth:sanctum')->group(function () {
    // Rutas de usuario
    Route::get('/usuario', function(Request $request){
        return $request->user();
    });
    Route::post('/logout', [controladorUsuario::class, 'logout']);
    Route::get('/perfil', [controladorUsuario::class, 'perfil']);
    Route::put('/perfil', [controladorUsuario::class, 'actualizarPerfil']);

    // Rutas de inmuebles protegidas
    Route::post('/inmueble', [controladorInmueble::class, 'store'])->middleware('rol:negocio');
    Route::put('/inmueble/{id}', [controladorInmueble::class, 'update'])->middleware('rol:negocio');
    Route::patch('/inmueble/{id}', [controladorInmueble::class, 'updatePartial'])->middleware('rol:negocio');
    Route::delete('/inmueble/{id}', [controladorInmueble::class, 'destroy'])->middleware('rol:negocio');
    Route::get('/mis-inmuebles', [controladorInmueble::class, 'misPropiedades'])->middleware('rol:negocio');

    // Rutas de contactos (para usuarios de tipo negocio)
    Route::get('/mis-contactos', [controladorContacto::class, 'index'])->middleware('rol:negocio');
    Route::put('/contacto/{id}/leido', [controladorContacto::class, 'marcarLeido'])->middleware('rol:negocio');

    // Rutas de favoritos
    Route::get('/favoritos', [FavoritoController::class, 'index']);
    Route::post('/favoritos', [FavoritoController::class, 'store']);
    Route::delete('/favoritos/{inmueble_id}', [FavoritoController::class, 'destroy']);

    // Rutas de administrador
    Route::middleware('rol:admin')->group(function () {
        Route::get('/usuarios', [controladorUsuario::class, 'usuarios']);
        Route::post('/admin/users', [controladorUsuario::class, 'crearUsuarioPorAdmin']);
        Route::get('/users/{id}', [controladorUsuario::class, 'mostrarUsuario']);
        Route::put('/users/{id}', [controladorUsuario::class, 'actualizarUsuario']);
        Route::delete('/users/{id}', [controladorUsuario::class, 'eliminarUsuario']);
    });

    // Rutas de solicitudes de negocio
    Route::get('/solicitudes-negocio', [SolicitudNegocioController::class, 'index']);
    Route::post('/solicitud-negocio', [SolicitudNegocioController::class, 'store']);
    Route::post('/solicitud-negocio/{id}/aprobar', [SolicitudNegocioController::class, 'aprobar']);
    Route::delete('/solicitud-negocio/{id}/rechazar', [SolicitudNegocioController::class, 'rechazar']);
});

