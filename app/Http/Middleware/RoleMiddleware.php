<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role = null){
    $user = Auth::user();

    // Verificar autenticación
    if (!$user) {
        return response()->json(['message' => 'Usuario no autenticado.'], 401);
    }

    // Si no se pasa un rol, simplemente continúa
    if (!$role) {
        return $next($request);
    }

    // Validar el rol del usuario
    if ($user->role !== $role) {
        return response()->json(['message' => 'Acceso denegado.'], 403);
    }

    return $next($request);
    }

}


