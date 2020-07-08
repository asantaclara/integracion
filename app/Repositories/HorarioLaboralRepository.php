<?php

namespace App\Repositories;

use App\Horario_Laboral;

class HorarioLaboralRepository
{
    public function all()
    {
        return Horario_Laboral::all();
    }

    public function create($data)
    {
        $horarioLaboral = new Horario_Laboral($data);
        $horarioLaboral->save();
        return $horarioLaboral;
    }

    public function update(Horario_Laboral $horarioLaboral, $data)
    {
        $horarioLaboral->update($data);
        return $horarioLaboral;
    }
}
