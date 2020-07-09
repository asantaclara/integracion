<?php

namespace App\Services;

use App\Cliente;
use App\Empleado;
use App\Locacion;
use App\Repositories\LocacionRepository;
use Prophecy\Exception\Prophecy\MethodProphecyException;

class LocacionService
{
    private $locacionRepository;

    public function __construct(LocacionRepository $locacionRepository)
    {
        $this->locacionRepository = $locacionRepository;
    }
    public function all($user)
    {
        $clienteId = null;
        if($user->rol == 'Cliente') {
            $clienteId = $user->cliente->id;
        }
        return $this->locacionRepository->all($clienteId);
    }

    public function create($data)
    {
        return $this->locacionRepository->create($data);
    }

    public function update(Locacion $locacion, $data)
    {
        return $this->locacionRepository->update($locacion, $data);
    }

    public function empleadosDeLocacion(Locacion $locacion)
    {
        return $this->locacionRepository->empleadosDeLocacion($locacion);
    }
}
