<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';
    protected $fillable = [
        'cuit_cuil',
        'tipo_categoria',
        'tipo_cliente',
        'forma_pago_habitual',
        'direccion',
        'nombre_razon_social',
        'cuenta_bancaria',
        'email',
        'telefono'
    ];

    public function locaciones()
    {
        return $this->hasMany('App\Locacion','cliente_id');
    }

    public function empleados()
    {
        return $this->hasMany('App\Empleado','cliente_id');
    }
}
