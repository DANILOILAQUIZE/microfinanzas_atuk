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
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class, 'prestamo_id');
    }
}
