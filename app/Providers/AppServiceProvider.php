<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Prestamo;
use App\Models\Usuario;
use App\Observers\PrestamoObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar observer para auditoría
        Prestamo::observe(PrestamoObserver::class);
        
        // Definir Gates para permisos
        Gate::before(function (Usuario $user, string $ability) {
            // Verificar si el usuario tiene el permiso a través de su rol
            $permiso = $user->rol->permisos()->where('slug', $ability)->first();
            return $permiso ? true : null;
        });
    }
}
