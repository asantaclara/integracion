<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'factura';
    protected $fillable = [
        'cliente_id',
        'fecha',
        'fecha_desde',
        'fecha_hasta',
        'monto',
        'forma_pago',
        'pagada'
    ];
}
