<?php

if (!function_exists('hasPermission')) {
    /**
     * Verifica si el usuario autenticado tiene un permiso específico
     */
    function hasPermission(string $permission): bool
    {
        $user = auth()->user();
        
        if (!$user || !$user->rol) {
            return false;
        }

        return $user->rol->permisos->contains('slug', $permission);
    }
}

if (!function_exists('hasRole')) {
    /**
     * Verifica si el usuario autenticado tiene un rol específico
     */
    function hasRole(string $role): bool
    {
        $user = auth()->user();
        
        if (!$user || !$user->rol) {
            return false;
        }

        return $user->rol->nombre === $role;
    }
}

if (!function_exists('hasAnyRole')) {
    /**
     * Verifica si el usuario autenticado tiene alguno de los roles especificados
     */
    function hasAnyRole(array $roles): bool
    {
        $user = auth()->user();
        
        if (!$user || !$user->rol) {
            return false;
        }

        return in_array($user->rol->nombre, $roles);
    }
}
