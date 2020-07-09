<?php

namespace App\Http\Controllers;

use App\Empleado_Locacion;
use App\Services\EmpleadoLocacionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmpleadoLocacionController extends Controller
{
    private $empleadoLocacionService;

    public function __construct(EmpleadoLocacionService $empleadoLocacionService)
    {
        $this->empleadoLocacionService = $empleadoLocacionService;
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
        $validator = Validator::make($request->all(), [
            'empleado_id' => 'required|exists:empleado,id,cliente_id,'.$user->cliente->id,
            'locacion_id' => 'required|exists:locacion,id,cliente_id,'.$user->cliente->id,
            'fecha_vinculacion' => 'date',
            'fecha_desvinculacion' => 'date|after_or_equal:fecha_vinculacion'
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try{
            $request = $request->all();
            unset($request['api_token']);
            if(!isset($request['fecha_vinculacion'])) {
                $request['fecha_vinculacion'] = Carbon::now()->format('Y-m-d');
            }
            $empleadoLocacion = $this->empleadoLocacionService->asignarLocacion($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'empleadoLocacion' => $empleadoLocacion],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Empleado_Locacion  $empleado_Locacion
     * @return \Illuminate\Http\Response
     */
    public function show(Empleado_Locacion $empleado_Locacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Empleado_Locacion  $empleado_Locacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Empleado_Locacion $empleado_Locacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Empleado_Locacion  $empleado_Locacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Empleado_Locacion $empleado_Locacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Empleado_Locacion  $empleado_Locacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Empleado_Locacion $empleado_Locacion)
    {
        //
    }
}
