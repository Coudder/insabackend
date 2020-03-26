<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//cargando clases
use App\Http\Middleware\ApiAuthMiddleware;

//RUTAS DE PRUEBA
Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/test-orm', 'PruebasController@testOrm');
Route::get('/test-cito', 'PruebasController@testOrmCitos');

//RUTAS DE API

    //rutas de prueba
   // Route::get('/admin/prueba',       'AdminController@pruebas');
    //Route::get('/unidades/prueba',    'UnidadesController@pruebas');
    //Route::get('/citologias/prueba',  'CitosController@pruebas');
    //Route::get('/detecciones/prueba', 'DeteccionesController@pruebas');
    //Route::get('/mastos/prueba',      'MastosController@pruebas');
    //Route::get('/vphs/prueba',        'VphsController@pruebas');

    //RUTAS OFICIALES CONTROLADOR USUARIO

Route::post('/api/register', 'AdminController@register' );
Route::post('/api/login',    'AdminController@login' );
Route::put('/api/user/update',    'AdminController@update' );
Route::post('/api/user/upload','AdminController@upload')->middleware(ApiAuthMiddleware::class); 
Route::get('/api/user/avatar/{filename}','AdminController@getImage'); 
Route::get('/api/user/profile/{id}','AdminController@profile');

    //RUTAS OFICIALES CONTROLADOR UNIDADES tipo resource

    
    Route::resource('/api/unidades', 'UnidadesController'); 
    Route::post('/api/loginunidad',    'UnidadesController@loginUnidad' );
    
    Route::resource('/api/citologias', 'CitosController');
    Route::get('/api/citologias/unidades/{id}', 'CitosController@getCitosByUnidades' );
   
    Route::resource('/api/detecciones', 'DeteccionesController');
    Route::get('/api/detecciones/unidades/{id}', 'DeteccionesController@getDetByUnidades' );


    Route::resource('/api/mastografias', 'MastosController');
    Route::get('/api/mastografias/unidades/{id}', 'MastosController@getMastoByUnidades' );

    Route::resource('/api/vphs', 'VphsController');
    Route::get('/api/vphs/unidades/{id}', 'VphsController@getVphByUnidades');


    