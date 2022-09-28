<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Responsables;

class RespController extends Controller
{
    public function __construct()
    {
        //aqui le decimos que use el middlewarte de auth en todos los metodos except los que nosotros no queramos
        //en este caso en el metodo index y show
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $responsables = Responsables::all();

        return response() -> json([
            'code' => 200,
            'status' => 'success',
            'responsables' => $responsables
        ]);
    }

    public function show($id)
    {
        $responsable = Responsables::find($id);

        if(is_object($responsable))
        {
            $data = [
                'code' => 200,
                'status' => 'success',
                'responsable' => $responsable
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No existe Registro de Responsable'
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
                'email' => 'required|email|unique:responsables',
                'password' => 'required',
                'programa' => 'required|unique:responsables'
            ]);

            if($validate->fails())
            {
                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'El registro no se ah guardado correctamenta',
                    'errors' => $validate->errors()
                    
                ];
            }else{
                $pwd = hash('sha256', $params->password);

                $responsable = new Responsables();
                $responsable->name = $params_array['name'];
                $responsable->email = $params_array['email'];
                $responsable->password = $pwd;
                $responsable->programa = $params_array['programa'];

                $responsable->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'el registro se ah creado correctamente',
                    'responsable' => $responsable
                ];
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'El registro no se ah realizado correctamente'
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
                'email' => 'required|email|unique:responsables',
                'password' => 'required',
                'programa' => 'required|unique:responsables'
            ]);

            unset($params_array['id']);
            unset($params_array['created_at']);
            unset($params_array['password']);

            //actualizamos

            $responsable = Responsables::where('id', $id)->update($params_array);

            $data = [
                'code' => 200,
                'status' => 'success',
                'responsable' => $params_array
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
        $responsable_delete = Responsables::find($id);

        if(!empty($responsable_delete))
        {
            $responsable_delete->delete();

            $data =[
                'code' => 200,
                'status' => 'success',
                'message' => 'Se ah eliminado el registro correctamente',
                'responsable_delete' => $responsable_delete
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ah eliminado el registro',
                'responsable_delete' => $responsable_delete

            ];
        }
        return response()->json($data, $data['code']);
    }
}
