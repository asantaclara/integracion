<?php

namespace App\Services;

use App\Empleado;
use App\Empleado_Locacion;
use App\Horario_Laboral;
use App\Http\Controllers\EmpleadoLocacionHorarioLaboralController;
use App\Repositories\HorarioLaboralRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HorarioLaboralService
{
    private $horarioLaboralRepository;
    private $empleadoLocacionHorarioLaboralService;

    public function __construct(HorarioLaboralRepository $horarioLaboralRepository, EmpleadoLocacionHorarioLaboralService $empleadoLocacionHorarioLaboralService)
    {
        $this->horarioLaboralRepository = $horarioLaboralRepository;
        $this->empleadoLocacionHorarioLaboralService = $empleadoLocacionHorarioLaboralService;
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

    public function asignarHorariosLaborales($data)
    {
        return DB::transaction(function () use ($data) {

            $primerHorario = $data['horarios'][0];
            $ultimoHorario = $data['horarios'][sizeof($data['horarios']) - 1];

            if (Carbon::parse(substr($primerHorario[0], 0, 24))->diffInDays(Carbon::parse(substr($ultimoHorario[1], 0, 24))) + 1 > $data['periodo']) {
                throw new \Exception("El periodo es menor a los dias seleccionados");
            }

            $empleadoLocaciones = Empleado_Locacion::
            whereIn('empleado_id', $data['empleados_id'])
                ->where('locacion_id', $data['locacion_id'])
                ->get();

            $hasta = Carbon::parse(($data['hasta']))->isAfter(Carbon::parse('2021-01-01')) ? Carbon::parse('2021-01-01') : Carbon::parse($data['hasta']);

            $horariosCreados = $this->horarioLaboralRepository->createHorarios($data['horarios'], $data['periodo'], $hasta);

            $result = $this->empleadoLocacionHorarioLaboralService->create($empleadoLocaciones, $horariosCreados);

            return $result;
        });
    }
}
