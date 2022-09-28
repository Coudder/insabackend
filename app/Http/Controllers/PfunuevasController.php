<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\JwtAuth;
//use App\Helpers\JwtAuthu;

use App\Pfunuevas;
use App\Responsables;
use App\Coordinadores;
use App\Jurisdiccion;
use App\Unidades;

class PfunuevasController extends Controller
{
    public function __construct()
    {
       
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $pfunuevas = Pfunuevas::all() -> load('unidades', 'responsables', 'coordinadores', 'jurisdiccion');

       // var_dump($pfConSubs);
        

        if(!empty($pfunuevas) && sizeof($pfunuevas) )
        {
            $data = [
                 'code' => 200,
                 'status' => 'success',
                 'pfunuevas' => $pfunuevas
            ];        
        }else{

            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No existe información disponible'
            ];    
        }

        return response()->json($data, $data['code'] );
    }

    public function show($id)
    {
        $unuevas = Pfunuevas::find($id);

        if(is_object($unuevas) && !empty($unuevas))
        {
            $unuevas -> load('unidades', 'responsables', 'coordinadores', 'jurisdiccion'); 
            
            $data = [
                'code'     => 200,
                'status'   => 'success',
                'unuevasById' => $unuevas
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No existe información disponible'
            ];
        }

        return response() -> json($data, $data['code']);

       
    }

    public function store(Request $request)
    {
        $json         = $request->input('json', null);
        $params       = json_decode($json);
        $params_array = json_decode($json, true);


        if(!empty($params_array))
        {
            //si no van vacios obtenemos al usuario identificado
            $jwtAuth = new JwtAuth();
            $token   = $request -> header('Authorization', null);
            $user    = $jwtAuth -> checkToken($token, true);

            //validamos los datos enviados
            $validate = \Validator::make($params_array, [
                'unidad_id'  => 'required|unique:pf_usuarias_nuevas',
                'responsable_id' => 'required',
                'coordinador_id' => 'required',
                'jurisdiccion_id' => 'required',
                'meta_user_nuevas'  => 'required|numeric',
                'ano'        => 'numeric'
            ]);

            //comprobamos si los datos se validaros correctamente
            if($validate -> fails())
            {
                $data = [
                    'code'    => 400,
                    'status'  => 'error',
                    'message' => 'Los datos no se han validado correctamente'
                ];
            }else{
                //si la validacion es correcta guardamos en la bd la nueva inform de cito
                $unuevas = new Pfunuevas();
                $unuevas -> unidad_id = $params -> unidad_id;
                $unuevas -> responsable_id = $params -> responsable_id;
                $unuevas -> coordinador_id = $params -> coordinador_id;
                $unuevas -> jurisdiccion_id = $params -> jurisdiccion_id;
                $unuevas -> meta_user_nuevas = $params -> meta_user_nuevas;
                $unuevas -> ano       = $params -> ano;

                $unuevas -> save();

                $data = [
                    'code'             => 200,
                    'status'           => 'success',
                    'unuevas' => $unuevas
                ];
            }

        }else { //else  si no se reciben los datos correctamente
                $data =[
                    'code'    => 400,
                    'status'  => 'error',
                    'message' => 'No se ha recibido la información correctamente'
                ];
        }

        return response() -> json($data, $data['code']);
    }

    public function update(Request $request, $id) 
    {
       
        $json = $request -> input('json', null);
        $params_array =  json_decode($json, true);

        //Datos para devolver
        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'No se ah Actualizado la Información'
        );

        if(!empty($params_array))
        {
            //validamos datos
            $validate = \Validator::make($params_array, [
                // 'unidad_id'  => 'required',
                // 'responsable_id'  => 'required',
                // 'coordinador_id'  => 'required',
                'meta_user_nuevas'  => 'numeric',
                'enero'      => 'numeric',
                'febrero'    => 'numeric',
                'marzo'      => 'numeric',
                'abril'      => 'numeric',
                'mayo'       => 'numeric',
                'junio'      => 'numeric',
                'julio'      => 'numeric',
                'agosto'     => 'numeric',
                'septiembre' => 'numeric',
                'octubre'    => 'numeric',
                'noviembre'  => 'numeric',
                'diciembre'  => 'numeric',
                'total_unuevas' => 'numeric',
                'ano'        => 'numeric'
            ]);

            if($validate->fails())
            {
                $data['errors'] = $validate -> errors();
                return response() -> json($data, $data['code']);
            }

            //eliminamos los datos que no deseamos actualizar
            //unset($params_array['ano']); ejemplo

            $where = [
                'id' => $id,
            ];

            $unuevas_update = Pfunuevas::updateOrCreate($where, $params_array);

            //devolvemos los datos correctos
            $data = array(
                'code' => 200,
                'status' => 'success',
                'CHANGES' => $params_array
            );
        }

        return response() -> json($data, $data['code']);
    }

    public function destroy(Request $request, $id)
    {
        $unuevas_del = Pfunuevas::where('id', $id) -> first();

        if(!empty($unuevas_del))
        {
            $unuevas_del -> delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Registro eliminado correctamente',
                'unuevas_del' => $unuevas_del
            ];
        }else{

            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al eliminar la información solicitada'
            ];
        }
        
        return response()->json($data, $data['code']);
    }

    public function getunuevasByUnidades($id) 
    {
        $unuevasxunidad = Pfunuevas::where('unidad_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($unuevasxunidad))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'unuevasxunidad' => $unuevasxunidad
            ];
        }else{
            $data =[
                'code'    => 400,
                'status'  => 'error',
                'message' => 'No existe información disponible'
            ];
        }

        return response() -> json($data, $data['code']);
    }

    public function getunuevasByResponsable($id) 
    {
        $unuevasxresponsable = Pfunuevas::where('responsable_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($unuevasxresponsable))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'unuevasxresponsable' => $unuevasxresponsable
            ];
        }else{
            $data =[
                'code'    => 400,
                'status'  => 'error',
                'message' => 'No existe información disponible'
            ];
        }

        return response() -> json($data, $data['code']);
    }

    public function getunuevasByCoordinador($id) 
    {
        $unuevasxcoordinador = Pfunuevas::where('coordinador_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($unuevasxcoordinador))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'unuevasxcoordinador' => $unuevasxcoordinador
            ];
        }else{
            $data =[
                'code'    => 400,
                'status'  => 'error',
                'message' => 'No existe información disponible'
            ];
        }

        return response() -> json($data, $data['code']);
    }

    public function getunuevasByJurisdiccion($id) 
    {
        $unuevasxjurisdiccion = Pfunuevas::where('jurisdiccion_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($unuevasxjurisdiccion))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'unuevasxjurisdiccion' => $unuevasxjurisdiccion
            ];
        }else{
            $data =[
                'code'    => 400,
                'status'  => 'error',
                'message' => 'No existe información disponible'
            ];
        }

        return response() -> json($data, $data['code']);
    }
}
