<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\JwtAuth;

use App\Admin;

class AdminController extends Controller
{
    public function pruebas(Request $request) {
        return "Accion de Pruebas de AdminController";
    }

    public function register(Request $request)
    {
        //RECOGEMOS DATOS DEL USUARIO
        $json = $request -> input('json', null);
        
        //DECODIFICAMOS JSON
        $params       = json_decode($json); //cramos un objeto de lso datos esto nos crea un objeto
        $params_array = json_decode($json, true); //obtenemos un array

        //COMPROBAMOS QUE SE ENVIEN DATOS
        if(!empty($params) && !empty($params_array))
        {
            $params_array = array_map('trim', $params_array); //limpiamos los espacios de los datos para que no den error

            //VALIDAMOS DATOS CON LIBRERIA PROPIA DE LARAVEL VALIDATOR
            $validate =\Validator::make($params_array,
            [
                'name'     => 'required|alpha',
                'lastname' => 'required|alpha',
                'email'    => 'required|email|unique:admins',
                'password' => 'required'
            ]);
            if($validate->fails())
            {
                $data = array
                (
                    'satus'   => 'error',
                    'code'    => 404,
                    'message' => 'El usuario no se ah creado',
                    'errors'  => $validate->errors()   
                );
            }else{ //si la validacion es correcta
                //ciframos la contraseña
                $pwd = hash('sha256', $params->password);

                //creamos nuevo administrador
                $user = new Admin();
                $user -> name = $params_array['name'];
                $user -> lastname = $params_array['lastname'];
                $user -> email    = $params_array['email'];
                $user -> password = $pwd;
                $user -> save(); //guardamos al usaurio en la bd

                $data = array
                (
                    'status' => 'success',
                    'code'   =>  200,
                    'message'=> 'El usuario se ah creado correctamente',
                    'USER'   =>  $user
                );
            }

        }else{ //si no se envian bien los datos del formulario entraa aqui
            $data = array
            (
                'satus'   => 'error',
                'code'    => 404,
                'message' => 'Los datos enviados no son correctos',    
            );
        }

        //enviamos la respuesta en json y convertimos el array en json
        return response()->json($data, $data['code']);
         


    }

    public function login(Request $request)
    {  
        //return "Accion de login de usuarios";
        $jwtAuth = new \JwtAuth();

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

    public function update(Request $request)
    { //ACTUALIZAMOS AL INFORMACION DEL USUARIO
        
        //COMPROBAR SI EL USUARIO ESTA IDENTIFICADO
        
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        //var_dump($checkToken);die();
        //RECOGER LOS DATOS POR PÓST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true); //aqui estaba el error tenia comillas
       // var_dump($params_array); die();




        if($checkToken && !empty($params_array)){
            //ACTUALIZAR EL USUARIO
 
               

             //SACAR USUARIO IDENTIFICADO
             $user = $jwtAuth->checkToken($token, true);
            //var_dump($user); die(); //hasta aqui todo bien devuelve al usuario dientificado
              //VALIDAR LOS DATOS
               $validate = \Validator::make($params_array, [
                     'name' => 'required|alpha',
                     'lastname' => 'required|alpha',
                     'email' => 'required|email|unique:admins,'.$user->sub //id del usuario unico del usuario
     
               ]);
 
            //QUITAR LOS CAMPOS QUE NO SE ACTUALIZARAN 
                 unset($params_array['id']);
                 unset($params_array['password']);
                 unset($params_array['created_at']);
                 unset($params_array['remember_token']);
            //ACTUALIZAR EL USUARIO EN LA BASE DE DATOS
                 $user_update = Admin::where('id', $user->sub)->update($params_array);//buscamos en el modeloo admin donde el id sea el mismo que el user id  y se actualiza en la bd
               
                    

            //DEVOLVER ARRAY CON EL RESULTADO
                 $data = array(
                 'code'    =>  200,
                 'status'  => 'success',
                 'user'    => $user,
                 'changes' => $params_array,
                 );
 
         }else{
             $data = array(
                 'code'    =>  400,
                 'status'  => 'error',
                 'message' => 'El usuario no esta identificado' 
             );
         }
 
         return response()->json($data, $data['code']);


        /*if($checkToken)
        {
            echo "<h1>Login correcto</h1>";
        }else{
            echo "<h1>Login Incorrecto</h1>";
        }

    die();        */

    }

    public function upload(Request $request)
    { //GUARDAMOS LA IMAGEN DEL USUARIO CHECAR VIDEO CUALQUIER DUDA
        
        //RECOGER DATOS DE LA PETICION
        $image = $request->file('file0');


        //VALIDACION DE IMAGEN
        $validate = \Validator::make($request->all(),[
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'

        ]);

        //SUBIR Y GUARDAR IMAGEN
        if(!$image || $validate->fails()){
            
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir imagen'
            );
            
        }else{
            $image_name = time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            );

           

        }
        return response()->json($data, $data['code']);
    }

    public function getImage($filename){ //OBTENEMOS LA IMAGEN DEL USUARIO

       // $path = storage_path('app\\users\\' . $filename);
        $isset =\Storage::disk('users')->exists($filename);

        if($isset){


            $file = \Storage::disk('users')->get($filename);
            //$type = \File::mimeType($path);
            return new Response($file, 200);
        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'La imagen no existe'
            ); 
            return response()->json($data, $data['code']);
        }
    }

    public function profile($id){ //DEVOLVEMOS LA INFORMACION DEL USUARIO
        
        $user = Admin::find($id); //buscamos el usuario por el id DEL MODEL ADMIN QUE ESTA TIENE LA TABALA ADMINS

        if(is_object($user)){

            //ocultamos el campo de ocntraseña
            unset($user['password']);

            $data = array(
                'code'   => 200,
                'status' => 'successs',
                'user'   => $user
            );
        }else{
            $data = array(
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'El usuario no existe' 
            );
        }

        return response()->json($data, $data['code']);

    }

}
