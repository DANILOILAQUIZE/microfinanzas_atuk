<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPrestamo extends Model
{
    protected $table = 'tipos_prestamo';

    protected $fillable = [
        'nombre',
        'interes',
        'plazo_maximo',
    ];

    protected $casts = [
        'interes' => 'decimal:2',
    ];

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'tipo_prestamo_id');
    }

    public function hechosCartera()
    {
        return $this->hasMany(HechoCartera::class, 'tipo_prestamo_id');
    }

    public function hechosRentabilidad()
    {
        return $this->hasMany(HechoRentabilidad::class, 'tipo_prestamo_id');
    }
}
