<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Services\ClienteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     */
    private $clienteService;

    public function __construct(ClienteService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    public function index()
    {
        if(Auth::guard('api')->user()->rol != 'Administrador') {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }

        return $this->clienteService->all();
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
            'cuit_cuil',
            'tipo_categoria',
            'tipo_cliente',
            'forma_pago_habitual',
            'direccion',
            'nombre_razon_social',
            'email',
            'telefono'
        ];
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
            'cuit_cuil' => 'digits_between:10,11|required',
            'tipo_categoria' => 'required|in:IVA Responsable Inscripto,IVA Sujeto Excento,Consumidor Final,Responsable Monotributo',
            'tipo_cliente' => 'required|in:P. Fisica,P. Juridica',
            'forma_pago_habitual' => 'required|in:Contado,TC,TD,Cuentacorriente',
            'direccion' => 'required',
            'nombre_razon_social' => 'required',
            'email' => 'required|email',
            'telefono' => 'regex:/[\d\-\/]+/|required',
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
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
     * @param  \App\Cliente  $cliente
     */
    public function show(Cliente $cliente)
    {
        if(Auth::guard('api')->user()->rol != 'Administrador') {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }

        if($cliente) {
            return response()->json(['success' => 'success', 'cliente' => $cliente],200);
        } else {
            return response()->json(['error' => 'Forbidden', 'message' => 'client not found'],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cliente  $cliente
     */
    public function edit(Cliente $cliente)
    {
        if(Auth::guard('api')->user()->rol != 'Administrador') {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }

        return [
            'id',
            'cuit_cuil',
            'tipo_categoria',
            'tipo_cliente',
            'forma_pago_habitual',
            'direccion',
            'nombre_razon_social',
            'email',
            'telefono'
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cliente  $cliente
     */
    public function update(Request $request, Cliente $cliente)
    {
        if(Auth::guard('api')->user()->rol != 'Administrador') {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }

        $validator = Validator::make($request->all(), [
            'cuit_cuil' => 'digits_between:10,11',
            'tipo_categoria' => 'in:IVA Responsable Inscripto,IVA Sujeto Excento,Consumidor Final,Responsable Monotributo',
            'tipo_cliente' => 'in:P. Fisica,P. Juridica',
            'forma_pago_habitual' => 'in:Contado,TC,TD,Cuentacorriente',
            'email' => 'email',
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try{
            $request = $request->all();
            unset($request['api_token']);
            unset($request['id']);
           $cliente = $this->clienteService->update($cliente,$request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'cliente' => $cliente],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cliente  $cliente
     */
    public function destroy(Cliente $cliente)
    {
        if(Auth::guard('api')->user()->rol != 'Administrador') {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }
        try{
          $cliente = $this->clienteService->destroy($cliente);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'cliente' => $cliente],200);
    }
}
