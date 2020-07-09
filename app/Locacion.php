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

    public function empleados()
    {
        return $this->belongsToMany(
            'App\Empleado',
            'empleado_locacion',
            'locacion_id',
            'empleado_id'
        );
    }

    public function cliente()
    {
        return $this->belongsTo('App\Cliente', 'cliente_id');
    }
}
