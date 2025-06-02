<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Verifica si está autenticado
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // Verifica si su rol está en la lista permitida
        if (!in_array($user->rol, $roles)) {
            return response()->json(['error' => 'Acceso denegado'], 403);
        }

        return $next($request);
    }
}
