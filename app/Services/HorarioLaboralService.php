<?php

namespace App\Services;

use App\Horario_Laboral;
use App\Repositories\HorarioLaboralRepository;

class HorarioLaboralService
{
    private $horarioLaboralRepository;

    public function __construct(HorarioLaboralRepository $horarioLaboralRepository)
    {
        $this->horarioLaboralRepository = $horarioLaboralRepository;
    }
    public function all()
    {
        return $this->horarioLaboralRepository->all();
    }

    public function create($data)
    {
        return $this->horarioLaboralRepository->create($data);
    }

    public function update(Horario_Laboral $horarioLaboral, $data)
    {
        return $this->horarioLaboralRepository->update($horarioLaboral, $data);
    }

    public function asignarHorariosLaborales(array $request)
    {

    }
}
