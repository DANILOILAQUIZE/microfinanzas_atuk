<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertaRiesgo extends Model
{
    protected $table = 'alerta_riesgos';

    protected $fillable = [
        'socio_id',
        'prestamo_id',
        'tipo_alerta',
        'nivel',
        'mensaje',
        'leida',
        'fecha_alerta',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'fecha_alerta' => 'datetime',
    ];

    public function socio()
    {
        return $this->belongsTo(Socio::class, 'socio_id');
    }

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class, 'prestamo_id');
    }
}
