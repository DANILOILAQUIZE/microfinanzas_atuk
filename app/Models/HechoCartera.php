<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HechoCartera extends Model
{
    protected $table = 'hecho_cartera';

    protected $fillable = [
        'dimension_temporal_id',
        'socio_id',
        'tipo_prestamo_id',
        'monto_desembolsado',
        'monto_pagado',
        'saldo_pendiente',
        'prestamos_activos',
        'prestamos_nuevos',
        'prestamos_finalizados',
    ];

    protected $casts = [
        'monto_desembolsado' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
    ];

    public function dimensionTemporal()
    {
        return $this->belongsTo(DimensionTemporal::class, 'dimension_temporal_id');
    }

    public function socio()
    {
        return $this->belongsTo(Socio::class, 'socio_id');
    }

    public function tipoPrestamo()
    {
        return $this->belongsTo(TipoPrestamo::class, 'tipo_prestamo_id');
    }
}
