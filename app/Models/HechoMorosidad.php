<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HechoMorosidad extends Model
{
    protected $table = 'hecho_morosidad';

    protected $fillable = [
        'fecha',
        'socio_id',
        'cartera_total',
        'cartera_vencida',
        'cuotas_vencidas_total',
        'monto_mora_total',
        'prestamos_vencidos',
        'cuotas_mora_1_30',
        'cuotas_mora_31_60',
        'cuotas_mora_61_90',
        'cuotas_mora_mas_90',
        'monto_mora_1_30',
        'monto_mora_31_60',
        'monto_mora_61_90',
        'monto_mora_mas_90',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cartera_total' => 'decimal:2',
        'cartera_vencida' => 'decimal:2',
        'cuotas_vencidas_total' => 'integer',
        'monto_mora_total' => 'decimal:2',
        'prestamos_vencidos' => 'integer',
        'cuotas_mora_1_30' => 'integer',
        'cuotas_mora_31_60' => 'integer',
        'cuotas_mora_61_90' => 'integer',
        'cuotas_mora_mas_90' => 'integer',
        'monto_mora_1_30' => 'decimal:2',
        'monto_mora_31_60' => 'decimal:2',
        'monto_mora_61_90' => 'decimal:2',
        'monto_mora_mas_90' => 'decimal:2',
    ];

    public function socio()
    {
        return $this->belongsTo(Socio::class, 'socio_id');
    }
}
