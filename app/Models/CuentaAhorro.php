<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaAhorro extends Model
{
    protected $table = 'cuentas_ahorro';

    protected $fillable = [
        'socio_id',
        'numero_cuenta',
        'fecha_apertura',
        'deposito_inicial',
        'saldo',
        'saldo_disponible',
        'saldo_bloqueado',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'deposito_inicial' => 'decimal:2',
        'saldo_disponible' => 'decimal:2',
        'saldo_bloqueado' => 'decimal:2',
        'fecha_apertura' => 'date',
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
