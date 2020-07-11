<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Horario_Laboral extends Model
{
    protected $table = 'horario_laboral';
    protected $fillable = [
        'fecha_desde',
        'fecha_hasta'
    ];
}
