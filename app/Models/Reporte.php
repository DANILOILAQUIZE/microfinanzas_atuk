<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $table = 'reportes';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'tipo',
        'configuracion',
        'usuario_creador_id',
        'activo',
    ];

    protected $casts = [
        'configuracion' => 'array',
        'activo' => 'boolean',
    ];

    public function usuarioCreador()
    {
        return $this->belongsTo(Usuario::class, 'usuario_creador_id');
    }
}
