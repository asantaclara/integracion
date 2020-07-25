<?php

namespace App\Http\Controllers;

use App\Empleado;
use App\Horario_Laboral;
use App\Locacion;
use App\Services\HorarioLaboralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HorarioLaboralController extends Controller
{
    private $horarioLaboralService;

    public function __construct(HorarioLaboralService $horarioLaboralService)
    {
        $this->horarioLaboralService = $horarioLaboralService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Horario_Laboral  $horario_Laboral
     * @return \Illuminate\Http\Response
     */
    public function show(Horario_Laboral $horario_Laboral)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Horario_Laboral  $horario_Laboral
     * @return \Illuminate\Http\Response
     */
    public function edit(Horario_Laboral $horario_Laboral)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Horario_Laboral  $horario_Laboral
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Horario_Laboral $horario_Laboral)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Horario_Laboral  $horario_Laboral
     * @return \Illuminate\Http\Response
     */
    public function destroy(Horario_Laboral $horario_Laboral)
    {
        //
    }

    public function asignarHorarioLaboralAEmpleados(Request $request)
    {
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Cliente'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }
        $clienteId = $user->cliente->id;
        $locacionId = $request['locacion_id'];
        $validator = Validator::make($request->all(), [
            'locacion_id' => 'required|exists:locacion,id,cliente_id,'.$clienteId,
            'periodo'   => 'required|numeric|between:1,7',
            'empleados_id'   => 'required|array',
            'empleados_id.*' => 'exists:empleado_locacion,empleado_id,locacion_id,'.$locacionId,
            'hasta' => 'date|required|nullable|after_or_equal:desde',
            'desde' => 'date',
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try {
            $request = $request->all();
            unset($request['api_token']);
            if(count($request['horarios']) > 0) {
                $horarioLaboral = $this->horarioLaboralService->asignarHorariosLaborales($request);
            } else {
                throw new \Exception('No se han seleccionado horarios');
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'horarioLaboral' => $horarioLaboral],200);
    }

    public function horarioLaboralDeEmpleado(Request $request)
    {
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Cliente'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }
        $clienteId = $user->cliente->id;
        $locacionId = $request['locacion_id'];
        $validator = Validator::make($request->all(), [
            'locacion_id' => 'required|exists:locacion,id,cliente_id,'.$clienteId,
            'empleado_id' => 'required|exists:empleado_locacion,empleado_id,locacion_id,'.$locacionId,
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try {
            $request = $request->all();
            unset($request['api_token']);
            $horarios = $this->horarioLaboralService->horarioLaboralDeEmpleado($request->all());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'horarios_laborales' => $horarios],200);
    }
}
