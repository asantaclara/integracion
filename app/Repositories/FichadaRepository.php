<?php

namespace App\Repositories;


use App\Feriado;
use App\Fichada;
use App\Horario_Laboral;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function generarReporte($data)
    {
        // desde, hasta, array de empleados y locacion_id
        $user = Auth::guard('api')->user();

        $fichadas = Fichada::where('fecha_hora_entrada', '>=', $data['desde'])
            ->where('fecha_hora_salida', '<=', $data['hasta'])
            ->where('locacion_id', $data['locacion_id'])
            ->whereIn('empleado_id', $user->cliente->empleados->pluck('id'))
            ->whereIn('empleado_id', $data['empleados_id'])
            ->groupBy('empleado_id')
            ->selectRaw('empleado_id, sum(minutos_trabajados) as minutos_trabajados')
            ->get();

        $horariosLaborales = Horario_Laboral::join('empleado_locacion_horario_laboral as elhl', 'horario_laboral.id', 'elhl.horario_laboral_id' )
            ->join('empleado_locacion as el', 'el.id', 'elhl.empleado_locacion_id')
            ->where('horario_laboral.fecha_desde', '>=', $data['desde'])
            ->where('horario_laboral.fecha_hasta', '<=', $data['hasta'])
            ->where('locacion_id', $data['locacion_id'])
            ->whereIn('el.empleado_id', $user->cliente->empleados->pluck('id'))
            ->whereIn('el.empleado_id', $data['empleados_id'])
            ->groupBy('el.empleado_id')
            ->selectRaw('empleado_id, sum(duracion_minutos) as minutos_a_trabajar')
            ->get();

        $diasATrabajar = collect(DB::select("select x.empleado_id, count(*) as dias_a_trabajar
            from (select DISTINCT el.empleado_id, DAYOFYEAR(fecha_desde) as dia_del_ano, YEAR(fecha_desde) as ano from horario_laboral hl
            join empleado_locacion_horario_laboral elhl on elhl.horario_laboral_id = hl.id
            join empleado_locacion el on elhl.empleado_locacion_id = el.id
            where locacion_id = ".$data['locacion_id']." and hl.fecha_desde >= '".$data['desde']."' and hl.fecha_hasta <= '".$data['hasta']."') as x group by x.empleado_id "));

        $diasTrabajados = collect(DB::select("select x.empleado_id, count(*) as dias_trabajados
            from (select DISTINCT empleado_id, DAYOFYEAR(fecha_hora_entrada) as dia_del_ano, year(fecha_hora_entrada) as ano from fichada f
            where locacion_id = ".$data['locacion_id']." and fecha_hora_entrada >= '".$data['desde']."' and fecha_hora_salida <= '".$data['hasta']."') as x group by x.empleado_id"));

        $result = [];
        foreach ($horariosLaborales as $hl) {
            $empleado = [];
            $empleado['empleado_id'] = $hl->empleado_id;
            $empleado['horas_a_trabajar'] = $horariosLaborales->where('empleado_id', $empleado['empleado_id'])->first()['minutos_a_trabajar']/60;
            $empleado['dias_a_trabajar'] = $diasATrabajar->where('empleado_id', $empleado['empleado_id'])->first()->dias_a_trabajar;
            $empleado['horas_trabajadas'] = $fichadas->where('empleado_id', $empleado['empleado_id'])->first()['minutos_trabajados']/60;
            $empleado['dias_trabajados'] = $diasTrabajados->where('empleado_id', $empleado['empleado_id'])->first()->dias_trabajados;
            $empleado['ausencias'] = $empleado['dias_a_trabajar'] - $empleado['dias_trabajados'];
            $empleado['horas_extras'] = $empleado['horas_trabajadas'] - ($empleado['horas_a_trabajar'] / $empleado['dias_a_trabajar']) * $empleado['dias_trabajados'];
            array_push($result,$empleado);
        }
        return [$result];
    }
}
