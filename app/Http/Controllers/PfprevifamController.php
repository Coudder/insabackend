<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\JwtAuth;
//use App\Helpers\JwtAuthu;

use App\Pfprevifam;
use App\Responsables;
use App\Coordinadores;
use App\Jurisdiccion;
use App\Unidades;


class PfprevifamController extends Controller
{
    public function __construct()
    {
       
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $pfprevifam = Pfprevifam::all() -> load('unidades', 'responsables', 'coordinadores', 'jurisdiccion');

       // var_dump($pfConSubs);
        

        if(!empty($pfprevifam) && sizeof($pfprevifam) )
        {
            $data = [
                 'code' => 200,
                 'status' => 'success',
                 'pfprevifam' => $pfprevifam
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
        $previfam = Pfprevifam::find($id);

        if(is_object($previfam) && !empty($previfam))
        {
            $previfam -> load('unidades', 'responsables', 'coordinadores', 'jurisdiccion'); 
            
            $data = [
                'code'     => 200,
                'status'   => 'success',
                'previfamById' => $previfam
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
                'unidad_id'  => 'required|unique:pf_pre_vio_familiar',
                'responsable_id' => 'required',
                'coordinador_id' => 'required',
                'jurisdiccion_id' => 'required',
                'meta_familiar'  => 'required|numeric',
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
                $previfam = new Pfprevifam();
                $previfam -> unidad_id = $params -> unidad_id;
                $previfam -> responsable_id = $params -> responsable_id;
                $previfam -> coordinador_id = $params -> coordinador_id;
                $previfam -> jurisdiccion_id = $params -> jurisdiccion_id;
                $previfam -> meta_familiar = $params -> meta_familiar;
                $previfam -> ano       = $params -> ano;

                $previfam -> save();

                $data = [
                    'code'             => 200,
                    'status'           => 'success',
                    'previfam' => $previfam
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
                'meta_familiar'  => 'numeric',
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
                'total_familiar' => 'numeric',
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

            $previfam_update = Pfprevifam::updateOrCreate($where, $params_array);

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
        $previfam_del = Pfprevifam::where('id', $id) -> first();

        if(!empty($previfam_del))
        {
            $previfam_del -> delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Registro eliminado correctamente',
                'previfam_del' => $previfam_del
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

    public function getprevifamByUnidades($id) 
    {
        $previfamxunidad = Pfprevifam::where('unidad_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($previfamxunidad))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'previfamxunidad' => $previfamxunidad
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

    public function getprevifamByResponsables($id) 
    {
        $previfamxresponsable = Pfprevifam::where('responsable_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($previfamxresponsable))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'previfamxresponsable' => $previfamxresponsable
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

    public function getprevifamByCoordinador($id) 
    {
        $previfamxcoordinador = Pfprevifam::where('coordinador_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($previfamxcoordinador))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'previfamxcoordinador' => $previfamxcoordinador
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

    public function getprevifamByJurisdiccion($id) 
    {
        $previfamxjurisdiccion = Pfprevifam::where('jurisdiccion_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($previfamxjurisdiccion))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'previfamxjurisdiccion' => $previfamxjurisdiccion
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
