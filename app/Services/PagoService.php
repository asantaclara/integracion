<?php

namespace App\Services;

use App\Cliente;
use App\Repositories\PagoRepository;
use App\Repositories\SubscripcionRepository;
use App\Subscripcion;
use Exception;


class PagoService
{
    private $pagoRepository;

    public function __construct(PagoRepository $pagoRepository)
    {
        $this->pagoRepository = $pagoRepository;
    }

    public function all($user)
    {
        $clienteId = null;
        if($user->rol == 'Cliente') {
            $clienteId = $user->cliente->id;
        }
        return $this->pagoRepository->all($clienteId);
    }

    public function create($data)
    {
        return $this->pagoRepository->create($data);
    }

}
