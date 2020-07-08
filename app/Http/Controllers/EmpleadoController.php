<?php

namespace App\Http\Controllers;

use App\Empleado;
use App\Services\EmpleadoService;
use Carbon\Carbon;
use Carbon\Exceptions\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmpleadoController extends Controller
{
    private $empleadoService;

    public function __construct(EmpleadoService $empleadoService)
    {
        $this->empleadoService = $empleadoService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->empleadoService->all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return [
            'nombre',
            'legajo',
            'tipo_documento',
            'documento',
            'direccion',
            'telefono',
            'nacionalidad',
            'genero',
            'cliente_id'
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'legajo' => 'required',
            'tipo_documento' => 'required|in:CUIT,CDI,LE,LC,CI Extranjera,Dni,Pasaporte,CI Policia Federal,Certificado de Migracion',
            'documento' => 'required|numeric',
            'direccion' => 'required',
            'telefono' => 'regex:/[\d\-\/]+/|required',
            'nacionalidad' => 'required|in:Argentina,Bolivia,Brasil,Chile,Paraguay,Uruguay,Otra',
            'genero' => 'required|in:Hombre,Mujer,Otro',
            'cliente_id' => 'required|exists:cliente,id'
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try {
            $request = $request->all();
            unset($request['api_token']);
            unset($request['id']);
            $empleado = $this->empleadoService->create($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'empleado' => $empleado],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Empleado  $empleado
     */
    public function show(Empleado $empleado)
    {
        if($empleado) {
            return response()->json(['success' => 'success', 'empleado' => $empleado],200);
        } else {
        return response()->json(['error' => 'Forbidden', 'message' => 'client not found'],404);
    }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Empleado  $empleado
     */
    public function edit(Empleado $empleado)
    {
        return [
            'id',
            'nombre',
            'legajo',
            'tipo_documento',
            'documento',
            'direccion',
            'telefono',
            'nacionalidad',
            'genero',
            'cliente_id'
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Empleado  $empleado
     */
    public function update(Request $request, Empleado $empleado)
    {
        $validator = Validator::make($request->all(), [
            'tipo_documento' => 'in:CUIT,CDI,LE,LC,CI Extranjera,Dni,Pasaporte,CI Policia Federal,Certificado de Migracion',
            'documento' => 'numeric',
            'telefono' => 'regex:/[\d\-\/]+/',
            'nacionalidad' => 'in:Argentina,Bolivia,Brasil,Chile,Paraguay,Uruguay,Otra',
            'genero' => 'in:Hombre,Mujer,Otro',
            'cliente_id' => 'exists:cliente,id'
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try{
            $request = $request->all();
            unset($request['api_token']);
            unset($request['id']);
            $empleado = $this->empleadoService->update($empleado,$request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'empleado' => $empleado],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function destroy(Empleado $empleado)
    {
        //
    }

    public function asignarLocacionAEmpleado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empleado_id' => 'required|exists:empleado,id',
            'locacion_id' => 'required|exists:locacion,id',
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
                $request['fecha_vinculacion'] = Carbon::now();
            }
            $empleadoLocacion = $this->empleadoService->asignarLocacion($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'empleadoLocacion' => $empleadoLocacion],200);
    }

}
