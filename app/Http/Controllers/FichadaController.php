<?php

namespace App\Http\Controllers;

use App\Fichada;
use App\Services\FichadaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FichadaController extends Controller
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
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Fichador'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }
        $validator = Validator::make($request->all(), [
            'empleado_id' => 'required|exists:empleado,id,cliente_id,'.$user->cliente_id.'|exists:empleado_locacion,empleado_id,locacion_id,'.$user->locacion_id,
            'accion' => 'required|in:Entrada,Salida',
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try{
            $fichada = $this->fichadaService->create($request->only('empleado_id','accion'));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'fichada' => $fichada],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Fichada  $fichada
     * @return \Illuminate\Http\Response
     */
    public function show(Fichada $fichada)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Fichada  $fichada
     * @return \Illuminate\Http\Response
     */
    public function edit(Fichada $fichada)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Fichada  $fichada
     */
    public function update(Request $request, Fichada $fichada)
    {
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Cliente'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }
        $validator = Validator::make($request->all(), [
            'fecha_hora_entrada' => 'date|required|date_format:Y-m-d H:i',
            'fecha_hora_salida' => 'required|date|after:fecha_hora_entrada|date_format:Y-m-d H:i',
            'justificacion' => 'string|required'
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try{
            unset($request['api_token']);
            $fichada = $this->fichadaService->update($request->all(), $fichada);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'fichada' => $fichada],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Fichada  $fichada
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fichada $fichada)
    {

    }

    public function fichadasDeCliente(Request $request)
    {
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Cliente'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }
        $validator = Validator::make($request->all(), [
            'locacion_id' => 'exists:locacion,id,cliente_id,'.$user->cliente_id,
            'empleado_id' => 'exists:empleado,id,cliente_id,'.$user->cliente_id
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try{
            $fichada = $this->fichadaService->fichadasDeCliente($request->all());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'fichada' => $fichada],200);
    }

    public function fichadaManual(Request $request)
    {
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Cliente'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }
        $validator = Validator::make($request->all(), [
            'locacion_id' => 'exists:locacion,id,cliente_id,'.$user->cliente_id,
            'empleado_id' => 'required|exists:empleado,id,cliente_id,'.$user->cliente_id.'|exists:empleado_locacion,empleado_id,locacion_id,'.$request['locacion_id'],
            'fecha_hora_entrada' => 'date|required|date_format:Y-m-d H:i',
            'fecha_hora_salida' => 'required|date|after:fecha_hora_entrada|date_format:Y-m-d H:i',
            'justificacion' => 'string|required'
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try{
            $request = $request->all();
            unset($request['api_token']);
            $fichada = $this->fichadaService->crearFichadaManual($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'fichada' => $fichada],200);
    }
}
