<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscripcion extends Model
{
    protected $table = 'subscripcion';

    protected $fillable = [
        'locacion_id',
        'servicio_id',
        'fecha_desde',
        'fecha_hasta'
    ];

    public function servicio()
    {
        return $this->hasOne(Servicio::class,'id', 'servicio_id');
    }
}
