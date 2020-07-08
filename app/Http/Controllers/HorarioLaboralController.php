<?php

namespace App\Http\Controllers;

use App\Empleado;
use App\Horario_Laboral;
use App\Locacion;
use App\Services\HorarioLaboralService;
use Illuminate\Http\Request;
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
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|exists:cliente,id'
            ]);
        if($validator->validate()){
            $clienteId = $request['cliente_id'];
            $validator = Validator::make($request->all(), [
                'locacion_id' => 'required|exists:locacion,id,cliente_id,'.$clienteId,
                'empleados_id'   => 'required|array',
                'empleados_id.*' => 'exists:empleado,id,cliente_id,'.$clienteId,
                //TODO seguir cargando las validaciones, faltan las horas y despues ver como se asignan los horarios.
            ]);
        }
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try {
            $request = $request->all();
            unset($request['api_token']);
            $horarioLaboral = $this->horarioLaboralService->asignarHorariosLaborales($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'horarioLaboral' => $horarioLaboral],200);
    }
}
