<?php

namespace App\Repositories;

use App\Empleado;

class EmpleadoRepository
{
    public function all($clienteId = null)
    {
        if($clienteId){
            return Empleado::where('cliente_id',$clienteId)->get();
        }
        return Empleado::all();
    }

    public function create($data)
    {
        $empleado = new Empleado($data);
        $empleado->save();
        return $empleado;
    }

    public function update(Empleado $empleado, $data)
    {
        $empleado->update($data);
        return $empleado;
    }
}
