<?php

namespace App\Observers;

use App\Models\Prestamo;
use App\Models\AuditoriaLog;

class PrestamoObserver
{
    public function created(Prestamo $prestamo)
    {
        AuditoriaLog::create([
            'usuario_id' => auth()->id(),
            'tabla' => 'prestamos',
            'registro_id' => $prestamo->id,
            'accion' => 'CREAR',
            'valores_antiguos' => null,
            'valores_nuevos' => $prestamo->toJson(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function updated(Prestamo $prestamo)
    {
        // Solo registrar cambios importantes
        $cambiosImportantes = ['estado', 'estado_aprobacion', 'saldo', 'usuario_aprobador_id'];
        $cambios = $prestamo->getChanges();
        
        if (count(array_intersect(array_keys($cambios), $cambiosImportantes)) > 0) {
            AuditoriaLog::create([
                'usuario_id' => auth()->id(),
                'tabla' => 'prestamos',
                'registro_id' => $prestamo->id,
                'accion' => 'ACTUALIZAR',
                'valores_antiguos' => json_encode($prestamo->getOriginal()),
                'valores_nuevos' => json_encode($cambios),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    public function deleted(Prestamo $prestamo)
    {
        AuditoriaLog::create([
            'usuario_id' => auth()->id(),
            'tabla' => 'prestamos',
            'registro_id' => $prestamo->id,
            'accion' => 'ELIMINAR',
            'valores_antiguos' => $prestamo->toJson(),
            'valores_nuevos' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
