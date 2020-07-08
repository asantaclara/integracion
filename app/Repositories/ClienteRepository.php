<?php

namespace App\Repositories;

use App\Cliente;

class ClienteRepository
{
    public function all()
    {
        return Cliente::all();
    }

    public function create($data)
    {
        $cliente = new Cliente($data);
        $cliente->save();
        return $cliente;
    }

    public function update(Cliente $cliente, $data)
    {
        $cliente->update($data);
        return $cliente;
    }
}
