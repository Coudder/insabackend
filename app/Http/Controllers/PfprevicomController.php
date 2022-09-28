<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\JwtAuth;
//use App\Helpers\JwtAuthu;

use App\Pfprevicom;
use App\Responsables;
use App\Coordinadores;
use App\Jurisdiccion;
use App\Unidades;

class PfprevicomController extends Controller
{
    public function __construct()
    {
       
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $pfprevicom = Pfprevicom::all() -> load('unidades', 'responsables', 'coordinadores', 'jurisdiccion');

       // var_dump($pfConSubs);
        

        if(!empty($pfprevicom) && sizeof($pfprevicom) )
        {
            $data = [
                 'code' => 200,
                 'status' => 'success',
                 'pfprevicom' => $pfprevicom
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
        $previcom = Pfprevicom::find($id);

        if(is_object($previcom) && !empty($previcom))
        {
            $previcom -> load('unidades', 'responsables', 'coordinadores', 'jurisdiccion'); 
            
            $data = [
                'code'     => 200,
                'status'   => 'success',
                'previcomById' => $previcom
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
                'unidad_id'  => 'required|unique:pf_prev_comunidad',
                'responsable_id' => 'required',
                'coordinador_id' => 'required',
                'jurisdiccion_id' => 'required',
                'meta_comunidad'  => 'required|numeric',
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
                $previcom = new Pfprevicom();
                $previcom -> unidad_id = $params -> unidad_id;
                $previcom -> responsable_id = $params -> responsable_id;
                $previcom -> coordinador_id = $params -> coordinador_id;
                $previcom -> jurisdiccion_id = $params -> jurisdiccion_id;
                $previcom -> meta_comunidad = $params -> meta_comunidad;
                $previcom -> ano       = $params -> ano;

                $previcom -> save();

                $data = [
                    'code'             => 200,
                    'status'           => 'success',
                    'previcom' => $previcom
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
                'meta_comunidad'  => 'numeric',
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
                'total_comunidad' => 'numeric',
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

            $previcom_update = Pfprevicom::updateOrCreate($where, $params_array);

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
        $previcom_del = Pfprevicom::where('id', $id) -> first();

        if(!empty($previcom_del))
        {
            $previcom_del -> delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Registro eliminado correctamente',
                'previcom_del' => $previcom_del
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

    public function getprevicomByUnidades($id) 
    {
        $previcomxunidad = Pfprevicom::where('unidad_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($previcomxunidad))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'previcomxunidad' => $previcomxunidad
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

    public function getprevicomByResponsables($id) 
    {
        $previcomxresponsables = Pfprevicom::where('responsable_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($previcomxresponsables))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'previcomxresponsables' => $previcomxresponsables
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

    public function getprevicomByCoordinador($id) 
    {
        $previcomxcoordinador = Pfprevicom::where('coordinador_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($previcomxcoordinador))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'previcomxcoordinador' => $previcomxcoordinador
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

    public function getprevicomByJurisdiccion($id) 
    {
        $previcomxjurisdiccion = Pfprevicom::where('jurisdiccion_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($previcomxjurisdiccion))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'previcomxjurisdiccion' => $previcomxjurisdiccion
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
