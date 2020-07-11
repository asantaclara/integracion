<?php

namespace App\Services;

use App\Cliente;
use App\Repositories\SubscripcionRepository;
use App\Subscripcion;


class SubscripcionService
{
    private $subscripcionRepository;

    public function __construct(SubscripcionRepository $subscripcionRepository)
    {
        $this->subscripcionRepository = $subscripcionRepository;
    }
    public function all($user)
    {
        $clienteId = null;
        if($user->rol == 'Cliente') {
            $clienteId = $user->cliente->id;
        }
        return $this->subscripcionRepository->all($clienteId);
    }

    public function create($data)
    {
        return $this->subscripcionRepository->create($data);
    }

    public function update(Subscripcion $subscripcion, $data)
    {
        return $this->subscripcionRepository->update($subscripcion, $data);
    }

    public function subscripcionesDeCliente(Cliente $cliente)
    {
        return $this->subscripcionRepository->subscripcionesDeCliente($cliente);
    }
}
