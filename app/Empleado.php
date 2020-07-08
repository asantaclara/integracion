<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleado';
    protected $fillable = [
        'id',
        'nombre',
        'legajo',
        'tipo_documento',
        'documento',
        'direccion',
        'telefono',
        'nacionalidad',
        'genero',
        'cliente_id'
    ];

    public function cliente()
    {
        return $this->hasOne(Cliente::class,'id', 'cliente_id');
    }

}
