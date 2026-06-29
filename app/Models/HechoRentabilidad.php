<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HechoRentabilidad extends Model
{
    protected $table = 'hecho_rentabilidad';

    protected $fillable = [
        'dimension_temporal_id',
        'tipo_prestamo_id',
        'intereses_ganados',
        'mora_ganada',
        'comisiones_ganadas',
        'ingresos_totales',
        'costos_operativos',
        'rentabilidad_neta',
        'roi',
    ];

    protected $casts = [
        'intereses_ganados' => 'decimal:2',
        'mora_ganada' => 'decimal:2',
        'comisiones_ganadas' => 'decimal:2',
        'ingresos_totales' => 'decimal:2',
        'costos_operativos' => 'decimal:2',
        'rentabilidad_neta' => 'decimal:2',
        'roi' => 'decimal:2',
    ];

    public function dimensionTemporal()
    {
        return $this->belongsTo(DimensionTemporal::class, 'dimension_temporal_id');
    }

    public function tipoPrestamo()
    {
        return $this->belongsTo(TipoPrestamo::class, 'tipo_prestamo_id');
    }
}
