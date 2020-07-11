<?php

namespace App\Repositories;

use App\Horario_Laboral;
use Carbon\Carbon;

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

    public function createHorarios($horas, $periodo, $fechaHasta)
    {
        $count = 0;
        foreach ($horas as $hora) {
            $horas[$count][0] = Carbon::parse(substr($hora[0],0,24));
            $horas[$count][1] = Carbon::parse(substr($hora[1],0,24));
            $count++;
        }
        $horariosCreados = collect();
        $flag = 0;

        while ($flag == 0){
            $count = 0;
            foreach ($horas as $hora) {
                if($hora[1]->isBefore($fechaHasta)) {
                    $horarioLaboral = Horario_Laboral::create([
                        'fecha_desde' => $hora[0],
                        'fecha_hasta' => $hora[1]
                    ]);
                    $horas[$count][0] = $horas[$count][0]->addDays($periodo);
                    $horas[$count][1] = $horas[$count][1]->addDays($periodo);
                    $horariosCreados->push($horarioLaboral);
                } else {
                    $flag = 1;
                    continue;
                }
                $count++;
            }
        }
        return $horariosCreados;
    }
}
