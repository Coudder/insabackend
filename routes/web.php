<?php

//cargando clases
use App\Http\Middleware\ApiAuthMiddleware;

//RUTAS DE PRUEBA
Route::get('/', function () {
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
//*************************FIN DE RUTAS CANCER Y UNIDADES */

    //*********RUTAS PARA EL MANEJO DE RESPONSABLES COORDINAODRES Y JURISDICCION */

    Route::resource('/api/responsables', 'RespController');
    Route::resource('/api/coordinadores', 'CoordController');
    Route::resource('/api/jurisdiccion', 'JurController');



    //*********RUTAS PARA PLANIFICACION FAMILIAR */    
    
    // ya listro en el front
    Route::resource('/api/pf/consubs', 'pfConsubsController'); //consulta subsecuente controlador
    Route::get('/api/pf/consubs/unidades/{id}', 'pfConsubsController@getConSubsByUnidades'); //by unidad
    Route::get('/api/pf/consubs/responsables/{id}', 'pfConsubsController@getConSubsByResponsable'); //by resp
    Route::get('/api/pf/consubs/coordinador/{id}', 'pfConsubsController@getConSubsByCoordinador'); //by coord
    Route::get('/api/pf/consubs/jurisdiccion/{id}', 'pfConsubsController@getConSubsByJurisdiccion');//by jur


        //ya listo en el front
    Route::resource('/api/pf/firstconsulta', 'pffirstconsultaController'); //consulta primera vez controlador
    Route::get('/api/pf/firstconsulta/unidades/{id}', 'pffirstconsultaController@getConfirstByUnidades'); //by unidad
    Route::get('/api/pf/firstconsulta/responsables/{id}', 'pffirstconsultaController@getConfirstByResponsables');
    Route::get('/api/pf/firstconsulta/coordinador/{id}', 'pffirstconsultaController@getConfirstByCoordinador');
    Route::get('/api/pf/firstconsulta/jurisdiccion/{id}', 'pffirstconsultaController@getConfirstByJurisdiccion');


    //rutas  grupos adolescentes promotores de la salud grupoSaludController
    Route::resource('/api/pf/gruposalud', 'pfgruposaludController');
    Route::get('/api/pf/gruposalud/unidades/{id}', 'pfgruposaludController@getgrupoByUnidades');
    Route::get('/api/pf/gruposalud/responsables/{id}', 'pfgruposaludController@getgrupoByResponsables');
    Route::get('/api/pf/gruposalud/coordinador/{id}', 'pfgruposaludController@getgrupoByCoordinador');
    Route::get('/api/pf/gruposalud/jurisdiccion/{id}', 'pfgruposaludController@getgrupoByJurisdiccion');


    //rutas para otb controlador PfotbController 
    Route::resource('/api/pf/otb', 'PfotbController');
    Route::get('/api/pf/otb/unidades/{id}', 'PfotbController@getotbByUnidades');
    Route::get('/api/pf/otb/responsables/{id}', 'PfotbController@getotbByResponsables');
    Route::get('/api/pf/otb/coordinador/{id}', 'PfotbController@getotbByCoordinador');
    Route::get('/api/pf/otb/jurisdiccion/{id}', 'PfotbController@getotbByJurisdiccion');


    //controlador prevencion vioelncia a la comunidad intervenciones
    Route::resource('/api/pf/previcom', 'PfprevicomController');
    Route::get('/api/pf/previcom/unidades/{id}', 'PfprevicomController@getprevicomByUnidades');
    Route::get('/api/pf/previcom/responsables/{id}', 'PfprevicomController@getprevicomByResponsables');
    Route::get('/api/pf/previcom/coordinador/{id}', 'PfprevicomController@getprevicomByCoordinador');
    Route::get('/api/pf/previcom/jurisdiccion/{id}', 'PfprevicomController@getprevicomByJurisdiccion');


    //controlador prevencion vioelncia a la familia intervenciones
    Route::resource('/api/pf/previfam', 'PfprevifamController');
    Route::get('/api/pf/previfam/unidades/{id}', 'PfprevifamController@getprevifamByUnidades');
    Route::get('/api/pf/previfam/responsables/{id}', 'PfprevifamController@getprevifamByResponsables');
    Route::get('/api/pf/previfam/coordinador/{id}', 'PfprevifamController@getprevifamByCoordinador');
    Route::get('/api/pf/previfam/jurisdiccion/{id}', 'PfprevifamController@getprevifamByJurisdiccion');


    //controlador sesiones a adolescentes 
    Route::resource('/api/pf/sesadol', 'PfsesadolController');
    Route::get('/api/pf/sesadol/unidades/{id}', 'PfsesadolController@getsesadolByUnidades');
    Route::get('/api/pf/sesadol/responsables/{id}', 'PfsesadolController@getsesadolByresponsables');
    Route::get('/api/pf/sesadol/coordinadores/{id}', 'PfsesadolController@getsesadolByCoordinador');
    Route::get('/api/pf/sesadol/jurisdiccion/{id}', 'PfsesadolController@getsesadolByJurisdiccion');


    //controlador sesiones a padres de familia
    Route::resource('/api/pf/sespadres', 'PfsespadresController');
    Route::get('/api/pf/sespadres/unidades/{id}', 'PfsespadresController@getsespadresByUnidades');
    Route::get('/api/pf/sespadres/responsable/{id}', 'PfsespadresController@getsespadresByResponsable');
    Route::get('/api/pf/sespadres/coordinador/{id}', 'PfsespadresController@getsespadresByCoordinador');
    Route::get('/api/pf/sespadres/jurisdiccion/{id}', 'PfsespadresController@getsespadresByJurisdiccion');


    //controlador usuarioas nuevas  ya listo en el front
    Route::resource('/api/pf/unuevas', 'PfunuevasController');
    Route::get('/api/pf/unuevas/unidades/{id}', 'PfunuevasController@getunuevasByUnidades');
    Route::get('/api/pf/unuevas/responsable/{id}', 'PfunuevasController@getunuevasByResponsable');
    Route::get('/api/pf/unuevas/coordinador/{id}', 'PfunuevasController@getunuevasByCoordinador');
    Route::get('/api/pf/unuevas/jurisdiccion/{id}', 'PfunuevasController@getunuevasByJurisdiccion');



    //controlador Vasectomia  controlador
    Route::resource('/api/pf/vsb', 'PfvsbController');
    Route::get('/api/pf/vsb/unidades/{id}', 'PfvsbController@getvsbByUnidades');
    Route::get('/api/pf/vsb/responsable/{id}', 'PfvsbController@getvsbByResponsable');
    Route::get('/api/pf/vsb/coordinador/{id}', 'PfvsbController@getvsbByCoordinador');
    Route::get('/api/pf/vsb/jurisdiccion/{id}', 'PfvsbController@getvsbByJurisdiccion');


        //*********RUTAS PARA PLANIFICACION FAMILIAR */    

        //RUTAS PARA VIOLENCIA FAMILIAR//
    Route::resource('/api/violencia/aplicadas', 'ViaplicadasController');
    Route::get('/api/violencia/aplicadas/unidad/{id}', 'ViaplicadasController@getaplicadasByUnidades');
    Route::get('/api/violencia/aplicadas/responsable/{id}', 'ViaplicadasController@getaplicadasByResponsable');
    Route::get('/api/violencia/aplicadas/coordinador/{id}', 'ViaplicadasController@getaplicadasByCoordinador');
    Route::get('/api/violencia/aplicadas/jurisdiccion/{id}', 'ViaplicadasController@getaplicadasByJurisdiccion');

    Route::resource('/api/violencia/negativas', 'VinegativasController');
    Route::get('/api/violencia/negativas/unidad/{id}', 'VinegativasController@getnegativasByUnidades');
    Route::get('/api/violencia/negativas/responsable/{id}', 'VinegativasController@getnegativasByResponsable');
    Route::get('/api/violencia/negativas/coordinador/{id}', 'VinegativasController@getnegativasByCoordinador');
    Route::get('/api/violencia/negativas/jurisdiccion/{id}', 'VinegativasController@getnegativasByJurisdiccion');

    Route::resource('/api/violencia/positivas', 'VipositivasController');
    Route::get('/api/violencia/positivas/unidad/{id}', 'VipositivasController@getpositivasByUnidades');
    Route::get('/api/violencia/positivas/responsable/{id}', 'VipositivasController@getpositivasByResponsable');
    Route::get('/api/violencia/positivas/coordinador/{id}', 'VipositivasController@getpositivasByCoordinador');
    Route::get('/api/violencia/positivas/jurisdiccion/{id}', 'VipositivasController@getpositivasByJurisdiccion');

        //RUTAS PARA VIOLENCIA FAMILIAR//

        //**RUTAS PARA MORTALIDAD MATERNA */
    Route::resource('/api/materna/puerperio', 'MapuerperioController');
    Route::get('/api/materna/puerperio/unidad/{id}', 'MapuerperioController@getpuerperioByUnidades');
    Route::get('/api/materna/puerperio/responsable/{id}', 'MapuerperioController@getpuerperioByResponsable');
    Route::get('/api/materna/puerperio/coordinador/{id}', 'MapuerperioController@getpuerperioByCoordinador');
    Route::get('/api/materna/puerperio/jurisdiccion/{id}', 'MapuerperioController@getpuerperioByJurisdiccion');

    Route::resource('/api/materna/trimestre', 'MatrimestreController');
    Route::get('/api/materna/trimestre/unidad/{id}', 'MatrimestreController@gettrimestreByUnidades');
    Route::get('/api/materna/trimestre/responsable/{id}', 'MatrimestreController@gettrimestreByResponsable');
    Route::get('/api/materna/trimestre/coordinador/{id}', 'MatrimestreController@gettrimestreByCoordinador');
    Route::get('/api/materna/trimestre/jurisdiccion/{id}', 'MatrimestreController@gettrimestreByJurisdiccion');


    //siendo 18/01/2021 llevamos los programas en el backend de 
    //CANCER DE LA MUJER FALTA SOLTAR LA INFROMACION POR COORDINADORES ETC
    //PLAINIFACION FAMILIAR
    //VIOLENCIA
    //MORATLIDAD MATERNA




        






    