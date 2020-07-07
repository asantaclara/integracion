<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Locacion extends Model
{
    protected $table = 'locacion';
    protected $fillable = [
        'cliente_id',
        'direccion',
        'descripcion'
    ];
}
