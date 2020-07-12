<?php

namespace App\Services;

use App\Cliente;
use App\Http\Controllers\ServicioController;
use App\Repositories\CargoRepository;
use App\Repositories\PagoRepository;
use App\Repositories\SubscripcionRepository;
use App\Servicio;
use App\Subscripcion;
use Carbon\Carbon;
use Exception;


class CargoService
{
    private $cargoRepository;

    public function __construct(CargoRepository $cargoRepository)
    {
        $this->cargoRepository = $cargoRepository;
    }

    public function all($user)
    {
        return $this->cargoRepository->all();
    }

    public function create($data)
    {
        return $this->cargoRepository->create($data);
    }

    public function crearCargoDeSubscripcion($subscripcion)
    {
        $servicio = Servicio::where('id', $subscripcion->servicio_id)->first();

        $data = [];
        $data['subscripcion_id'] = $subscripcion->id;
        $data['fecha_desde'] = $subscripcion->fecha_desde;
        $data['fecha_hasta'] = $subscripcion->fecha_hasta ? $subscripcion->fecha_hasta : Carbon::parse($subscripcion->fecha_desde)->endOfMonth()->format('Y-m-d');
        $dias = Carbon::parse($data['fecha_desde'])->diffInDays(Carbon::parse($data['fecha_hasta']));
        $data['monto'] = $servicio->monto * ($dias ? $dias : 1);

        return $this->cargoRepository->create($data);
    }

}
