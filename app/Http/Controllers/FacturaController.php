<?php

namespace App\Http\Controllers;

use App\Factura;
use App\Fichada;
use App\Services\FacturaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FacturaController extends Controller
{
    private $facturaService;

    public function __construct(FacturaService $facturaService)
    {
        $this->facturaService = $facturaService;
    }
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $user = Auth::guard('api')->user();
        if(!in_array($user->rol,['Cliente','Administrador'])) {
            return response()->json(['error' => 'Forbidden', 'message' => 'No tiene permisos'],401);
        }
        return $this->facturaService->all($user);
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
            'cliente_id' => 'required|exists:cliente,id',
            'forma_pago' => 'required|in:Contado,TC,TD,Cuentacorriente',
            'fecha_hasta' => 'required|date|before_or_equal:'.Carbon::now()->format('Y-m-d')
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try {
            $request = $request->all();
            unset($request['api_token']);
            $cliente = $this->facturaService->create($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'cliente' => $cliente],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function show(Factura $factura)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function edit(Factura $factura)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Factura $factura)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function destroy(Factura $factura)
    {
        //
    }

    public function database(Request $request)
    {
//        $fecha_inicio_primera = Carbon::parse('2020-01-06');
//
//        while ($fecha_inicio_primera->isBefore(Carbon::parse('2020-04-01'))) {
//            $fecha_inicio = Carbon::parse($fecha_inicio_primera);
//            for($i=1 ; $i < 6 ; $i++) {
//                $fecha_actual_entrada = Carbon::parse($fecha_inicio)->addMinutes(480 + rand(-5, 5));
//                $fecha_actual_salida = Carbon::parse($fecha_inicio)->addMinutes(960 + rand(-5, 5));
//                Fichada::create([
//                    'empleado_id' => 8,
//                    'locacion_id' => 3,
//                    'fecha_hora_entrada' => $fecha_actual_entrada,
//                    'fecha_hora_salida' => $fecha_actual_salida,
//                    'minutos_trabajados' => $fecha_actual_salida->diffInMinutes(Carbon::parse($fecha_actual_entrada)),
//                ]);
//                $fecha_inicio->addDay();
//            }
//            $fecha_inicio_primera->addWeek();
//        }
    }
}
