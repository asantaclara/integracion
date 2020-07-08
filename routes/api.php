<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/login', 'Auth\LoginController@authenticate');

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/logout', 'Auth\LoginController@logout');

    Route::resource('cliente', 'ClienteController');
    Route::resource('locacion', 'LocacionController');
    Route::resource('horario_laboral', 'HorarioLaboralController');

    Route::resource('empleado', 'EmpleadoController');
    Route::post('asignar-locacion-a-empleado', 'EmpleadoController@asignarLocacionAEmpleado');
    Route::post('asignar-horario-laboral-a-empleados', 'HorarioLaboralController@asignarHorarioLaboralAEmpleados');

    Route::get('cliente-test', 'ClienteController@test');
});
