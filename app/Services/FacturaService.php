<?php

namespace App\Services;

use App\Cargo;
use App\Repositories\FacturaRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;


class FacturaService
{
    private $facturaRepository;

    public function __construct(FacturaRepository $facturaRepository)
    {
        $this->facturaRepository = $facturaRepository;
    }

    public function all($user)
    {
        $clienteId = null;
        if($user->rol == 'Cliente') {
            $clienteId = $user->cliente->id;
        }
        return $this->facturaRepository->all($clienteId);
    }

    public function create($data)
    {
        return DB::transaction(function () use ($data) {
            $cargos = Cargo::join('subscripcion as s', 's.id' ,'cargo.subscripcion_id')
                ->join('locacion as l', 'l.id', 's.locacion_id')
                ->where('cliente_id', $data['cliente_id'])
                ->whereNull('cargo.factura_id')
                ->where('cargo.created_at', '<=', Carbon::parse($data['fecha_hasta'])->addDay())
                ->select('cargo.*')
                ->get();

            if(count($cargos) < 1) {
                throw new Exception('El cliente no tiene cargos a facturar');
            }
            $facturaData = [];
            $facturaData['cliente_id'] = $data['cliente_id'];
            $facturaData['fecha'] = Carbon::now();
            $facturaData['fecha_desde'] = $cargos->min('created_at');
            $facturaData['fecha_hasta'] = $data['fecha_hasta'];
            $facturaData['monto'] = $cargos->sum('monto');
            $facturaData['forma_pago'] = $data['forma_pago'];

            $factura = $this->facturaRepository->create($facturaData);

            foreach ($cargos as $cargo) {
                $cargo->factura_id = $factura->id;
                $cargo->save();
            }

            return $factura;
        });
    }

}
