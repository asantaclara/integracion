<?php

namespace App\Repositories;

use App\Cliente;
use App\Subscripcion;

class SubscripcionRepository
{
    public function all($clienteId)
    {
        if($clienteId){
            return Subscripcion::join('locacion as l', 'l.id', 'subscripcion.locacion_id')
                ->where('l.cliente_id',$clienteId)
                ->get();
        }
        return Subscripcion::all();
    }

    public function create($data)
    {
        return Subscripcion::create([$data]);
    }

    public function update(Subscripcion $subscripcion, $data)
    {
        $subscripcion->update($data);
        return $subscripcion;
    }

    public function subscripcionesDeCliente(Cliente $cliente)
    {
        return Subscripcion::whereIn('locacion_id', $cliente->locaciones->pluck('id'))->with('servicio')->get();
    }

}
