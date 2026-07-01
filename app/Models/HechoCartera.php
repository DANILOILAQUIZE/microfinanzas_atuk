<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HechoCartera extends Model
{
    protected $table = 'hecho_cartera';

    protected $fillable = [
        'fecha',
        'dimension_temporal_id',
        'socio_id',
        'tipo_prestamo_id',
        'cartera_total',
        'cartera_vigente',
        'cartera_vencida',
        'numero_prestamos',
        'monto_desembolsado_mes',
        'monto_recuperado_mes',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cartera_total' => 'decimal:2',
        'cartera_vigente' => 'decimal:2',
        'cartera_vencida' => 'decimal:2',
        'monto_desembolsado_mes' => 'decimal:2',
        'monto_recuperado_mes' => 'decimal:2',
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
