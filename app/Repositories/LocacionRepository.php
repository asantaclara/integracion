<?php

namespace App\Repositories;

use App\Locacion;

class LocacionRepository
{
    public function all($clienteId = null)
    {
        if($clienteId){
            return Locacion::where('cliente_id',$clienteId)->get();
        }
        return Locacion::all();
    }

    public function create($data)
    {
        $locacion = new Locacion($data);
        $locacion->save();
        return $locacion;
    }

    public function update(Locacion $locacion, $data)
    {
        $locacion->update($data);
        return $locacion;
    }

    public function empleadosDeLocacion(Locacion $locacion)
    {
        return $locacion->empleados;
    }
}
