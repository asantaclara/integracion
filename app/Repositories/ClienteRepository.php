<?php

namespace App\Repositories;

use App\Cliente;
use App\User;
use Couchbase\UserSettings;

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

    public function destroy(Cliente $cliente)
    {
        $users = User::where('cliente_id', $cliente->id)->get();

        foreach ($users as $u) {
            $u->activo = 0;
            $u->save();
        }

        return count($users) > 0;
    }
}
