<?php

namespace App\Repositories;


use App\Fichada;
use Illuminate\Support\Facades\Auth;

class FichadaRepository
{
    public function all()
    {
        return Fichada::all();
    }

    public function create($data)
    {
        unset($data['accion']);
        $data['fecha_hora_entrada'] = $data['fecha_hora'] ?? $data['fecha_hora_entrada'];
        unset($data['fecha_hora']);
        return Fichada::create($data);
    }

    public function update(Fichada $fichada, $data)
    {
        $fichada->update($data);
        return $fichada;
    }

    public function fichadasDeCliente($data)
    {
        $user = Auth::guard('api')->user();

        $fichadas = Fichada::select('*');

        if(isset($data['empleado_id']) && $data['empleado_id'] != '') {
            $fichadas->where('empleado_id', $data['empleado_id']);
        }

        if(isset($data['locacion_id']) && $data['locacion_id'] != '') {
            $fichadas->where('locacion_id', $data['locacion_id']);
        }
        return $fichadas
            ->whereIn('empleado_id', $user->cliente->empleados->pluck('id'))
            ->with('locacion')
            ->orderBy('fecha_hora_entrada')
            ->get();
    }
}
