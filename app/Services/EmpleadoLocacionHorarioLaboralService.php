<?php

namespace App\Services;

use App\Empleado;
use App\Empleado_Locacion;
use App\Empleado_Locacion_Horario_Laboral;
use App\Horario_Laboral;
use App\Http\Controllers\EmpleadoLocacionHorarioLaboralController;
use App\Repositories\EmpleadoLocacionHorarioLaboralRepository;
use App\Repositories\HorarioLaboralRepository;
use Carbon\Carbon;

class EmpleadoLocacionHorarioLaboralService
{
    private $empleadoLocacionHorarioLaboralRepository;

    public function __construct(EmpleadoLocacionHorarioLaboralRepository $empleadoLocacionHorarioLaboralRepository)
    {
        $this->empleadoLocacionHorarioLaboralRepository = $empleadoLocacionHorarioLaboralRepository;
    }

    public function create($empleadoLocaciones, $horariosCreados)
    {
        foreach ($empleadoLocaciones as $empleado) {
            foreach ($horariosCreados as $horario) {
                Empleado_Locacion_Horario_Laboral::create([
                    'empleado_locacion_id' => $empleado->id,
                    'horario_laboral_id'=> $horario->id,
                ]);
            }
        }

        return true;
    }

}
