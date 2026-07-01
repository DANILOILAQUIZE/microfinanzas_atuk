<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPrestamo extends Model
{
    protected $table = 'tipos_prestamo';

    protected $fillable = [
        'nombre',
        'descripcion',
        'interes',
        'monto_minimo',
        'monto_maximo',
        'plazo_minimo',
        'plazo_maximo',
        'estado',
        'requiere_garantia',
    ];

    protected $casts = [
        'interes' => 'decimal:2',
        'monto_minimo' => 'decimal:2',
        'monto_maximo' => 'decimal:2',
        'requiere_garantia' => 'boolean',
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
