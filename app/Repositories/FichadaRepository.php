<?php

namespace App\Repositories;


use App\Empleado;
use App\Feriado;
use App\Fichada;
use App\Horario_Laboral;
use Carbon\Carbon;
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

    public function generarReporte($data, $user = null)
    {
        $data['desde'] = Carbon::parse($data['desde'])->startOfDay();
        $data['hasta'] = Carbon::parse($data['hasta'])->endOfDay();

        // desde, hasta, array de empleados y locacion_id
        if(!$user) {
            $user = Auth::guard('api')->user();
        }

        $fichadas = Fichada::where('fecha_hora_entrada', '>=', $data['desde'])
            ->where('fecha_hora_salida', '<=', $data['hasta'])
            ->where('locacion_id', $data['locacion_id'])
            ->whereIn('empleado_id', $user->cliente->empleados->pluck('id'))
            ->whereIn('empleado_id', $data['empleados_id'])
            ->groupBy('empleado_id')
            ->selectRaw('empleado_id, sum(minutos_trabajados) as minutos_trabajados')
            ->get();

        $horariosLaborales = Horario_Laboral::join('empleado_locacion_horario_laboral as elhl', 'horario_laboral.id', 'elhl.horario_laboral_id')
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
            $emp = Empleado::find($hl->empleado_id);
            $diasNoTrabajo = collect(DB::select("select descripcion, count(*) as cant from feriado where empleado_id = ".$emp->id."
                                and fecha <= '".$data['hasta']."' and fecha >= '".$data['desde']."' and locacion_id = ".$data['locacion_id']." group by descripcion"));

            $empleado = [];
            $empleado['empleado_id'] = $hl->empleado_id;
            $empleado['cuit'] = $emp->documento;
            $empleado['feriados'] = count($diasNoTrabajo->where('descripcion','Feriado')) > 0 ? $diasNoTrabajo->where('descripcion','Feriado')->first()->cant : 0;
            $empleado['diasVacaciones'] = count($diasNoTrabajo->where('descripcion','Vacaciones')) > 0 ? $diasNoTrabajo->where('descripcion','Vacaciones')->first()->cant : 0;
            $empleado['diasEnfermedad'] = count($diasNoTrabajo->where('descripcion','Enfermedad')) > 0 ? $diasNoTrabajo->where('descripcion','Enfermedad')->first()->cant : 0;
            $empleado['cuit'] = $emp->documento;
            $empleado['nombre'] = $user->cliente->empleados->where('id',$hl->empleado_id)->first()->nombre;
            $empleado['horas_a_trabajar'] = $horariosLaborales ? $horariosLaborales->where('empleado_id', $empleado['empleado_id'])->first()['minutos_a_trabajar']/60 : 0;
            $empleado['dias_a_trabajar'] = $diasATrabajar ? $diasATrabajar->where('empleado_id', $empleado['empleado_id'])->first()->dias_a_trabajar : 0;
            $empleado['horas_trabajadas'] = count($fichadas->where('empleado_id', $empleado['empleado_id']))  ? floor($fichadas->where('empleado_id', $empleado['empleado_id'])->first()['minutos_trabajados']/60) : 0;
            $empleado['dias_trabajados'] = count($diasTrabajados->where('empleado_id', $empleado['empleado_id'])) ? $diasTrabajados->where('empleado_id', $empleado['empleado_id'])->first()->dias_trabajados : 0;
            $empleado['ausencias'] = $empleado['dias_a_trabajar'] - $empleado['dias_trabajados'];
            if($empleado['horas_trabajadas'] - ($empleado['horas_a_trabajar'] / $empleado['dias_a_trabajar']) * $empleado['dias_trabajados'] < 0) {
                $empleado['horas_extras'] = 0;
            } else {
                $empleado['horas_extras'] = floor($empleado['horas_trabajadas'] - ($empleado['horas_a_trabajar'] / $empleado['dias_a_trabajar']) * $empleado['dias_trabajados']);
            }
            array_push($result,$empleado);
        }
        return $result;
    }
}
