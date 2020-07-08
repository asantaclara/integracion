<?php

namespace App\Repositories;

use App\Locacion;

class LocacionRepository
{
    public function all()
    {
        return Locacion::all();
    }

    public function create($data)
    {
        $locacion = new Locacion($data);
        $locacion->save();
        return $locacion;
    }

    public function update(Locacion $locacion, $data)
    {
        $locacion->update($data);
        return $locacion;
    }
}
