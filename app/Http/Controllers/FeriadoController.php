<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Feriado;
use App\Services\FichadaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FeriadoController extends Controller
{
    private $fichadaService;

    public function __construct(FichadaService $fichadaService)
    {
        $this->fichadaService = $fichadaService;
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
     */
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Cliente'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }
        $clienteId = $user->cliente->id;
        $locacionId = $request['locacion_id'];
        $validator = Validator::make($request->all(), [
            'locacion_id' => 'required|exists:locacion,id,cliente_id,'.$clienteId,
            'empleados_id'   => 'required|array',
            'empleados_id.*' => 'exists:empleado_locacion,empleado_id,locacion_id,'.$locacionId,
            'fecha_desde' => 'date|required',
            'fecha_hasta' => 'date|required|after_or_equal:desde',
            'descripcion' => 'required|in:Feriado,Enfermedad,Vacaciones',
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try {
            $request = $request->all();
            unset($request['api_token']);
            $feriados = $this->fichadaService->crearFeriados($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'feriados' => $feriados],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Feriado  $feriado
     * @return \Illuminate\Http\Response
     */
    public function show(Feriado $feriado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Feriado  $feriado
     * @return \Illuminate\Http\Response
     */
    public function edit(Feriado $feriado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feriado  $feriado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feriado $feriado)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Feriado  $feriado
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feriado $feriado)
    {
        //
    }
}
