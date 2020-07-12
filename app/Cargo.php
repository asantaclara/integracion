<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'cargo';
    protected $fillable = [
        'subscripcion_id',
        'fecha_desde',
        'fecha_hasta',
        'monto',
        'factura_id'
    ];
}
