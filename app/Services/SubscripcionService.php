<?php

namespace App\Services;

use App\Cliente;
use App\Repositories\SubscripcionRepository;
use App\Subscripcion;
use Exception;


class SubscripcionService
{
    private $subscripcionRepository;

    public function __construct(SubscripcionRepository $subscripcionRepository)
    {
        $this->subscripcionRepository = $subscripcionRepository;
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
        if((isset($data['fecha_hasta']) && $data['fecha_hasta'] != '') || !isset($data['fecha_hasta'])) {

            $subscripcion = Subscripcion::where('servicio_id', $data['servicio_id'])
                ->where('locacion_id', $data['locacion_id'])
                ->orderByDesc('fecha_desde')->first();

            if(!$subscripcion->fecha_hasta) {
                throw new Exception('Ya tiene un subscripcion activa para esa locacion');
            }
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

        if($subscripcion) {
            throw new Exception('Ya tiene una subscripcion activa para esa locacion y fecha.');
        }
        return $this->subscripcionRepository->create($data);
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
