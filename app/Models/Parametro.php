<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    protected $table = 'parametros';

    protected $fillable = [
        'clave',
        'nombre',
        'valor',
        'descripcion',
        'tipo',
        'grupo',
    ];
}
