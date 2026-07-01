<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    protected $table = 'cuotas';

    protected $fillable = [
        'prestamo_id',
        'numero_cuota',
        'fecha_vencimiento',
        'fecha_pago',
        'monto',
        'capital',
        'interes',
        'mora',
        'saldo_pendiente',
        'estado',
    ];

    protected $casts = [
        'capital' => 'decimal:2',
        'interes' => 'decimal:2',
        'monto' => 'decimal:2',
        'mora' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
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
