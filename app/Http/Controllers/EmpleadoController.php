<?php

namespace App\Http\Controllers;

use App\Empleado;
use App\Locacion;
use App\Services\EmpleadoService;
use App\Services\LocacionService;
use Carbon\Carbon;
use Carbon\Exceptions\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmpleadoController extends Controller
{
    private $empleadoService;
    private $locacionService;

    public function __construct(EmpleadoService $empleadoService, LocacionService $locacionService)
    {
        $this->empleadoService = $empleadoService;
        $this->locacionService = $locacionService;
    }
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Cliente'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }
        return $this->empleadoService->all($user);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        if(Auth::guard('api')->user()->rol != 'Administrador') {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }
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
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Cliente'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'legajo' => 'required',
            'tipo_documento' => 'required|in:CUIT,CDI,LE,LC,CI Extranjera,Dni,Pasaporte,CI Policia Federal,Certificado de Migracion',
            'documento' => 'required|numeric|digits_between:7,8',
            'direccion' => 'required',
            'telefono' => 'numeric|required',
            'nacionalidad' => 'required|in:Argentina,Bolivia,Brasil,Chile,Paraguay,Uruguay,Otra',
            'genero' => 'required|in:Hombre,Mujer,Otro',
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try {
            $request = $request->all();
            unset($request['api_token']);
            unset($request['id']);
            unset($request['cliente_id']);
            $request['cliente_id'] = $user->cliente->id;
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
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Cliente'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }

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
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Cliente'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }

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
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Cliente'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }

        if($empleado->cliente_id != $user->cliente->id) {
            return response()->json(['error' => 'Forbidden', 'message' => 'El empleado no pertenece al cliente'],401);
        }

        $validator = Validator::make($request->all(), [
            'tipo_documento' => 'in:CUIT,CDI,LE,LC,CI Extranjera,Dni,Pasaporte,CI Policia Federal,Certificado de Migracion',
            'documento' => 'numeric',
            'telefono' => 'numeric',
            'nacionalidad' => 'in:Argentina,Bolivia,Brasil,Chile,Paraguay,Uruguay,Otra',
            'genero' => 'in:Hombre,Mujer,Otro',
            'cliente_id' => 'in:'.$user->cliente->id
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

    public function empleadosDeLocacion(Locacion $locacion)
    {
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Cliente'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }

        if($locacion->cliente != $user->cliente){
            return response()->json(['error' => 'Forbidden', 'message' => 'La locacion no pertenece al cliente'],401);
        }


        try {
            return $this->locacionService->empleadosDeLocacion($locacion);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
    }
}
