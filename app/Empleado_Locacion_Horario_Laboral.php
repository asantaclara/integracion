<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empleado_Locacion_Horario_Laboral extends Model
{
    protected $table = 'empleado_locacion_horario_laboral';
    protected $fillable = [
        'empleado_locacion_id',
        'horario_laboral_id'
    ];
}
