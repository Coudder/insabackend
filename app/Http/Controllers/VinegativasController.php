<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\JwtAuth;
//use App\Helpers\JwtAuthu;

use App\ViNegativas;
use App\Responsables;
use App\Coordinadores;
use App\Jurisdiccion;
use App\Unidades;

class VinegativasController extends Controller
{
    
    public function __construct()
    {
       
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $vinegativas = Vinegativas::all() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(!empty($vinegativas) && sizeof($vinegativas) )
        {
            $data = [
                 'code' => 200,
                 'status' => 'success',
                 'vinegativas' => $vinegativas
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
        $negativas = ViNegativas::find($id);

        if(is_object($negativas) && !empty($negativas))
        {
            $negativas -> load('unidades', 'responsables', 'coordinadores', 'jurisdiccion'); 
            
            $data = [
                'code'     => 200,
                'status'   => 'success',
                'negativasById' => $negativas
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
                'unidad_id'  => 'required|unique:vi_negativas',
                'responsable_id' => 'required',
                'coordinador_id' => 'required',
                'jurisdiccion_id' => 'required',
                'meta_negativas'  => 'required|numeric',
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
                $negativas = new Vinegativas();
                $negativas -> unidad_id = $params -> unidad_id;
                $negativas -> responsable_id = $params -> responsable_id;
                $negativas -> coordinador_id = $params -> coordinador_id;
                $negativas -> jurisdiccion_id = $params -> jurisdiccion_id;
                $negativas -> meta_negativas = $params -> meta_negativas;
                $negativas -> ano       = $params -> ano;

                $negativas -> save();

                $data = [
                    'code'             => 200,
                    'status'           => 'success',
                    'negativas' => $negativas
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
                'meta_negativas'  => 'numeric',
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
                'total_negativas' => 'numeric',
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

            $negativas_update = Vinegativas::updateOrCreate($where, $params_array);

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
        $negativas_del = Vinegativas::where('id', $id) -> first();

        if(!empty($negativas_del))
        {
            $negativas_del -> delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Registro eliminado correctamente',
                'negativas_del' => $negativas_del
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

    public function getnegativasByUnidades($id)
    {
        $negativasxunidad = Vinegativas::where('unidad_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($negativasxunidad))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'negativasxunidad' => $negativasxunidad
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

    public function getnegativasByResponsable($id)
    {
        $negativasxresponsable = Vinegativas::where('responsable_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($negativasxresponsable))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'negativasxresponsable' => $negativasxresponsable
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

    public function getnegativasByCoordinador($id)
    {
        $negativasxcoord = Vinegativas::where('coordinador_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($negativasxcoord))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'negativasxcoord' => $negativasxcoord
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

    public function getnegativasByJurisdiccion($id)
    {
        $negativasxjur = Vinegativas::where('jurisdiccion_id', $id) ->get() -> load('unidades','responsables','coordinadores','jurisdiccion');

        if(sizeof($negativasxjur))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'negativasxjur' => $negativasxjur
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
