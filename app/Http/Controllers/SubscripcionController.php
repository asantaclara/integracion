<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Services\SubscripcionService;
use App\Subscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubscripcionController extends Controller
{
    private $subscripcionService;

    public function __construct(SubscripcionService $subscripcionService)
    {
        $this->subscripcionService = $subscripcionService;
    }
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Administrador','Cliente'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }
        return $this->subscripcionService->all($user);
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
        if(Auth::guard('api')->user()->rol != 'Administrador') {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }

        $validator = Validator::make($request->all(), [
            'locacion_id' => 'required|exists:locacion,id',
            'servicio_id' => 'required|exists:servicio,id',
            'cliente_id' => 'required|exists:cliente,id|exists:locacion,id,cliente_id,'.$request['cliente_id'],
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        return 'ok';
        try {
            $request = $request->all();
            unset($request['api_token']);
            unset($request['id']);
            $cliente = $this->clienteService->create($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'cliente' => $cliente],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Subscripcion  $subscripcion
     * @return \Illuminate\Http\Response
     */
    public function show(Subscripcion $subscripcion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Subscripcion  $subscripcion
     * @return \Illuminate\Http\Response
     */
    public function edit(Subscripcion $subscripcion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subscripcion  $subscripcion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscripcion $subscripcion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subscripcion  $subscripcion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscripcion $subscripcion)
    {
        //
    }

    public function subscripcionesDeCliente(Cliente $cliente) {
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Administrador','Cliente'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }
        return $this->subscripcionService->subscripcionesDeCliente($cliente);
    }
}
