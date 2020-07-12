<?php

namespace App\Services;

use App\Cliente;
use App\Repositories\SubscripcionRepository;
use App\Subscripcion;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;


class SubscripcionService
{
    private $subscripcionRepository;
    private $cargoService;

    public function __construct(SubscripcionRepository $subscripcionRepository, CargoService $cargoService)
    {
        $this->subscripcionRepository = $subscripcionRepository;
        $this->cargoService = $cargoService;
    }
    public function all($user)
    {
        $clienteId = null;
        if($user->rol == 'Cliente') {
            $clienteId = $user->cliente->id;
        }
        return $this->subscripcionRepository->all($clienteId);
    }

    public function create($data)
    {
        return DB::transaction(function () use ($data) {
            if ((isset($data['fecha_hasta']) && $data['fecha_hasta'] == '') || !isset($data['fecha_hasta'])) {
                $subscripcion = Subscripcion::where('servicio_id', $data['servicio_id'])
                    ->where('locacion_id', $data['locacion_id'])
                    ->orderByDesc('fecha_desde')->first();

                if (!$subscripcion->fecha_hasta) {
                    throw new Exception('Ya tiene un subscripcion activa para esa locacion');
                } else {
                    $subscripcion = null;
                }
            } else {
                if ($data['servicio_id'] == 1) { // Si el servicio es mensual, la fecha de finalizacion es fin de mes.
                    $data['fecha_hasta'] = Carbon::parse($data['fecha_hasta'])->endOfMonth()->format('Y-m-d');
                }

                $subscripcion = Subscripcion::where('servicio_id', $data['servicio_id'])
                    ->where('locacion_id', $data['locacion_id'])
                    ->where('fecha_desde', '<=', $data['fecha_hasta'])
                    ->where(function ($query) use ($data) {
                        $query->where('fecha_hasta', '>=', $data['fecha_desde'])
                            ->orWhereNull('fecha_hasta');
                    })
                    ->orderByDesc('fecha_desde')
                    ->first();
            }

            if (Carbon::parse($data['fecha_desde'])->diffInDays(Carbon::parse($data['fecha_hasta'])) <= Carbon::parse($data['fecha_desde'])->daysInMonth && $data['servicio_id'] == 1) {
                throw new \Exception('El periodo de tiempo solicitado es incompatible con un servicio mensual');
            }
            if ($subscripcion) {
                throw new Exception('Ya tiene una subscripcion activa para esa locacion y fecha.');
            }

            $subscripcion = $this->subscripcionRepository->create($data);

            $cargo = $this->cargoService->crearCargoDeSubscripcion($subscripcion);

            return [$subscripcion, $cargo];
        });
    }

    public function update(Subscripcion $subscripcion, $data)
    {
        return $this->subscripcionRepository->update($subscripcion, $data);
    }

    public function subscripcionesDeCliente(Cliente $cliente)
    {
        return $this->subscripcionRepository->subscripcionesDeCliente($cliente);
    }
}
