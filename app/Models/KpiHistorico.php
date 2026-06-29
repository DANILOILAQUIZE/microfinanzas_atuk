<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiHistorico extends Model
{
    protected $table = 'kpi_historicos';

    protected $fillable = [
        'dimension_temporal_id',
        'nombre_kpi',
        'slug',
        'valor',
        'unidad_medida',
        'valor_anterior',
        'variacion',
        'descripcion',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'valor_anterior' => 'decimal:2',
        'variacion' => 'decimal:2',
    ];

    public function dimensionTemporal()
    {
        return $this->belongsTo(DimensionTemporal::class, 'dimension_temporal_id');
    }
}
