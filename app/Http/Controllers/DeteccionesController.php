<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Iluminate\Http\Response;

use App\Helpers\JwtAuth; ///cargamos el helper
use App\Helpers\JwtAuthu;

use App\Deteccion;
use App\Unidades;

class DeteccionesController extends Controller
{
    public function __construct(){
        //aqui le decimos que use el middlewarte de auth en todos los metodos except los que nosotros no queramos
        //en este caso en el metodo index y show
        $this->middleware('api.auth', ['except' => ['index', 'show','getDetByUnidades']]);
        // $this->middleware('api.authu');
    }

    public function index() //MOSTRAMOS TODA LA INFO QUE HAY EN LA TABLA DETECCIONES
    {
        $detecciones = Deteccion::all() ->load('unidades');

        return response() -> json([
            'code'            => 200,
            'status'          => 'success',
            'infoDetecciones' => $detecciones
        ]);
    }

    public function show($id) //mostramos la info x id de la tabla detecciones
    {
        $deteccion = Deteccion::find($id);

        if(is_object($deteccion) && !empty($deteccion))
        {
            $deteccion -> load('unidades');

            $data = [
                'code'          => 200,
                'status'        => 'success',
                'DeteccionById' => $deteccion
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No existe informacion'
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
                'unidad_id'         => 'required', //detalle arreglo de años para todos
              //  'unidad_id'         => 'required|unique:detecciones', //intento quitar unique y poner año unico
                'meta_detecciones'  => 'required|numeric',
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
                // 'total_det'         => 'numeric',
                'ano'               => 'required|numeric'

                //NO PUIEDE SER UNICO PORQUE SOLO ME ACEPTARIA UN AÑO POR UNIDAD SI PONGO 2020 EN DOS UNIDADES NO ME ACEPTARIA
                //PORQUE EL NUMERO DEBE SER UNICO, POR LO TANTO QUEDA SIN UNIQUE PARA QUE ACEPTEW LA INFORMACION, Y MEJOR
                //VALIDARLA EN EL FRONT END SI ESE NUMERO YA LO TIENE QUE MANDE MENSAJE DE QUE YA EXISTE ESE AÑO EN META
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
                $det_info = new Deteccion();
                $det_info -> unidad_id        = $params -> unidad_id;
                $det_info -> meta_detecciones = $params -> meta_detecciones;
                $det_info -> ano              = $params -> ano;


             

                    $det_info -> save();

                    $data = [
                        'code'             => 200,
                        'status'           => 'success',
                        'detInfo'          => $det_info
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
        //recogemos los datos por post
        $json = $request -> input('json', null);
        $params_array =  json_decode($json, true);

        //Datos para devolver
        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'No se ah Actualizado la Información'
        );

         //usamos un if para verificar si los datos enviados no van vacios
         if(!empty($params_array))
         {

            // $total_deteccion = Deteccion::sum('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre');
            // $params_array_total = json_decode($total_deteccion, true);
            
            //validamos los datos enviados
             $validate = \Validator::make($params_array, [
                 'unidad_id'         => 'required',
                 'meta_detecciones'  => 'numeric',
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
                  'total_det'         =>  'numeric',
                  'ano'               => 'numeric'
             ]);

             if($validate->fails())
             {
                 $data['errors'] = $validate -> errors();
                 return response() -> json($data, $data['code']);
             }
 
             //eliminamos los datos que no deseamos actualizar
             //unset($params_array['ano']); ejemplo

            //  $stats = Deteccion::raw("enero + febrero + marzo + abril + mayo + junio");

 
             $where = [
                 'id' => $id,
             ];
 
             $det_info_update = Deteccion::updateOrCreate($where, $params_array);
            //  $total_det_update = Deteccion::updateOrCreate($where, $stats);
 
             //devolvemos los datos correctos
             $data = array(
                 'code'        => 200,
                 'status'      => 'success',
                 'infoupdate' => $params_array
             );
         }
 
         return response() -> json($data, $data['code']);
    }

    public function destroy(Request $request, $id) //metodo para eliminar un registro
    {
        $det_delete = Deteccion::where('id', $id) -> first();

        if(!empty($det_delete))
        {
            $det_delete -> delete();

            $data = [
                'code'       => 200,
                'status'     => 'success',
                'DET_DELETE' => $det_delete
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

    public function getDetByUnidades($id) //obtener los datos por unidad 
    {
        $detxunidad = Deteccion::where('unidad_id', $id) -> get() -> load('unidades');

        if(sizeof($detxunidad))
        {
            $data =[
                'code'           => 200,
                'status'         => 'success',
                'detxunidad' => $detxunidad
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
