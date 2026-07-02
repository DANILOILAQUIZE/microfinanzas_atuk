<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [UsuarioController::class, 'showLoginForm'])->name('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Roles y Permisos - Solo Administrador
    Route::middleware(['role:Administrador'])->group(function () {
        Route::resource('roles', \App\Http\Controllers\RolController::class)->parameters([
            'roles' => 'rol'
        ]);
        Route::resource('permisos', \App\Http\Controllers\PermisoController::class)->only(['index']);
        Route::resource('usuarios', \App\Http\Controllers\UsuarioController::class)->except(['show'])->parameters([
            'usuarios' => 'usuario'
        ]);
    });
    
    // Socios - Admin, Gerente y Cajero
    // IMPORTANTE: Las rutas específicas (create, edit) deben ir ANTES de las rutas con parámetros
    Route::middleware(['permission:crear_socios'])->group(function () {
        Route::get('/socios/create', [\App\Http\Controllers\SocioController::class, 'create'])->name('socios.create');
        Route::post('/socios', [\App\Http\Controllers\SocioController::class, 'store'])->name('socios.store');
    });
    
    Route::middleware(['permission:ver_socios'])->group(function () {
        Route::get('/socios', [\App\Http\Controllers\SocioController::class, 'index'])->name('socios.index');
    });
    
    Route::middleware(['permission:editar_socios'])->group(function () {
        Route::get('/socios/{socio}/edit', [\App\Http\Controllers\SocioController::class, 'edit'])->name('socios.edit');
        Route::put('/socios/{socio}', [\App\Http\Controllers\SocioController::class, 'update'])->name('socios.update');
    });
    
    Route::middleware(['permission:ver_socios'])->group(function () {
        Route::get('/socios/{socio}', [\App\Http\Controllers\SocioController::class, 'show'])->name('socios.show');
    });
    
    Route::middleware(['permission:eliminar_socios'])->group(function () {
        Route::delete('/socios/{socio}', [\App\Http\Controllers\SocioController::class, 'destroy'])->name('socios.destroy');
    });
    
    // Tipos de Préstamo - Admin y Gerente (parámetros del sistema)
    Route::middleware(['permission:gestionar_parametros'])->group(function () {
        Route::resource('tipos-prestamo', \App\Http\Controllers\TipoPrestamoController::class)->parameters([
            'tipos-prestamo' => 'tiposPrestamo'
        ]);
        Route::resource('parametros', \App\Http\Controllers\ParametroController::class)->except(['show', 'create']);
    });
    
    // Préstamos - Admin, Gerente y Cajero
    Route::middleware(['permission:ver_prestamos'])->group(function () {
        Route::get('/prestamos', [\App\Http\Controllers\PrestamoController::class, 'index'])->name('prestamos.index');
        Route::get('/prestamos/{prestamo}', [\App\Http\Controllers\PrestamoController::class, 'show'])->name('prestamos.show');
    });
    
    Route::middleware(['permission:crear_prestamos'])->group(function () {
        Route::post('/prestamos', [\App\Http\Controllers\PrestamoController::class, 'store'])->name('prestamos.store');
    });
    
    Route::middleware(['permission:editar_prestamos'])->group(function () {
        Route::get('/prestamos/{prestamo}/edit', [\App\Http\Controllers\PrestamoController::class, 'edit'])->name('prestamos.edit');
        Route::put('/prestamos/{prestamo}', [\App\Http\Controllers\PrestamoController::class, 'update'])->name('prestamos.update');
    });
    
    Route::middleware(['permission:eliminar_prestamos'])->group(function () {
        Route::delete('/prestamos/{prestamo}', [\App\Http\Controllers\PrestamoController::class, 'destroy'])->name('prestamos.destroy');
    });
    
    Route::middleware(['permission:aprobar_prestamos'])->group(function () {
        Route::post('/prestamos/{prestamo}/aprobar', [\App\Http\Controllers\PrestamoController::class, 'aprobar'])->name('prestamos.aprobar');
        Route::post('/prestamos/{prestamo}/rechazar', [\App\Http\Controllers\PrestamoController::class, 'rechazar'])->name('prestamos.rechazar');
        Route::post('/prestamos/detectar-mora', [\App\Http\Controllers\PrestamoController::class, 'ejecutarDeteccionMora'])->name('prestamos.detectar-mora');
    });
    
    // Pagos - Admin, Gerente y Cajero
    Route::middleware(['permission:ver_pagos'])->group(function () {
        Route::get('/pagos', [\App\Http\Controllers\PagoController::class, 'index'])->name('pagos.index');
        Route::get('/pagos/{pago}', [\App\Http\Controllers\PagoController::class, 'show'])->name('pagos.show');
    });
    
    Route::middleware(['permission:registrar_pagos'])->group(function () {
        Route::get('/prestamos/{prestamo}/registrar-pago', [\App\Http\Controllers\PagoController::class, 'registrarPago'])->name('pagos.registrar');
        Route::post('/pagos', [\App\Http\Controllers\PagoController::class, 'store'])->name('pagos.store');
        Route::delete('/pagos/{pago}/anular', [\App\Http\Controllers\PagoController::class, 'anular'])->name('pagos.anular');
    });
    
    // Garantías - Solo Admin y Gerente
    Route::middleware(['permission:editar_prestamos'])->group(function () {
        Route::post('/garantias', [\App\Http\Controllers\GarantiaController::class, 'store'])->name('garantias.store');
        Route::get('/garantias/{garantia}/edit', [\App\Http\Controllers\GarantiaController::class, 'edit'])->name('garantias.edit');
        Route::put('/garantias/{garantia}', [\App\Http\Controllers\GarantiaController::class, 'update'])->name('garantias.update');
        Route::put('/garantias/{garantia}/liberar', [\App\Http\Controllers\GarantiaController::class, 'liberar'])->name('garantias.liberar');
        Route::delete('/garantias/{garantia}', [\App\Http\Controllers\GarantiaController::class, 'destroy'])->name('garantias.destroy');
    });
    
    // Cuentas de Ahorro - Admin, Gerente y Cajero
    Route::middleware(['permission:ver_socios'])->group(function () {
        Route::get('/cuentas-ahorro', [\App\Http\Controllers\CuentaAhorroController::class, 'index'])->name('cuentas-ahorro.index');
        Route::get('/cuentas-ahorro/socios-sin-cuenta', [\App\Http\Controllers\CuentaAhorroController::class, 'getSociosSinCuenta'])->name('cuentas-ahorro.socios-sin-cuenta');
        Route::get('/cuentas-ahorro/{cuentaAhorro}', [\App\Http\Controllers\CuentaAhorroController::class, 'show'])->name('cuentas-ahorro.show');
        Route::get('/cuentas-ahorro/{cuentaAhorro}/edit', [\App\Http\Controllers\CuentaAhorroController::class, 'edit'])->name('cuentas-ahorro.edit');
    });
    
    Route::middleware(['permission:crear_socios'])->group(function () {
        Route::post('/cuentas-ahorro', [\App\Http\Controllers\CuentaAhorroController::class, 'store'])->name('cuentas-ahorro.store');
        Route::put('/cuentas-ahorro/{cuentaAhorro}', [\App\Http\Controllers\CuentaAhorroController::class, 'update'])->name('cuentas-ahorro.update');
        Route::delete('/cuentas-ahorro/{cuentaAhorro}', [\App\Http\Controllers\CuentaAhorroController::class, 'destroy'])->name('cuentas-ahorro.destroy');
    });
    
    // Movimientos de Ahorro - Admin, Gerente y Cajero
    Route::middleware(['permission:ver_movimientos_ahorro'])->group(function () {
        Route::get('/movimientos-ahorro', [\App\Http\Controllers\MovimientoAhorroController::class, 'index'])->name('movimientos-ahorro.index');
        Route::get('/movimientos-ahorro/{movimientoAhorro}', [\App\Http\Controllers\MovimientoAhorroController::class, 'show'])->name('movimientos-ahorro.show');
    });
    
    Route::middleware(['permission:gestionar_movimientos'])->group(function () {
        Route::get('/movimientos-ahorro/crear/{cuenta?}', [\App\Http\Controllers\MovimientoAhorroController::class, 'crear'])->name('movimientos-ahorro.crear');
        Route::get('/movimientos-ahorro/cuentas-activas', [\App\Http\Controllers\MovimientoAhorroController::class, 'getCuentasActivas'])->name('movimientos-ahorro.cuentas-activas');
        Route::post('/movimientos-ahorro', [\App\Http\Controllers\MovimientoAhorroController::class, 'store'])->name('movimientos-ahorro.store');
        Route::delete('/movimientos-ahorro/{movimientoAhorro}/anular', [\App\Http\Controllers\MovimientoAhorroController::class, 'anular'])->name('movimientos-ahorro.anular');
    });
    
    // Alertas de Riesgo - Admin y Gerente
    Route::middleware(['permission:ver_alertas'])->group(function () {
        Route::get('/alertas', [\App\Http\Controllers\AlertaRiesgoController::class, 'index'])->name('alertas.index');
        Route::get('/alertas/{alerta}', [\App\Http\Controllers\AlertaRiesgoController::class, 'show'])->name('alertas.show');
        Route::put('/alertas/{alerta}/marcar-leida', [\App\Http\Controllers\AlertaRiesgoController::class, 'marcarLeida'])->name('alertas.marcar-leida');
        Route::put('/alertas/marcar-todas-leidas', [\App\Http\Controllers\AlertaRiesgoController::class, 'marcarTodasLeidas'])->name('alertas.marcar-todas-leidas');
        Route::post('/alertas/generar-manualmente', [\App\Http\Controllers\AlertaRiesgoController::class, 'generarManualmente'])->name('alertas.generar-manualmente');
        Route::delete('/alertas/{alerta}', [\App\Http\Controllers\AlertaRiesgoController::class, 'destroy'])->name('alertas.destroy');
    });
    
    // Notificaciones - Admin y Gerente
    Route::middleware(['permission:ver_notificaciones'])->group(function () {
        Route::get('/notificaciones', [\App\Http\Controllers\NotificacionController::class, 'index'])->name('notificaciones.index');
        Route::get('/notificaciones/{notificacion}', [\App\Http\Controllers\NotificacionController::class, 'show'])->name('notificaciones.show');
        Route::put('/notificaciones/{notificacion}/marcar-leida', [\App\Http\Controllers\NotificacionController::class, 'marcarLeida'])->name('notificaciones.marcar-leida');
        Route::put('/notificaciones/marcar-todas-leidas', [\App\Http\Controllers\NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.marcar-todas-leidas');
        Route::post('/notificaciones/enviar-manualmente', [\App\Http\Controllers\NotificacionController::class, 'enviarManualmente'])->name('notificaciones.enviar-manualmente');
        Route::delete('/notificaciones/{notificacion}', [\App\Http\Controllers\NotificacionController::class, 'destroy'])->name('notificaciones.destroy');
    });
    
    // Reportes BI - Admin y Gerente
    Route::middleware(['permission:ver_reportes'])->group(function () {
        Route::get('/reportes', [\App\Http\Controllers\ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/cartera', [\App\Http\Controllers\ReporteController::class, 'cartera'])->name('reportes.cartera');
        Route::get('/reportes/morosidad', [\App\Http\Controllers\ReporteController::class, 'morosidad'])->name('reportes.morosidad');
        Route::get('/reportes/rentabilidad', [\App\Http\Controllers\ReporteController::class, 'rentabilidad'])->name('reportes.rentabilidad');
        Route::get('/reportes/kpis', [\App\Http\Controllers\ReporteController::class, 'kpis'])->name('reportes.kpis');
        Route::get('/reportes/socios', [\App\Http\Controllers\ReporteController::class, 'socios'])->name('reportes.socios');
    });
});


// Auditoría - Admin y Gerente (parte de BI)
Route::middleware(['auth', 'permission:ver_auditoria'])->group(function () {
    Route::get('/auditoria', [\App\Http\Controllers\AuditoriaController::class, 'index'])->name('auditoria.index');
    Route::get('/auditoria/{auditoria}', [\App\Http\Controllers\AuditoriaController::class, 'show'])->name('auditoria.show');
});

// Pólizas Contables - Admin (configuración)
Route::middleware(['auth', 'permission:ver_polizas'])->group(function () {
    Route::get('/polizas', [\App\Http\Controllers\PolizaController::class, 'index'])->name('polizas.index');
    Route::get('/polizas/{poliza}', [\App\Http\Controllers\PolizaController::class, 'show'])->name('polizas.show');
});

Route::middleware(['auth', 'permission:crear_polizas'])->group(function () {
    Route::get('/polizas/create', [\App\Http\Controllers\PolizaController::class, 'create'])->name('polizas.create');
    Route::post('/polizas', [\App\Http\Controllers\PolizaController::class, 'store'])->name('polizas.store');
});

Route::middleware(['auth', 'permission:editar_polizas'])->group(function () {
    Route::get('/polizas/{poliza}/edit', [\App\Http\Controllers\PolizaController::class, 'edit'])->name('polizas.edit');
    Route::put('/polizas/{poliza}', [\App\Http\Controllers\PolizaController::class, 'update'])->name('polizas.update');
});

Route::middleware(['auth', 'permission:eliminar_polizas'])->group(function () {
    Route::delete('/polizas/{poliza}', [\App\Http\Controllers\PolizaController::class, 'destroy'])->name('polizas.destroy');
});
