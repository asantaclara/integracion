<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empleado_Locacion extends Model
{
    protected $table = 'empleado_locacion';
    protected $fillable = [
        'empleado_id',
        'locacion_id',
        'fecha_vinculacion',
        'fecha_desvinculacion'
    ];

}
