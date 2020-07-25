<?php

namespace App\Repositories;

use App\Empleado_Locacion;
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
        $data['duracion_minutos'] = Carbon::parse($data['hora_hasta'])->diffInMinutes(Carbon::parse($data['hora_desde']));

        return Horario_Laboral::create($data);
    }

    public function update(Horario_Laboral $horarioLaboral, $data)
    {
        $data['duracion_minutos'] = Carbon::parse($data['hora_hasta'])->diffInMinutes(Carbon::parse($data['hora_desde']));
        $horarioLaboral->update($data);
        return $horarioLaboral;
    }

    public function createHorarios($horas, $periodo, $fechaHasta)
    {
        $count = 0;
        foreach ($horas as $hora) {
            $horas[$count][0] = Carbon::parse($hora[0])->setTimezone('America/Argentina/Buenos_Aires');
            $horas[$count][1] = Carbon::parse($hora[1])->setTimezone('America/Argentina/Buenos_Aires');
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
                        'fecha_hasta' => $hora[1],
                        'duracion_minutos' => Carbon::parse($hora[1])->diffInMinutes(Carbon::parse($hora[0]))
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

    public function horarioLaboralDeEmpleado($data)
    {
        return Empleado_Locacion::join('empleado_locacion_horario_labora as elhl', 'elhl.empleado_locacion_id', 'empleado_locacion.id')
            ->join('horario_laboral as hl', 'hl.id', 'elhl.horario_laboral_id')
            ->where('empleado_locacion.empleado_id', $data['empleado_id'])
            ->where('empleado_locacion.locacion_id', $data['locacion_id'])
            ->get();
    }
}
