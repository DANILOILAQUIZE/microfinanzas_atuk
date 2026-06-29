<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Socio extends Model
{
    protected $table = 'socios';

    protected $fillable = [
        'cedula',
        'nombres',
        'apellidos',
        'telefono',
        'direccion',
        'correo',
        'fecha_registro',
        'estado',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
    ];

    public function cuentasAhorro()
    {
        return $this->hasMany(CuentaAhorro::class, 'socio_id');
    }

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'socio_id');
    }

    public function alertasRiesgo()
    {
        return $this->hasMany(AlertaRiesgo::class, 'socio_id');
    }

    public function hechosCartera()
    {
        return $this->hasMany(HechoCartera::class, 'socio_id');
    }

    public function hechosMorosidad()
    {
        return $this->hasMany(HechoMorosidad::class, 'socio_id');
    }
}
