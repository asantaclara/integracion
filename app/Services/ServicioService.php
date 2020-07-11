<?php

namespace App\Services;

use App\Empleado;
use App\Empleado_Locacion;
use App\Horario_Laboral;
use App\Http\Controllers\EmpleadoLocacionHorarioLaboralController;
use App\Repositories\HorarioLaboralRepository;
use App\Repositories\ServicioRepository;
use App\Servicio;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ServicioService
{
    private $servicioRepository;

    public function __construct(ServicioRepository $servicioRepository)
    {
        $this->servicioRepository = $servicioRepository;
    }
    public function all()
    {
        return $this->servicioRepository->all();
    }

    public function create($data)
    {
        return $this->servicioRepository->create($data);
    }

    public function update(Servicio $servicio, $data)
    {
        return $this->servicioRepository->update($servicio, $data);
    }
}
