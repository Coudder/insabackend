<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthuMiddleware
{
    /** MIDDLEWARE PARA LOS USUARIOS DE UNIDADES
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //COMPROBAR SI EL USUARIO ESTA IDENTIFICADO
        
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuthu();
        $checkToken = $jwtAuth->checkTokenUnidad($token);

        if($checkToken){
            return $next($request);

        }else{
            $data = array(
                'code'    =>  400,
                'status'  => 'error',
                'message' => 'El usuario no esta identificado' 
            );
            return response()->json($data, $data['code']);
        }
    }
}
