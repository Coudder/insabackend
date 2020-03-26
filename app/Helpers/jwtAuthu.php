<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Iluminate\Support\Facades\DB;
use App\Unidades ;

    ///ESTER SE CREO PARA EL LOGIN DE USUARIOS DE UNIDADES

class JwtAuthu{

    public $key;

    public function __construct(){
        $this->key = 'esto_es_una_clave_super_secreta-10101988';
    }

    public function signup($email, $password, $getToken = null){

        //Buscar si existe el usuario con sus credenciales
        $useru = Unidades::where([
                'email'    => $email,
                'password' => $password
        ])->first(); //->aqui bucamos dentro de la tabla admins si existe el email y contraseña y le decimos que nos muestre solo un objeto

        //Comprobar si son correctas las credenciaces(objeto)   
        $signup = false;
        if(is_object($useru)){
            $signup = true;
        }
        
        //Generar Token con datos del usuario identificado
        if($signup){

            $token = array(
                'sub'      => $useru->id,
                'email'    => $useru->email,
                'name'     => $useru->nom_responsable,
                'municipio' => $useru->municipio,
                'clues'    => $useru->clues,
                'unidad'=> $useru->nombre_unidad,
                'iat'      => time(),
                'exp'      => time() + (7 * 24 * 60 * 60)

            );
            $jwt = JWT::encode($token, $this->key, 'HS256');

            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
              //Devolver los datos decodificados o el token en funcion params
               if(is_null($getToken)){
                    $data = $jwt;
               } else{
                    $data = $decoded;
               }
        }else{
            $data = array(
                'status'  => 'error',
                'message' => 'Login incorrecto',
            );
        }

        return $data;


    }

    public function checkTokenUnidad($jwt, $getIdentity = false){
        $auth = false; //por default auth siempre va en falso

        try{    //cramos un try catrch para los errores comunes
            $jwt = str_replace('"', '', $jwt); //aqui quitamos las comillas del token por si se van
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        }catch(\UnexpectedValueException $e){
            $auth = false;
        }catch(\DomainException $e){
            $auth = false;
        }
        //si decoded no esta vacio y es un objeto y a la vez tiene su id entonces auth pasa a true
        if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){

            $auth = true;

        }else{
            $auth = false; //de lo contrario se vuelve false
        }

        if($getIdentity){ //si recibe el get identity entonces manda los decoded
            return  $decoded;
        }


        return $auth;

    }

    
}