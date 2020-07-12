<?php

namespace App\Repositories;


use App\Factura;

class FacturaRepository
{
    public function all($clienteId = null)
    {
        $pago = Factura::join('cliente as c', 'c.id', 'factura.cliente_id')
            ->select('factura.*','c.nombre_razon_social', 'c.cuit_cuil');

        if($clienteId){
            $pago->where('cliente_id', $clienteId);
        }
        return $pago->get();
    }

    public function create($data)
    {
        return Factura::create($data);
    }
}
