<?php

namespace App\Repositories;

use App\Cliente;
use App\Subscripcion;
use Carbon\Carbon;

class SubscripcionRepository
{
    public function all($clienteId)
    {
        $subs = Subscripcion::join('locacion as l', 'l.id', 'subscripcion.locacion_id')
            ->join('servicio as s', 's.id', 'subscripcion.servicio_id')
            ->join('cliente as c', 'c.id', 'l.cliente_id')
            ->select('subscripcion.id','c.nombre_razon_social','s.descripcion','l.direccion','subscripcion.fecha_desde','subscripcion.fecha_hasta');

        if($clienteId){
            $subs->where('l.cliente_id',$clienteId);
        }

        return $subs->get();
    }

    public function create($data)
    {
        return Subscripcion::create($data);
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

    public function destroy(Subscripcion $subscripcion)
    {
        $subscripcion->fecha_hasta = Carbon::now();
        $subscripcion->save();
        return $subscripcion;
    }

}
