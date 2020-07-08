<?php

namespace App\Repositories;

use App\Empleado_Locacion;

class EmpleadoLocacionRepository
{
    public function all()
    {
        return Empleado_Locacion::all();
    }

    public function create($data)
    {
        $empleadoLocacion = new Empleado_Locacion($data);
        $empleadoLocacion->save();
        return $empleadoLocacion;
    }

    public function update(Empleado_Locacion $empleadoLocacion, $data)
    {
        $empleadoLocacion->update($data);
        return $empleadoLocacion;
    }
}
