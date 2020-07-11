<?php

namespace App\Repositories;


use App\Pago;

class PagoRepository
{
    public function all($clienteId = null)
    {
        $pago = Pago::join('cliente as c', 'c.id', 'pago.cliente_id')
            ->select('pago.id', 'pago.monto', 'pago.fecha', 'c.cuit_cuil', 'c.nombre_razon_social');

        if($clienteId){
            $pago->where('cliente_id', $clienteId);
        }
        return $pago->get();
    }

    public function create($data)
    {
        return Pago::create($data);
    }
}
