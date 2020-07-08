<?php

namespace App\Services;

use App\Empleado;
use App\Empleado_Locacion;
use App\Repositories\EmpleadoLocacionRepository;
use App\Repositories\EmpleadoRepository;
use PHPUnit\Framework\Exception;

class EmpleadoService
{
    private $empleadoRepository;
    private $empleadoLocacionRepository;

    public function __construct(EmpleadoRepository $empleadoRepository, EmpleadoLocacionRepository $empleadoLocacionRepository)
    {
        $this->empleadoRepository = $empleadoRepository;
        $this->empleadoLocacionRepository = $empleadoLocacionRepository;
    }
    public function all()
    {
        return $this->empleadoRepository->all();
    }

    public function create($data)
    {
        return $this->empleadoRepository->create($data);
    }

    public function update(Empleado $empleado, $data)
    {
        return $this->empleadoRepository->update($empleado, $data);
    }

    public function asignarLocacion($data)
    {
        $empleado = Empleado::where('id', $data['empleado_id'])->first();

        $locacionesEmpleador = $empleado->cliente->locaciones;

        if(count($locacionesEmpleador->where('id', $data['locacion_id'])) == 0) {
            throw new Exception('La locacion no pertenece al empleador del empleado');
        }

        return $this->empleadoLocacionRepository->create($data);
    }
}
