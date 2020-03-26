<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Unidades;

class UnidadesController extends Controller
{
    public function __construct()
    {
        //aqui le decimos que use el middlewarte de auth en todos los metodos except los que nosotros no queramos
        //en este caso en el metodo index y show
        $this->middleware('api.auth', ['except' => ['index', 'show', 'loginUnidad']]);
    }

    public function index() //LISTAR TODAS LAS UNIDADES QUE TENEMOS
    {   
        $unidades = Unidades::all();

        return response()-> json([
            'code'     => 200,
            'status'   => 'success', 
            'unidades' => $unidades,
        ]);
    }

    public function show($id)//aqui creamos metodo para obtener solo una unidad de salud por su id
    { 
        $unidad = Unidades::find($id);

        //comprobamnos si es un objeto
        if(is_object($unidad)){
            $data = [
                'code' => 200,
                'status' => 'success',
                'unidad' => $unidad
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No existe la Unidad de Salud'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function store(Request $request)//guardamos nuevas unidades
    { 
     
            //RECOGEMOS LOS DATOS POR POST
        $json = $request->input('json', null);
        $params       = json_decode($json);
        $params_array = json_decode($json, true);


             //comprobamos si no va vacio los datos
       if(!empty($params_array)) {

            //VALIDAR LOS DATOS
        $validate = \Validator::make($params_array,[
            'nombre_unidad'   => 'required|unique:unidades',
            'municipio'       => 'required',
            'clues'           => 'required|unique:unidades',
            'nom_responsable' => 'required',
            'email'           => 'required|email|unique:unidades',
            'password'        => 'required'
        ]);

            //COMPROBAMOS QUE LA INFORMACION SEA CORRECTA
        if($validate->fails()){
            $data = [
                'status'   => 'error',
                'code'    => 404,
                'message' => 'La unidad no se ah guardado',
                'errors'  => $validate->errors()
            ];
        }else{ 
                     //si los datos son correctos

                    //ciframos la contraseña
                    $pwd = hash('sha256', $params->password);

                    //GUARDAR LA UNIDAD

                    $unidad = new Unidades();
                    $unidad->nombre_unidad = $params_array['nombre_unidad'];
                    $unidad->municipio = $params_array['municipio'];
                    $unidad->clues = $params_array['clues'];
                    $unidad->nom_responsable = $params_array['nom_responsable'];
                    $unidad->email = $params_array['email'];
                    $unidad->password = $pwd;

                    //guardamos en la base de datos unidades
                    $unidad->save();

                    $data = [
                        'status'   => 'success',
                        'code'    => 200,
                        'message' => 'La unidad se ah creado correctamente',
                        'UNIDAD'    => $unidad
                        
                    ];    
            }
        }else{
        $data = [
            'satus'   => 'error',
            'code'    => 404,
            'message' => 'No has enviado ninguna Unidad de Salud',
            
        ];
        }
      //DEVOOLVER EL RESULTADO
        return response()->json($data, $data['code']);


    }

///////////////////////////////////////////////////////////////////////////////////////////////
    public function update($id, Request $request)//metodo para actualizar datos de unidades
     { 

        //recoger datos que vengan por post
         $json = $request->input('json', null);
         $params_array = json_decode($json, true);

         if(!empty($params_array)){

             //validar los datos
            $validate = \Validator::make($params_array, [
                'nombre_unidad'   => 'required|unique:unidades',
                'municipio'       => 'required',
                'clues'           => 'required|unique:unidades',
                'nom_responsable' => 'required',
                'email'           => 'required|email|unique:unidades',
                'password'        => 'required'
            ]);

            //quitar lo que no se quiere actualizar
                unset($params_array['id']);
                unset($params_array['created_at']);
                unset($params_array['password']);
            
                //actualizar en la base de datros
                $unidad = Unidades::where('id', $id)->update($params_array);
            
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'unidad' => $params_array
                ];
         }else{
             $data = [
                 'code' => 400,
                 'status' => 'error',
                 'message' => 'Los datos de la unidad no se han Actualizado'
             ];
         }
        //devolver los datos
        return response()->json($data, $data['code']);

     }

    public function destroy($id, Request $request)
    {//eliminar unidad registrada

        //conseguir  el registro
        $unidad_delete = Unidades::find($id);
        

        if(!empty($unidad_delete)){
        //borrar el registro
    
        $unidad_delete->delete();
        //devolver algo
        $data = [
            'code' => 200,
            'status' => 'success',
            //'message' => 'Se ah eliminado correctamente la meta',
            'unidad_delete' => $unidad_delete
         ];
     }else{
        $data =[
        'code' => 400,
        'status' => 'error',
        'message' => 'No se ah podido eliminar la entrada',
        'meta_delete' => $unidad_delete
        ];    
         }   
        return response()->json($data, $data['code']);
 

    }


    //intento de loign por unidad

    public function loginUnidad(Request $request)
    {  
        //return "Accion de login de usuarios";
        $jwtAuth = new \JwtAuthu();

        //RECIBIR DATOS POR POST
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);
        
        //VALIDAR DATOS 
          $validate = \Validator::make($params_array, 
          [
              'email'    => 'required|email', 
              'password' => 'required'
          ]);
        
          //*comprobar si los datos son validos realmente
          if($validate->fails()){ //si alguno de los datos es incorrecto manda lo siguienre
                  //VALIDACION AH FALLADO
                  $signup = array(
                      'status'   => 'error',
                      'code'    => 404,
                      'message' => 'El usuario no se ah podido identificar',
                      'errors'  => $validate->errors()
                  );
          }else{
              //CIFRAR LA PASSWORD
              $pwd = hash('sha256', $params->password);
              //DEVOLVER EL TOKEN O DATOS
              $signup = $jwtAuth->signup($params->email, $pwd);

              //var_dump($signup); die();

              if(!empty($params->gettoken)){
                  $signup = $jwtAuth->signup($params->email, $pwd, true);
              }
          }

                return response()->json($signup, 200);
    }



}
