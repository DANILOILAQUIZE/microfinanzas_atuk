<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoAhorro extends Model
{
    protected $table = 'movimientos_ahorro';

    protected $fillable = [
        'cuenta_id',
        'usuario_id',
        'tipo_movimiento',
        'metodo_transaccion',
        'referencia',
        'monto',
        'saldo_anterior',
        'saldo_posterior',
        'fecha_movimiento',
        'descripcion',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'saldo_anterior' => 'decimal:2',
        'saldo_posterior' => 'decimal:2',
        'fecha_movimiento' => 'datetime',
    ];

    public function cuenta()
    {
        return $this->belongsTo(CuentaAhorro::class, 'cuenta_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
