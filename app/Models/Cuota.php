<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    protected $table = 'cuotas';

    protected $fillable = [
        'prestamo_id',
        'numero',
        'fecha_vencimiento',
        'capital',
        'interes',
        'iva',
        'mora',
        'total',
        'estado',
    ];

    protected $casts = [
        'capital' => 'decimal:2',
        'interes' => 'decimal:2',
        'iva' => 'decimal:2',
        'mora' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_vencimiento' => 'date',
    ];

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class, 'prestamo_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'cuota_id');
    }
}
