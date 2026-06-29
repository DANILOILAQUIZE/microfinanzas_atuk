<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialCambio extends Model
{
    protected $table = 'historial_cambios';

    protected $fillable = [
        'entidad',
        'entidad_id',
        'usuario_id',
        'campo',
        'valor_anterior',
        'valor_nuevo',
        'fecha_cambio',
    ];

    protected $casts = [
        'fecha_cambio' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
