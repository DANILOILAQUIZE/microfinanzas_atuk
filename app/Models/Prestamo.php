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
        'monto',
        'interes',
        'plazo',
        'fecha_desembolso',
        'saldo',
        'estado',
        'estado_aprobacion',
        'fecha_aprobacion',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'interes' => 'decimal:2',
        'saldo' => 'decimal:2',
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
