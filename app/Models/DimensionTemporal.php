<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DimensionTemporal extends Model
{
    protected $table = 'dimension_temporal';

    protected $fillable = [
        'fecha',
        'dia',
        'mes',
        'trimestre',
        'anio',
        'nombre_mes',
        'nombre_trimestre',
        'es_fin_semana',
    ];

    protected $casts = [
        'fecha' => 'date',
        'es_fin_semana' => 'boolean',
    ];

    public function hechosCartera()
    {
        return $this->hasMany(HechoCartera::class, 'dimension_temporal_id');
    }

    public function hechosMorosidad()
    {
        return $this->hasMany(HechoMorosidad::class, 'dimension_temporal_id');
    }

    public function hechosRentabilidad()
    {
        return $this->hasMany(HechoRentabilidad::class, 'dimension_temporal_id');
    }

    public function kpiHistoricos()
    {
        return $this->hasMany(KpiHistorico::class, 'dimension_temporal_id');
    }
}
