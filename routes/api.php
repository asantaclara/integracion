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
    Route::resource('fichada', 'FichadaController');
    Route::resource('empleado', 'EmpleadoController');
    Route::resource('servicio', 'ServicioController');
    Route::resource('subscripcion', 'SubscripcionController');
    Route::resource('pago', 'PagoController');

//    Empleados
    Route::get('empleado/locacion/{locacion}', 'EmpleadoController@empleadosDeLocacion');
    Route::get('empleados-de-fichador', 'EmpleadoController@empleadosDeFichador');

//    Asignaciones
    Route::post('asignar-locacion-a-empleado', 'EmpleadoLocacionController@store');
    Route::post('asignar-horario-laboral-a-empleados', 'HorarioLaboralController@asignarHorarioLaboralAEmpleados');

//    Subscripcion
    Route::get('subscripcion/cliente/{cliente}', 'SubscripcionController@subscripcionesDeCliente');

//    Locaciones
    Route::get('locacion/cliente/{cliente}', 'LocacionController@locacionesDeCliente');

//    Fichada
    Route::post('fichada/cliente', 'FichadaController@fichadasDeCliente');
    Route::post('fichada-manual', 'FichadaController@fichadaManual');
});
