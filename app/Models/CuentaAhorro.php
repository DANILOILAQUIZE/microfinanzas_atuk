<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaAhorro extends Model
{
    protected $table = 'cuentas_ahorro';

    protected $fillable = [
        'socio_id',
        'numero_cuenta',
        'saldo',
        'estado',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
    ];

    public function socio()
    {
        return $this->belongsTo(Socio::class, 'socio_id');
    }

    public function movimientosAhorro()
    {
        return $this->hasMany(MovimientoAhorro::class, 'cuenta_id');
    }
}
