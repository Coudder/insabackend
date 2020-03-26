<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Iluminate\Http\Response;

use App\Helpers\JwtAuth; ///cargamos el helper
use App\Helpers\JwtAuthu;

use App\Vph;
use App\Unidades;

class VphsController extends Controller
{
    public function __construct(){
        //aqui le decimos que use el middlewarte de auth en todos los metodos except los que nosotros no queramos
        //en este caso en el metodo index y show
        $this->middleware('api.auth', ['except' => ['index', 'show','getVphByUnidades']]);
        // $this->middleware('api.authu');
    }

    public function index()
    {
        $vphs = Vph::all() ->load('unidades');

        return response() -> json([
            'code' => 200,
            'status' => 'success',
            'vphs' => $vphs
        ]);
    }
    
    public function show($id)
    {
        $vph = Vph::find($id);

        if(is_object($vph) && !empty($vph))
        {
            $vph -> load('unidades');

            $data = [
                'code' => 200,
                'status' => 'success',
                'vphById' => $vph
            ];
        }else{
            $data = [
                'code' => 200,
                'status' => 'error',
                'message' => 'No existe Informacion '
            ];
        }

        return response() ->json($data, $data['code']);
    }

    public function store(Request $request)
    {
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
                'unidad_id'         => 'required|unique:vphs',
                'meta_vph'          => 'required|numeric',
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
                // 'total_vphs'        => 'numeric',
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
                $vph_info = new Vph();
                $vph_info -> unidad_id        = $params -> unidad_id;
                $vph_info -> meta_vph         = $params -> meta_vph;
                $vph_info -> ano              = $params -> ano;

                $vph_info -> save();

                $data = [
                    'code'             => 200,
                    'status'           => 'success',
                    'vphInfo'  => $vph_info
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
        $params_array = json_decode($json, true);

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
                 'meta_vph'          => 'numeric',
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
                 'total_vphs'        => 'numeric',
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
 
             $vph_info_update = Vph::updateOrCreate($where, $params_array);
 
             //devolvemos los datos correctos
             $data = array(
                 'code'        => 200,
                 'status'      => 'success',
                 'InfoVphUpdate' => $params_array
             );
         }

         return response() -> json($data, $data['code']);
    }

    public function destroy(Request $request, $id)
    {
        $vph_delete = Vph::where('id', $id) -> first();

        if(!empty($vph_delete))
        {
            $vph_delete -> delete();

            $data = [
                'code'      => 200,
                'status'    => 'success',
                'VphDelete' => $vph_delete
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ah podido eliminar la información solicitada'
            ];
        }

        return response() -> json($data, $data['code']);
    }

    public function getVphByUnidades($id)
    {
        $vphxunidad = Vph::where('unidad_id', $id) -> get() -> load('unidades');

        if(sizeof($vphxunidad))
        {
            $data = [
                'code' => 200,
                'status' => 'success',
                'vphByUnidad' => $vphxunidad
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
