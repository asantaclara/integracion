<?php

namespace App\Repositories;

use App\Cargo;

class CargoRepository
{
    public function all()
    {
        return Cargo::all();
    }

    public function create($data)
    {
        return Cargo::create($data);
    }
}
