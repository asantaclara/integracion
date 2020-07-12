<?php

namespace App\Repositories;


use App\Factura;
use App\Pago;

class PagoRepository
{
    public function all($clienteId = null)
    {
        $pago = Pago::join('cliente as c', 'c.id', 'pago.cliente_id')
            ->select('pago.id', 'pago.monto', 'pago.fecha', 'c.cuit_cuil', 'c.nombre_razon_social', 'pago.cliente_id');

        if($clienteId){
            $pago->where('cliente_id', $clienteId);
        }

        $pagos = $pago->get();

        $count = 0;
        foreach ($pagos as $pago) {
            $montoFacturas = Factura::where('cliente_id', $pago->cliente_id)
                ->where('fecha_hasta', '<=', $pago->fecha)
                ->sum('monto');

            $montoPagos = Pago::where('cliente_id', $pago->cliente_id)
                ->where('fecha', '<=', $pago->fecha)
                ->sum('monto');

            $pagos[$count]->estado_de_cuenta =  $montoFacturas - $montoPagos;
            $count++;
        }

        return $pagos;
    }

    public function create($data)
    {
        return Pago::create($data);
    }
}
