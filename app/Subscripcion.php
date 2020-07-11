<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscripcion extends Model
{
    protected $table = 'subscripcion';

    public function servicio()
    {
        return $this->hasOne(Servicio::class,'id', 'servicio_id');
    }
}
