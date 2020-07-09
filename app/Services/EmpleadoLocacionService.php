<?php

namespace App\Services;

use App\Empleado;
use App\Empleado_Locacion;
use App\Repositories\EmpleadoLocacionRepository;
use Exception;

class EmpleadoLocacionService
{
    private $empleadoLocacionRepository;

    public function __construct(EmpleadoLocacionRepository $empleadoLocacionRepository)
    {
        $this->empleadoLocacionRepository = $empleadoLocacionRepository;
    }

    public function asignarLocacion($data)
    {
        $empleado = Empleado::where('id', $data['empleado_id'])->first();

        $locacionesEmpleador = $empleado->cliente->locaciones;

        if(count($locacionesEmpleador->where('id', $data['locacion_id'])) == 0) {
            throw new Exception('La locacion no pertenece al empleador del empleado');
        }

        $empleadoLocacion = Empleado_Locacion::where('empleado_id', $data['empleado_id'])
                                ->where('locacion_id', $data['locacion_id'])->get();

        if(count($empleadoLocacion) > 0) {
            throw new Exception('Empleado ya asignado a la locacion');
        }

        return $this->empleadoLocacionRepository->create($data);
    }
}
