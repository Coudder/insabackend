<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Iluminate\Http\Response;

use App\Coordinadores;


class CoordController extends Controller
{
    public function __construct()
    {
        //aqui le decimos que use el middlewarte de auth en todos los metodos except los que nosotros no queramos
        //en este caso en el metodo index y show
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $coordinadores = Coordinadores::all();

        if(!empty($coordinadores))
        {
            $data = [
                'code' => 200,
                'status' => 'success',
                'coordinadores' => $coordinadores
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No existe información disponible'
            ];    
        }
        return response()->json($data, $data['code']);
    }

    public function show($id)
    {
        $coordinador = Coordinadores::find($id);

        if(is_object($coordinador))
        {
            $data = [
                'code' => 200,
                'status' => 'success',
                'coordinador' => $coordinador
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No existe Registro'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function store(Request $request)
    {
        $json = $request -> input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if(!empty($params_array))
        {
            $validate = \Validator::make($params_array,[
                'name' => 'required',
                'email' => 'required|email|unique:coordinadores',
                'password' => 'required',
                'coordinacion' => 'required|unique:coordinadores'
            ]);

            if($validate->fails())
            {
                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'El registro no se ah guardado correctamente',
                    'errors' => $validate->errors()
                    
                ];
            }else{
                $pwd = hash('sha256', $params->password);

                $coordinador = new Coordinadores();
                $coordinador->name = $params_array['name'];
                $coordinador->email = $params_array['email'];
                $coordinador->password = $pwd;
                $coordinador->coordinacion = $params_array['coordinacion'];

                $coordinador->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'el registro se ah guardado correctamente',
                    'coordinador' => $coordinador
                ];
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'El registro no se ah guardado correctamente'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array))
        {
            $validate = \Validator::make($params_array, [
                'name' => 'required',
                'email' => 'required|email|unique:coordinadores',
                'password' => 'required',
                'coordinacion' => 'required|unique:coordinadores'
            ]);

            unset($params_array['id']);
            unset($params_array['created_at']);
            unset($params_array['password']);

            //actualizamos

            $responsable = Coordinadores::where('id', $id)->update($params_array);

            $data = [
                'code' => 200,
                'status' => 'success',
                'coordinador' => $params_array
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ah actualizado el registro correctamente'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request)
    {
        $coordinador_delete = Coordinadores::find($id);

        if(!empty($coordinador_delete))
        {
            $coordinador_delete->delete();

            $data =[
                'code' => 200,
                'status' => 'success',
                'message' => 'Se ah eliminado el registro correctamente',
                'responsable_delete' => $coordinador_delete
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ah eliminado el registro',
                'responsable_delete' => $coordinador_delete

            ];
        }
        return response()->json($data, $data['code']);
    }
}   

