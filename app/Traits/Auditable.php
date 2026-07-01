<?php

namespace App\Traits;

use App\Models\AuditoriaLog;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::auditLog($model, 'CREAR', null, $model->toArray());
        });

        static::updated(function ($model) {
            self::auditLog($model, 'ACTUALIZAR', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            self::auditLog($model, 'ELIMINAR', $model->toArray(), null);
        });
    }

    protected static function auditLog($model, $accion, $valoresAnteriores, $valoresNuevos)
    {
        if (!auth()->check()) return; // No auditar si no hay usuario autenticado

        AuditoriaLog::create([
            'usuario_id' => auth()->id(),
            'entidad' => class_basename($model),
            'entidad_id' => $model->id,
            'accion' => $accion,
            'valores_anteriores' => $valoresAnteriores ? json_encode($valoresAnteriores) : null,
            'valores_nuevos' => $valoresNuevos ? json_encode($valoresNuevos) : null,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
