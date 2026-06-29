<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HechoMorosidad extends Model
{
    protected $table = 'hecho_morosidad';

    protected $fillable = [
        'dimension_temporal_id',
        'socio_id',
        'prestamo_id',
        'dias_mora',
        'monto_mora',
        'monto_vencido',
        'nivel_riesgo',
        'cuotas_vencidas',
    ];

    protected $casts = [
        'monto_mora' => 'decimal:2',
        'monto_vencido' => 'decimal:2',
    ];

    public function dimensionTemporal()
    {
        return $this->belongsTo(DimensionTemporal::class, 'dimension_temporal_id');
    }

    public function socio()
    {
        return $this->belongsTo(Socio::class, 'socio_id');
    }

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class, 'prestamo_id');
    }
}
