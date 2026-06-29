<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePoliza extends Model
{
    protected $table = 'detalle_poliza';

    protected $fillable = [
        'poliza_id',
        'cuenta',
        'debe',
        'haber',
    ];

    protected $casts = [
        'debe' => 'decimal:2',
        'haber' => 'decimal:2',
    ];

    public function poliza()
    {
        return $this->belongsTo(Poliza::class, 'poliza_id');
    }
}
