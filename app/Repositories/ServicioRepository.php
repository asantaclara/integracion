<?php

namespace App\Repositories;

use App\Servicio;

class ServicioRepository
{
    public function all()
    {
        return Servicio::all();
    }

    public function create($data)
    {
        return Servicio::create([$data]);
    }

    public function update(Servicio $servicio, $data)
    {
        $servicio->update($data);
        return $servicio;
    }

}
