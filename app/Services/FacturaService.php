<?php

namespace App\Services;

use App\Repositories\FacturaRepository;


class FacturaService
{
    private $facturaRepository;

    public function __construct(FacturaRepository $facturaRepository)
    {
        $this->facturaRepository = $facturaRepository;
    }

    public function all($user)
    {
        $clienteId = null;
        if($user->rol == 'Cliente') {
            $clienteId = $user->cliente->id;
        }
        return $this->facturaRepository->all($clienteId);
    }

    public function create($data)
    {
        dd(1);
        return $this->facturaRepository->create($data);
    }

}
