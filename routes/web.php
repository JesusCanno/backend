<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí registras las rutas web para Laravel.
| Pero como ahora el frontend está hecho en React,
| debes dejar que React maneje la mayoría de rutas.
|
*/

 //🔁 Ruta raíz: redirigir a React → Eliminamos esta redirección
//Route::get('/', function () {
//     return redirect()->route('inmuebles');
// });

 //❌ COMENTAMOS TODAS LAS RUTAS QUE MOSTRABAN VISTAS DE BLADE

 Route::get('/login', function () {
     return view('auth.login');
 })->name('login');

 Route::get('/register', function () {
     return view('auth.register');
 })->name('register');

 Route::get('/contacto', function () {
     return view('contacto');
 })->middleware('auth')->name('contacto');

 Route::get('/inmuebles', function () {
     return view('inmuebles.index');
 })->name('inmuebles');

 Route::get('/inmuebles/create', function () {
     return view('inmuebles.crear');
 });

 Route::get('/inmuebles/{id}', function ($id) {
     return view('inmuebles.show', ['id' => $id]);
 });

 Route::get('/perfil', function () {
     return view('auth.perfil');
 });

 Route::get('/admin/usuarios', function () {
     return view('admin.usuarios');
 });

 Route::get('/mis-inmuebles', function () {
     return view('inmuebles.mis-inmuebles');
 });

 Route::get('/inmuebles/editar/{id}', function ($id) {
     return view('inmuebles.editar', ['id' => $id]);
 });

 Route::get('/mis-contactos', function () {
     return view('contactos.index');
 });

/*
|--------------------------------------------------------------------------
| Catch-all: Delega a React (index.html) para cualquier ruta web
|--------------------------------------------------------------------------
|
| Esto asegura que todas las rutas (excepto API) sean manejadas por React.
|
*/

Route::get('/{any}', function () {
    return File::get(public_path('index.html'));
})->where('any', '^(?!api|sanctum|_ignition).*$');
