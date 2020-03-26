<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Iluminate\Http\Response;

use App\Helpers\JwtAuth; ///cargamos el helper
use App\Helpers\JwtAuthU;

//cargamos los modelos
use App\Mastografia;
use App\Unidades;


class MastosController extends Controller
{
    public function __construct() //doble __
    {
        $this->middleware('api.auth', ['except' => ['index', 'show','getMastoByUnidades']]);
        // $this->middleware('api.authu');
    }

    public function index() //mostramos todos los registros de la tabla mastografias
    {
        $mastografias = Mastografia::all() -> load('unidades');

        return response() -> json([
            'code' => 200,
            'status' => 'success',
            'mastografias' => $mastografias 
        ]);
    }

    public function show($id) //mostraamos un registro de mastos por su id
    {
        $masto = Mastografia::find($id);

        if(is_object($masto) && !empty($masto))
        {
            $masto -> load('unidades');

            $data = [
                'code' => 200,
                'status' => 'success',
                'mastoById' => $masto
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No existe ningun registro con esa información' 
            ];
        }

        return response() -> json($data, $data['code']);
    }

    public function store(Request $request) //guardar nueva info en tabla detecciones
    { 
        //recogemos datos
        $json         = $request->input('json', null);
        $params       = json_decode($json);
        $params_array = json_decode($json, true);

        //usamos un if para verificar si los datos enviados no van vacios
        if(!empty($params_array))
        {
            //si no van vacios obtenemos al usuario identificado
            $jwtAuth = new JwtAuth();
            $token   = $request -> header('Authorization', null);
            $user    = $jwtAuth -> checkToken($token, true);

            //validamos los datos enviados
            $validate = \Validator::make($params_array, [
                'unidad_id'         => 'required|unique:mastografias',
                'meta_masto'  => 'required|numeric',
                // 'enero'             => 'numeric',
                // 'febrero'           => 'numeric',
                // 'marzo'             => 'numeric',
                // 'abril'             => 'numeric',
                // 'mayo'              => 'numeric',
                // 'junio'             => 'numeric',
                // 'julio'             => 'numeric',
                // 'agosto'            => 'numeric',
                // 'septiembre'        => 'numeric',
                // 'octubre'           => 'numeric',
                // 'noviembre'         => 'numeric',
                // 'diciembre'         => 'numeric',
                // 'total_mastos'      => 'numeric',
                'ano'               => 'numeric'
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
                $masto_info = new Mastografia();
                $masto_info -> unidad_id        = $params -> unidad_id;
                $masto_info -> meta_masto = $params -> meta_masto;
                $masto_info -> ano              = $params -> ano;

                $masto_info -> save();

                $data = [
                    'code'             => 200,
                    'status'           => 'success',
                    'mastoInfo'  => $masto_info
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

    public function update(Request $request,  $id) //Actualizar un registro
    {
        //recogemos los datos
        $json = $request -> input('json', null);
        $params_array = json_decode($json, true);

        //Datos para devolver
        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'No se ah Actualizado la Información'
        );

         //usamos un if para verificar si los datos enviados no van vacios
         if(!empty($params_array))
         {
             //validamos los datos enviados
             $validate = \Validator::make($params_array, [
                 'unidad_id'         => 'required',
                 'meta_masto'        => 'numeric',
                 'enero'             => 'numeric',
                 'febrero'           => 'numeric',
                 'marzo'             => 'numeric',
                 'abril'             => 'numeric',
                 'mayo'              => 'numeric',
                 'junio'             => 'numeric',
                 'julio'             => 'numeric',
                 'agosto'            => 'numeric',
                 'septiembre'        => 'numeric',
                 'octubre'           => 'numeric',
                 'noviembre'         => 'numeric',
                 'diciembre'         => 'numeric',
                 'total_mastos'      => 'numeric',
                 'ano'               => 'numeric'
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
 
             $masto_info_update = Mastografia::updateOrCreate($where, $params_array);
 
             //devolvemos los datos correctos
             $data = array(
                 'code'            => 200,
                 'status'          => 'success',
                 'InfoMastoUpdate' => $params_array
             );
         }

         return response() -> json($data, $data['code']);
 

    }

    public function destroy(Request $request, $id) //eliminar un registro
    {
        $masto_delete = Mastografia::where('id', $id) -> first();

        if(!empty($masto_delete))
        {
            $masto_delete -> delete();

            $data = [
                'code'        => 200,
                'status'      => 'success',
                'MastoDelete' => $masto_delete
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ah podido eliminar  la información solicitada'
            ];
        }
        return response() -> json($data, $data['code']);
    }

    public function getMastoByUnidades($id)// mostrar info x unidad sus avances de mastos
    {
        $mastoxunidad = Mastografia::where('unidad_id', $id) -> get() -> load('unidades');

        if(sizeof($mastoxunidad))
        {
            $data =[
                'code' => 200,
                'status' => 'success',
                'mastoxunidad' => $mastoxunidad
            ];
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'No existe información disponible'
            ];
        }
        return response() -> json($data, $data['code']);
    }
}

