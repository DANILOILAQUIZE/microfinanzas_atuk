<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poliza extends Model
{
    protected $table = 'polizas';

    protected $fillable = [
        'usuario_id',
        'fecha',
        'tipo',
        'concepto',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePoliza::class, 'poliza_id');
    }
}
