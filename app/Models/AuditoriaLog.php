<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditoriaLog extends Model
{
    protected $table = 'auditoria_logs';

    protected $fillable = [
        'usuario_id',
        'accion',
        'tabla',
        'registro_id',
        'valores_antiguos',
        'valores_nuevos',
        'ip_address',
        'user_agent',
        'fecha_accion',
    ];

    protected $casts = [
        'valores_antiguos' => 'array',
        'valores_nuevos' => 'array',
        'fecha_accion' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
