<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder a esta página.');
        }

        $user = auth()->user();
        
        // Verificar si el usuario tiene el permiso requerido a través de su rol
        if (!$this->userHasPermission($user, $permission)) {
            abort(403, 'No tiene el permiso necesario para realizar esta acción.');
        }

        return $next($request);
    }

    /**
     * Verifica si el usuario tiene un permiso específico
     */
    private function userHasPermission($user, string $permission): bool
    {
        if (!$user->rol) {
            return false;
        }

        // Obtener todos los permisos del rol del usuario
        $rolePermissions = $user->rol->permisos->pluck('slug')->toArray();

        return in_array($permission, $rolePermissions);
    }
}
