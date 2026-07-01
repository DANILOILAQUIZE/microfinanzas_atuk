<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Garantia extends Model
{
    protected $table = 'garantias';

    protected $fillable = [
        'prestamo_id',
        'tipo',
        'descripcion',
        'valor',
        'estado',
        'documento_soporte',
        'fecha_registro',
        'fecha_liberacion',
        'observaciones',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'fecha_registro' => 'date',
        'fecha_liberacion' => 'date',
    ];

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class, 'prestamo_id');
    }
}
