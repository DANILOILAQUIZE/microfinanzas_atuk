<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = [
        'rol_id',
        'nombre',
        'apellido',
        'email',
        'password',
        'estado',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function polizas()
    {
        return $this->hasMany(Poliza::class, 'usuario_id');
    }

    public function movimientosAhorro()
    {
        return $this->hasMany(MovimientoAhorro::class, 'usuario_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'usuario_id');
    }

    public function reportes()
    {
        return $this->hasMany(Reporte::class, 'usuario_creador_id');
    }

    public function auditoriaLogs()
    {
        return $this->hasMany(AuditoriaLog::class, 'usuario_id');
    }

    public function historialCambios()
    {
        return $this->hasMany(HistorialCambio::class, 'usuario_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'usuario_id');
    }
}
