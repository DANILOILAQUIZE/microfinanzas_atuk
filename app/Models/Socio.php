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
        'fecha_nacimiento',
        'genero',
        'telefono',
        'direccion',
        'ciudad',
        'ocupacion',
        'ingresos_mensuales',
        'correo',
        'fecha_registro',
        'estado',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'fecha_nacimiento' => 'date',
        'ingresos_mensuales' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accesorios para mantener compatibilidad con las vistas
    public function getNombreAttribute()
    {
        return $this->attributes['nombres'] ?? null;
    }

    public function getApellidoAttribute()
    {
        return $this->attributes['apellidos'] ?? null;
    }

    public function getEmailAttribute()
    {
        return $this->attributes['correo'] ?? null;
    }

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
