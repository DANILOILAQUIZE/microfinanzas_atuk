<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'usuario_id',
        'socio_id',
        'titulo',
        'mensaje',
        'tipo',
        'canal',
        'link',
        'leida',
        'enviada',
        'fecha_lectura',
        'fecha_envio',
        'fecha_notificacion',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'enviada' => 'boolean',
        'fecha_lectura' => 'datetime',
        'fecha_envio' => 'datetime',
        'fecha_notificacion' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function socio()
    {
        return $this->belongsTo(Socio::class, 'socio_id');
    }
}
