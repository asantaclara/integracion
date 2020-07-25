<?php

namespace App\Repositories;

use App\Empleado;
use App\Empleado_Locacion;
use Carbon\Carbon;
use Carbon\Traits\Boundaries;

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

    public function desvincular(\App\Empleado $empleado)
    {
        $emplado_locacion = Empleado_Locacion::where('empleado_id', $empleado->id)->get();

        foreach ($emplado_locacion as $e) {
            if($e->fecha_desvinculacion && Carbon::parse($e->fecha_desvinculacion)->isAfter(Carbon::now()) || !$e->fecha_desvinculacion) {
                $e->fecha_desvinculacion = Carbon::now();
                $e->save();
            }
        }
        return $emplado_locacion;
    }
}
