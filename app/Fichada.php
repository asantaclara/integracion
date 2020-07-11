<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fichada extends Model
{
    protected $table = 'fichada';
    protected $fillable = [
        'empleado_id',
        'locacion_id',
        'fecha_hora_entrada',
        'fecha_hora_salida',
        'minutos_trabajados',
        'justificacion',
        'activa',
        'fichada_original'
    ];

    public function locacion()
    {
        return $this->hasOne(Locacion::class,'id', 'locacion_id');
    }
}
