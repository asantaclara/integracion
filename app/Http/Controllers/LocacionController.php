<?php

namespace App\Http\Controllers;

use App\Locacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        return Locacion::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        return [
            'cliente_id',
            'direccion',
            'descripcion'
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
            'cliente_id' => 'required|exists:cliente,id',
            'direccion' => 'required',
            'descripcion' => 'required'
        ]);
        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try {
            $request = $request->all();
            unset($request['api_token']);
            $locacion = new Locacion($request);
            $locacion->save();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'cliente' => $locacion],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Locacion  $locacion
     */
    public function show(Locacion $locacion)
    {
        if($locacion) {
            return response()->json(['success' => 'success', 'cliente' => $locacion],200);
        } else {
            return response()->json(['error' => 'Forbidden', 'message' => 'location not found'],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Locacion  $locacion
     */
    public function edit(Locacion $locacion)
    {
        return [
            'cliente_id',
            'direccion',
            'descripcion'
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Locacion  $locacion
     */
    public function update(Request $request, Locacion $locacion)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'exists:cliente',
        ]);

        if($validator->fails()){
            return response()->json(['error' => 'Forbidden', 'errors' => $validator->errors()],406);
        }
        try{
            $request = $request->all();
            unset($request['api_token']);
            $locacion->update($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Forbidden', 'message' => $e->getMessage()],406);
        }
        return response()->json(['success' => 'success', 'locacion' => $locacion],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Locacion  $locacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Locacion $locacion)
    {
        //
    }
}
