<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $table = 'prestamos';

    protected $fillable = [
        'socio_id',
        'tipo_prestamo_id',
        'usuario_id',
        'usuario_aprobador_id',
        'fecha_solicitud',
        'monto',
        'monto_total',
        'monto_cuota',
        'interes',
        'plazo',
        'fecha_desembolso',
        'saldo',
        'estado',
        'estado_aprobacion',
        'fecha_aprobacion',
        'observaciones',
        'motivo_rechazo',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'monto_total' => 'decimal:2',
        'monto_cuota' => 'decimal:2',
        'interes' => 'decimal:2',
        'saldo' => 'decimal:2',
        'fecha_solicitud' => 'date',
        'fecha_desembolso' => 'date',
        'fecha_aprobacion' => 'datetime',
    ];

    public function socio()
    {
        return $this->belongsTo(Socio::class, 'socio_id');
    }

    public function tipoPrestamo()
    {
        return $this->belongsTo(TipoPrestamo::class, 'tipo_prestamo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function usuarioAprobador()
    {
        return $this->belongsTo(Usuario::class, 'usuario_aprobador_id');
    }

    public function cuotas()
    {
        return $this->hasMany(Cuota::class, 'prestamo_id');
    }

    public function garantias()
    {
        return $this->hasMany(Garantia::class, 'prestamo_id');
    }

    public function alertasRiesgo()
    {
        return $this->hasMany(AlertaRiesgo::class, 'prestamo_id');
    }

    public function hechosMorosidad()
    {
        return $this->hasMany(HechoMorosidad::class, 'prestamo_id');
    }
}
