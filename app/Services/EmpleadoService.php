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
    public function all($user)
    {
        $clienteId = null;
        if($user->rol == 'Cliente') {
            $clienteId = $user->cliente->id;
        }
        return $this->empleadoRepository->all($clienteId);
    }

    public function create($data)
    {
        return $this->empleadoRepository->create($data);
    }

    public function update(Empleado $empleado, $data)
    {
        return $this->empleadoRepository->update($empleado, $data);
    }

}
