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
            'entidad' => 'Prestamo',
            'entidad_id' => $prestamo->id,
            'accion' => 'CREAR',
            'valores_anteriores' => null,
            'valores_nuevos' => $prestamo->toJson(),
            'ip' => request()->ip(),
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
                'entidad' => 'Prestamo',
                'entidad_id' => $prestamo->id,
                'accion' => 'ACTUALIZAR',
                'valores_anteriores' => json_encode($prestamo->getOriginal()),
                'valores_nuevos' => json_encode($cambios),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    public function deleted(Prestamo $prestamo)
    {
        AuditoriaLog::create([
            'usuario_id' => auth()->id(),
            'entidad' => 'Prestamo',
            'entidad_id' => $prestamo->id,
            'accion' => 'ELIMINAR',
            'valores_anteriores' => $prestamo->toJson(),
            'valores_nuevos' => null,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
