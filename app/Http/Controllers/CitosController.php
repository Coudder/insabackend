<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\JwtAuth;
use App\Helpers\JwtAuthu; //cargamos el helper para poder ubicar al usuario identificado

use App\Citologia;
use App\Unidades;

class CitosController extends Controller
{
    public function __construct(){
        //aqui le decimos que use el middlewarte de auth en todos los metodos except los que nosotros no queramos
        //en este caso en el metodo index y show
        $this->middleware('api.auth', ['except' => ['index', 'show', 'getCitosByUnidades']]);
        // $this->middleware('api.authu');
    }

    public function index() //metodo para mostrar todos los datos de la tabla citologias con sus unidades
    {
        $citologias = Citologia::all() -> load('unidades');

        return response()-> json([
            'code'       => 200,
            'status'     => 'success',
            'citologias' => $citologias
        ]);
    }

    public function show($id) //metodo para mostrar solo una unidad con sus metas y avances de citologias
    {
        $cito = Citologia::find($id);

        if(is_object($cito) && !empty($cito))
        {
            $cito -> load('unidades'); //si lleva datos tmb cargamos la unidad correspondiente a ese id de citologia 
            
            $data = [
                'code'     => 200,
                'status'   => 'success',
                'citoById' => $cito
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
    
    public function store(Request $request) //metodo para guardar una nueva meta y avances si asi se quiere
    {
        //regogemos datos por post
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
                'unidad_id'  => 'required|unique:citologias',
                'meta_cito'  => 'required|numeric',
                // 'enero'      => 'numeric',
                // 'febrero'    => 'numeric',
                // 'marzo'      => 'numeric',
                // 'abril'      => 'numeric',
                // 'mayo'       => 'numeric',
                // 'junio'      => 'numeric',
                // 'julio'      => 'numeric',
                // 'agosto'     => 'numeric',
                // 'septiembre' => 'numeric',
                // 'octubre'    => 'numeric',
                // 'noviembre'  => 'numeric',
                // 'diciembre'  => 'numeric',
                // 'total_cito' => 'numeric',
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
                $cito_info = new Citologia();
                $cito_info -> unidad_id = $params -> unidad_id;
                $cito_info -> meta_cito = $params -> meta_cito;
                $cito_info -> ano       = $params -> ano;

                $cito_info -> save();

                $data = [
                    'code'             => 200,
                    'status'           => 'success',
                    'citoInfo' => $cito_info
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

    public function update(Request $request, $id) //metodo para actualizar info de citologias
    {
        //recogemos los datos por post
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
                'unidad_id'  => 'required',
                'meta_cito'  => 'numeric',
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
                'total_cito' => 'numeric',
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

            $Cito_Info_update = Citologia::updateOrCreate($where, $params_array);

            //devolvemos los datos correctos
            $data = array(
                'code' => 200,
                'status' => 'success',
                'CHANGES' => $params_array
            );
        }

        return response() -> json($data, $data['code']);
    }

    public function destroy(Request $request, $id) //metodo para eliminar info de cito
    {
        //obtenemos el registro a eliminar
        $cito_delete = Citologia::where('id',$id) -> first();

        if(!empty($cito_delete))
        {
            //si se envia bien el id borramos el registro
            $cito_delete -> delete();

            $data = [
                'code'        => 200,
                'status'      => 'success',
                'CITO_DELETE' => $cito_delete
            ];
        }else{
            $data = [
                'code'    => 400,
                'status'  => 'error',
                'message' => 'No se ah podido eliminar la información solicitada'
            ];
        }

        return response() -> json($data, $data['code']);
    }

    public function getCitosByUnidades($id) //metodo para obtener por ubnidad sus citologias
    {
        $citosxunidad = Citologia::where('unidad_id', $id) -> get() -> load('unidades');

        if(sizeof($citosxunidad))
        {
            $data =[
                'code' => 200,
                'status' => 'success',
                'infoxunidad' => $citosxunidad
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
