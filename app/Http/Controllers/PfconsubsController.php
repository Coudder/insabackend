<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\JwtAuth;
//use App\Helpers\JwtAuthu;

use App\Pfconssubs;
use App\Responsables;
use App\Coordinadores;
use App\Jurisdiccion;
use App\Unidades;


class PfconsubsController extends Controller
{
    public function __construct()
    {
       
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $pfConSubs = Pfconssubs::all() -> load('unidades', 'responsables', 'coordinadores', 'jurisdiccion');

       // var_dump($pfConSubs);
        

        if(!empty($pfConSubs) )
        {
            $data = [
                 'code' => 200,
                 'status' => 'success',
                 'consubs' => $pfConSubs
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
        $consulta = Pfconssubs::find($id);

        if(is_object($consulta) && !empty($consulta))
        {
            $consulta -> load('unidades', 'responsables', 'coordinadores', 'jurisdiccion'); 
            
            $data = [
                'code'     => 200,
                'status'   => 'success',
                'consubsbyid' => $consulta
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No existe ninguna unidad con esa información'
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
                'unidad_id'  => 'required|unique:pf_consulta_subs',
                'responsable_id' => 'required',
                'coordinador_id' => 'required',
                'jurisdiccion_id' => 'required',
                'meta_subs'  => 'required|numeric',
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
                $consulta_sub = new Pfconssubs();
                $consulta_sub -> unidad_id = $params -> unidad_id;
                $consulta_sub -> responsable_id = $params -> responsable_id;
                $consulta_sub -> coordinador_id = $params -> coordinador_id;
                $consulta_sub -> jurisdiccion_id = $params -> jurisdiccion_id;
                $consulta_sub -> meta_subs = $params -> meta_subs;
                $consulta_sub -> ano       = $params -> ano;

                $consulta_sub -> save();

                $data = [
                    'code'             => 200,
                    'status'           => 'success',
                    'consulta_sub' => $consulta_sub
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
                'meta_subs'  => 'numeric',
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
                'total_subs' => 'numeric',
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

            $cons_update = Pfconssubs::updateOrCreate($where, $params_array);

            //devolvemos los datos correctos
            $data = array(
                'code' => 200,
                'status' => 'success',
                'changes' => $params_array
            );
        }

        return response() -> json($data, $data['code']);
    }

    public function destroy(Request $request, $id)
    {
        $consub_del = Pfconssubs::where('id', $id) -> first();

        if(!empty($consub_del))
        {
            $consub_del -> delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Registro eliminado correctamente',
                'consub_del' => $consub_del
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

    public function getConSubsByUnidades($id) 
    {
        $consubxunidad = Pfconssubs::where('unidad_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($consubxunidad))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'consubxunidad' => $consubxunidad
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

    public function getConsubsByResponsable($id)
    {
        $consubxresponsable = Pfconssubs::where('responsable_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($consubxresponsable))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'consubxresponsable' => $consubxresponsable
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

    public function getConsubsByCoordinador($id)
    {
        $consubxcoord = Pfconssubs::where('coordinador_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($consubxcoord))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'consubxcoord' => $consubxcoord
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

    public function getConsubsByJurisdiccion($id)
    {
        $consubxjur = Pfconssubs::where('jurisdiccion_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($consubxjur))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'consubxjur' => $consubxjur
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
