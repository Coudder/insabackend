<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;


use App\Helpers\JwtAuth;
//use App\Helpers\JwtAuthu;

use App\Pffirstconsulta;
use App\Responsables;
use App\Coordinadores;
use App\Jurisdiccion;
use App\Unidades;

class PffirstconsultaController extends Controller
{
    public function __construct()
    {
       
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $pfconsulta = Pffirstconsulta::all() -> load('unidades', 'responsables', 'coordinadores', 'jurisdiccion');

       // var_dump($pfConSubs);
        

        if(!empty($pfconsulta) && sizeof($pfconsulta) )
        {
            $data = [
                 'code' => 200,
                 'status' => 'success',
                 'primerconsulta' => $pfconsulta
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
        $consulta = Pffirstconsulta::find($id);

        if(is_object($consulta) && !empty($consulta))
        {
            $consulta -> load('unidades', 'responsables', 'coordinadores', 'jurisdiccion'); 
            
            $data = [
                'code'     => 200,
                'status'   => 'success',
                'firstconsulta' => $consulta
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
                'unidad_id'  => 'required|unique:pf_primera_consulta',
                'responsable_id' => 'required',
                'coordinador_id' => 'required',
                'jurisdiccion_id' => 'required',
                'meta_primeravez'  => 'required|numeric',
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
                $consulta_first = new Pffirstconsulta();
                $consulta_first -> unidad_id = $params -> unidad_id;
                $consulta_first -> responsable_id = $params -> responsable_id;
                $consulta_first -> coordinador_id = $params -> coordinador_id;
                $consulta_first -> jurisdiccion_id = $params -> jurisdiccion_id;
                $consulta_first -> meta_primeravez = $params -> meta_primeravez;
                $consulta_first -> ano       = $params -> ano;

                $consulta_first -> save();

                $data = [
                    'code'             => 200,
                    'status'           => 'success',
                    'consulta_first' => $consulta_first
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
                'meta_primeravez'  => 'numeric',
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
                'total_primeravez' => 'numeric',
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

            $cons_update = Pffirstconsulta::updateOrCreate($where, $params_array);

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
        $consulta_del = Pffirstconsulta::where('id', $id) -> first();

        if(!empty($consulta_del))
        {
            $consulta_del -> delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Registro eliminado correctamente',
                'consulta_del' => $consulta_del
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

    public function getConfirstByUnidades($id) 
    {
        $consxunidad = Pffirstconsulta::where('unidad_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($consxunidad))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'consxunidad' => $consxunidad
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

    public function getConfirstByResponsables($id) 
    {
        $consxresponsable = Pffirstconsulta::where('responsable_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($consxresponsable))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'consxresponsable' => $consxresponsable
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

    public function getConfirstByCoordinador($id) 
    {
        $consxcoord = Pffirstconsulta::where('coordinador_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($consxcoord))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'consxcoordinador' => $consxcoord
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

    public function getConfirstByJurisdiccion($id) 
    {
        $consxjurisdiccion = Pffirstconsulta::where('coordinador_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($consxjurisdiccion))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'consxjurisdiccion' => $consxjurisdiccion
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
