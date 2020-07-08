<?php

namespace App\Services;

use App\Locacion;
use App\Repositories\LocacionRepository;

class LocacionService
{
    private $locacionRepository;

    public function __construct(LocacionRepository $locacionRepository)
    {
        $this->locacionRepository = $locacionRepository;
    }
    public function all()
    {
        return $this->locacionRepository->all();
    }

    public function create($data)
    {
        return $this->locacionRepository->create($data);
    }

    public function update(Locacion $locacion, $data)
    {
        return $this->locacionRepository->update($locacion, $data);
    }
}
