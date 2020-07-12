<?php

namespace App\Services;

use App\Empleado_Locacion;
use App\Fichada;
use App\Repositories\FichadaRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FichadaService
{
    private $fichadaRepository;

    public function __construct(FichadaRepository $fichadaRepository)
    {
        $this->fichadaRepository = $fichadaRepository;
    }

    public function create($data)
    {
        $user = Auth::guard('api')->user();
        $data['locacion_id'] = $user->locacion_id;
        $data['fecha_hora'] = Carbon::now();

        $empleadoLocacion = Empleado_Locacion::where('empleado_id', $data['empleado_id'])
            ->where('locacion_id', $data['locacion_id'])
            ->orderByDesc('fecha_vinculacion')
            ->first();

        if($empleadoLocacion->fecha_desvinculacion && Carbon::parse($empleadoLocacion->fecha_desvinculacion)->isBefore(Carbon::now())) {
            throw new \Exception("El empleado no tiene persmiso para acceder al establecimiento");
        }

        $ultimaFichada = Fichada::where('empleado_id', $data['empleado_id'])
            ->where('locacion_id', $data['locacion_id'])
            ->orderByDesc('fecha_hora_entrada')
            ->first();

        if($ultimaFichada) {
            if($ultimaFichada->fecha_hora_salida && $data['accion'] == 'Salida') {
                throw new \Exception('El empleado figura fuera del establecimiento');
            } else if(!$ultimaFichada->fecha_hora_salida && $data['accion'] == 'Entrada') {
                throw new \Exception('El empleado figura dentro del establecimiento');
            }
        } else if($data['accion'] == 'Salida') {
            throw new \Exception('El empleado figura fuera del establecimiento');
        }

        if($ultimaFichada && !$ultimaFichada->fecha_hora_salida) {
             $ultimaFichada->update([
                'fecha_hora_salida' => Carbon::now(),
                'minutos_trabajados' => Carbon::now()->diffInMinutes(Carbon::parse($ultimaFichada->fecha_hora_entrada))
            ]);
        } else {
            $ultimaFichada = $this->fichadaRepository->create($data);
        }
        return $ultimaFichada;
    }

    public function fichadasDeCliente($data)
    {
        return $this->fichadaRepository->fichadasDeCliente($data);
    }

    public function update($data, $fichada)
    {
        $user = Auth::guard('api')->user();

        if(!in_array($fichada->empleado_id, $user->cliente->empleados->pluck('id')->toArray())) {
            throw new \Exception("La fichada no le pertenece al cliente");
        }
        $data['minutos_trabajados'] = Carbon::parse($data['fecha_hora_salida'])->diffInMinutes(Carbon::parse($data['fecha_hora_entrada']));
        $data['fecha_hora_entrada'] = Carbon::parse($data['fecha_hora_entrada']);
        $data['fecha_hora_salida'] = Carbon::parse($data['fecha_hora_salida']);
        $fichada->update($data);
        return $fichada;
    }

    public function crearFichadaManual($data)
    {
        $empleadoLocacion = Empleado_Locacion::where('empleado_id', $data['empleado_id'])
            ->where('locacion_id', $data['locacion_id'])
            ->orderByDesc('fecha_vinculacion')
            ->first();

        if($empleadoLocacion->fecha_desvinculacion && Carbon::parse($empleadoLocacion->fecha_desvinculacion)->isBefore(Carbon::parse($data['fecha_hora_entrada']))) {
            throw new \Exception("El empleado no pertenece al establecimiento a la fecha de la fichada");
        }

        $fichada = Fichada::where('empleado_id', $data['empleado_id'])
            ->where('locacion_id', $data['locacion_id'])
            ->where('fecha_hora_entrada', '<=', $data['fecha_hora_salida'])
            ->where(function ($query) use ($data) {
                $query->where('fecha_hora_salida', '>=', $data['fecha_hora_entrada'])
                    ->orWhereNull('fecha_hora_salida');
            })
            ->orderByDesc('fecha_hora_entrada')
            ->first();

        if($fichada) {
            throw new \Exception('Existe una fichada el '.$fichada->fecha_hora_entrada.' que hace incompatible el pedido');
        }

        $ultimaFichada = $this->fichadaRepository->create($data);

        return $ultimaFichada;
    }

    public function generarReporte($data)
    {
        return $this->fichadaRepository->generarReporte($data);
    }

}
