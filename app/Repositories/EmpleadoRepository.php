<?php

namespace App\Repositories;

use App\empleado;

class EmpleadoRepository
{
    public function all()
    {
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
