<?php

namespace App\Services;

use App\Cliente;
use App\Repositories\ClienteRepository;

class ClienteService
{
    private $clienteRepository;

    public function __construct(ClienteRepository $clienteRepository)
    {
        $this->clienteRepository = $clienteRepository;
    }
    public function all()
    {
        return $this->clienteRepository->all();
    }

    public function create($data)
    {
        return $this->clienteRepository->create($data);
    }

    public function update(Cliente $cliente, $data)
    {
        return $this->clienteRepository->update($cliente, $data);
    }
}
